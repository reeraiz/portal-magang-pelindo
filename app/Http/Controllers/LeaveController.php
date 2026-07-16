<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    /**
     * Tampilkan halaman pengajuan izin & riwayat untuk Intern.
     */
    public function index()
    {
        $leaves = LeaveRequest::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $totalLeaves = LeaveRequest::where('user_id', Auth::id())->count();
        $pendingLeaves = LeaveRequest::where('user_id', Auth::id())->where('status', 'pending')->count();
        $approvedLeaves = LeaveRequest::where('user_id', Auth::id())->where('status', 'approved')->count();

        return view('intern.leaves', compact('leaves', 'totalLeaves', 'pendingLeaves', 'approvedLeaves'));
    }

    /**
     * Simpan pengajuan izin/sakit baru (Intern).
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:izin,sakit,cuti',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
        }

        LeaveRequest::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('status', 'Pengajuan '.strtoupper($request->type).' berhasil dikirim dan menunggu persetujuan.');
    }

    /**
     * Tampilkan halaman verifikasi izin untuk Admin & Pembimbing.
     */
    public function adminIndex(Request $request)
    {
        $query = LeaveRequest::with('user');
        $this->applyMentorScope($query);

        if ($request->filled('filter_date')) {
            $date = $request->filter_date;
            $query->where(function ($q) use ($date) {
                $q->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date);
            });
        }

        if ($request->filled('filter_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->filter_name.'%');
            });
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $totalQuery = LeaveRequest::query();
        $this->applyMentorScope($totalQuery);
        $totalLeaves = (clone $totalQuery)->count();
        $pendingReviews = (clone $totalQuery)->where('status', 'pending')->count();
        $approvedLeaves = (clone $totalQuery)->where('status', 'approved')->count();

        return view('admin.leaves', compact('leaves', 'totalLeaves', 'pendingReviews', 'approvedLeaves'));
    }

    /**
     * Verifikasi (setujui/tolak) pengajuan izin (Admin & Pembimbing).
     */
    public function verify(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $query = LeaveRequest::where('id', $id);
        $this->applyMentorScope($query);
        $leave = $query->firstOrFail();

        $leave->status = $request->status;
        $leave->admin_note = $request->admin_note;
        $leave->save();

        if ($request->status === 'approved') {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                Attendance::updateOrCreate(
                    [
                        'user_id' => $leave->user_id,
                        'date' => $date->toDateString(),
                    ],
                    [
                        'status' => 'izin',
                        'notes' => '['.strtoupper($leave->type).'] '.$leave->notes,
                        'attachment' => $leave->attachment,
                    ]
                );
            }
        }

        return redirect()->back()->with('status', 'Status pengajuan izin berhasil diupdate.');
    }

    /**
     * Helper privat untuk membatasi akses pembimbing hanya pada intern bimbingannya.
     */
    private function applyMentorScope($query)
    {
        if (Auth::check() && Auth::user()->role === 'pembimbing') {
            $query->whereHas('user', function ($q) {
                $q->where('mentor_id', Auth::id());
            });
        }
    }
}
