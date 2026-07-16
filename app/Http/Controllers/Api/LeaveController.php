<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    /**
     * Ambil riwayat pengajuan izin intern via API.
     */
    public function index(Request $request)
    {
        $leaves = LeaveRequest::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $leaves,
        ]);
    }

    /**
     * Simpan pengajuan izin via API.
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

        $leave = LeaveRequest::create([
            'user_id' => $request->user()->id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Leave request submitted successfully',
            'data' => $leave,
        ], 201);
    }

    /**
     * Verifikasi pengajuan izin via API (Admin / Mentor).
     */
    public function verify(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $query = LeaveRequest::where('id', $id);
        if ($request->user()->role === 'pembimbing') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('mentor_id', $request->user()->id);
            });
        }
        $leave = $query->firstOrFail();

        $leave->status = $request->status;
        $leave->admin_note = $request->admin_note;
        $leave->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Leave request verified successfully',
            'data' => $leave,
        ]);
    }
}
