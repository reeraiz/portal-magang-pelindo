<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Logbook;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InternController extends Controller
{
    /**
     * Tampilkan halaman dashboard absensi intern.
     */
    public function absensi()
    {
        $userId = Auth::id();
        $todayAttendance = Attendance::where('user_id', $userId)
            ->whereDate('date', Carbon::today('Asia/Makassar'))
            ->first();

        // Fetch recent activities (mix of attendances and logbooks)
        $recentAttendances = Attendance::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentLogbooks = Logbook::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentActivities = $recentAttendances->concat($recentLogbooks)
            ->sortByDesc('created_at')
            ->take(5);

        // Milestone Progress Calculation
        $user = Auth::user();
        $progress = 0;
        $totalDays = 0;
        $daysAttended = 0;
        $remainingDays = 0;

        if ($user->internship_start_date && $user->internship_end_date) {
            $start = Carbon::parse($user->internship_start_date, 'Asia/Makassar');
            $end = Carbon::parse($user->internship_end_date, 'Asia/Makassar');

            $totalDays = $start->diffInDays($end);

            // Count actual days the intern has checked in
            $daysAttended = Attendance::where('user_id', $user->id)
                ->whereNotNull('check_in')
                ->count();

            $remainingDays = max(0, $totalDays - $daysAttended);
            $progress = $totalDays > 0 ? min(100, round(($daysAttended / $totalDays) * 100)) : 0;
        }

        return view('intern.absensi', compact('todayAttendance', 'recentActivities', 'progress', 'totalDays', 'daysAttended', 'remainingDays', 'user'));
    }

    /**
     * Simpan data check-in atau check-out absensi dengan waktu server WITA (Makassar).
     */
    public function storeAbsensi(Request $request)
    {
        $request->validate([
            'type' => 'required|in:check_in,check_out',
        ]);

        $type = $request->type;
        // Gunakan waktu server WITA (Makassar) secara mutlak untuk keamanan dan mencegah timestamp tampering (VULN-01)
        $now = Carbon::now('Asia/Makassar');

        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', $now->toDateString())
            ->first();

        if (! $attendance) {
            $attendance = new Attendance;
            $attendance->user_id = Auth::id();
            $attendance->date = $now->toDateString();
        }

        // Jangan izinkan absen jika sudah mengajukan izin/sakit
        if (in_array($attendance->status, ['pending', 'izin', 'sakit'])) {
            return redirect()->back()->withErrors(['error' => 'Anda sudah mengajukan izin/sakit hari ini, tidak dapat melakukan absensi.']);
        }

        if ($type === 'check_in' && ! $attendance->check_in) {
            $attendance->check_in = $now->format('H:i:s');
            $attendance->status = 'verified';
            $attendance->location = $request->location ?? 'WFO - Kantor Pusat';

            // Determine punctuality based on check-in time (WITA - Makassar) & Day of Week
            $isFriday = $now->isFriday();
            $targetMasukHour = $isFriday ? 7 : 8;
            $targetMasukMinute = $isFriday ? 30 : 0;
            $targetMasukStr = $isFriday ? '07:30:00' : '08:00:00';

            $jamMasuk = Carbon::createFromTime($targetMasukHour, $targetMasukMinute, 0, 'Asia/Makassar');
            if ($now->format('H:i:s') <= $targetMasukStr) {
                $attendance->notes = 'Tepat Waktu';
            } else {
                $selisih = (int) round(abs($now->diffInMinutes($jamMasuk)));
                $attendance->notes = 'Terlambat '.$selisih.' menit';
            }
        } elseif ($type === 'check_out' && $attendance->check_in && ! $attendance->check_out) {
            $attendance->check_out = $now->format('H:i:s');
            $isFriday = $now->isFriday();
            $targetPulangStr = $isFriday ? '16:30:00' : '17:00:00';
            if ($now->format('H:i:s') < $targetPulangStr) {
                $attendance->notes = 'Terlalu Cepat Pulang';
            }
        }

        $attendance->save();

        return redirect()->back()->with('status', 'Absensi berhasil disimpan!');
    }

    /**
     * Simpan pengajuan izin/sakit.
     */
    public function storeIzin(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|in:izin,sakit',
            'notes' => 'required|string|max:1000',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $now = Carbon::now('Asia/Makassar');

        if ($request->reason === 'izin') {
            $usedIzinDays = Attendance::where('user_id', Auth::id())
                ->whereIn('status', ['pending', 'verified', 'izin'])
                ->where(function ($q) {
                    $q->where('notes', 'like', '%[IZIN]%')->orWhere('notes', 'like', '%[CUTI]%');
                })
                ->whereMonth('date', $now->month)
                ->whereYear('date', $now->year)
                ->count();

            $maxQuotaPerMonth = 3;
            if ($usedIzinDays >= $maxQuotaPerMonth) {
                return back()->withErrors([
                    'reason' => 'Gagal: Kuota izin harian Anda bulan ini telah habis (Maksimal '.$maxQuotaPerMonth.' hari/bulan). Jika Anda sakit, pilih opsi SAKIT dan lampirkan surat dokter.'
                ])->withInput();
            }
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('izin-attachments', 'public');
        }

        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', $now->toDateString())
            ->first();

        if (! $attendance) {
            $attendance = new Attendance;
            $attendance->user_id = Auth::id();
            $attendance->date = $now->toDateString();
        }

        $attendance->status = 'pending';
        $attendance->check_in = null;
        $attendance->check_out = null;
        $attendance->notes = '['.strtoupper($request->reason).'] '.$request->notes;
        $attendance->attachment = $attachmentPath;
        $attendance->save();

        return redirect()->back()->with('status', 'Pengajuan '.$request->reason.' berhasil dikirim dan menunggu persetujuan Admin/Pembimbing.');
    }

    /**
     * Tampilkan riwayat logbook intern.
     */
    public function logbook()
    {
        $logbooks = Logbook::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('intern.logbook', compact('logbooks'));
    }

    /**
     * Simpan logbook aktivitas harian baru.
     */
    public function storeLogbook(Request $request)
    {
        $minDate = Auth::user()->internship_start_date ? Carbon::parse(Auth::user()->internship_start_date)->toDateString() : '2020-01-01';

        $request->validate([
            'date' => 'required|date|before_or_equal:today|after_or_equal:'.$minDate,
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'attachments.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $paths = $this->handleFileUploads($request);
        $firstAttachment = ! empty($paths) ? $paths[0] : null;
        $attachmentsJson = ! empty($paths) ? json_encode($paths) : null;

        Logbook::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'time' => Carbon::now('Asia/Makassar')->format('H:i'),
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
            'attachment' => $firstAttachment,
            'attachments' => $attachmentsJson,
        ]);

        return redirect()->back()->with('status', 'Logbook berhasil disimpan!');
    }

    /**
     * Perbarui data logbook yang masih berstatus pending.
     */
    public function updateLogbook(Request $request, $id)
    {
        $minDate = Auth::user()->internship_start_date ? Carbon::parse(Auth::user()->internship_start_date)->toDateString() : '2020-01-01';

        $request->validate([
            'date' => 'required|date|before_or_equal:today|after_or_equal:'.$minDate,
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'attachments.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'delete_photos' => 'nullable|array',
            'delete_photos.*' => 'string',
        ]);

        $logbook = Logbook::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($logbook->status === 'verified') {
            return redirect()->back()->withErrors(['error' => 'Logbook yang sudah disetujui tidak dapat diedit.']);
        }

        $currentPaths = $logbook->attachments_list;

        // 1. Hapus foto lama yang dipilih
        if ($request->has('delete_photos') && is_array($request->delete_photos)) {
            foreach ($request->delete_photos as $pathToDelete) {
                if (in_array($pathToDelete, $currentPaths)) {
                    Storage::disk('public')->delete($pathToDelete);
                    $currentPaths = array_values(array_diff($currentPaths, [$pathToDelete]));
                }
            }
        }

        // 2. Tambahkan foto baru jika ada
        $newPaths = $this->handleFileUploads($request);
        $currentPaths = array_merge($currentPaths, $newPaths);

        $data = [
            'date' => $request->date,
            'title' => $request->title,
            'description' => $request->description,
            'attachment' => ! empty($currentPaths) ? $currentPaths[0] : null,
            'attachments' => ! empty($currentPaths) ? json_encode(array_values($currentPaths)) : null,
        ];

        $logbook->update($data);

        return redirect()->back()->with('status', 'Logbook berhasil diperbarui!');
    }

    /**
     * Hapus logbook yang belum diverifikasi.
     */
    public function destroyLogbook($id)
    {
        $logbook = Logbook::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($logbook->status === 'verified') {
            return redirect()->back()->withErrors(['error' => 'Logbook yang sudah disetujui tidak dapat dihapus.']);
        }

        foreach ($logbook->attachments_list as $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $logbook->delete();

        return redirect()->back()->with('status', 'Logbook berhasil dihapus!');
    }

    /**
     * Tampilkan halaman cetak logbook.
     */
    public function printLogbook(Request $request)
    {
        $user = Auth::user()->load('mentor');
        $query = Logbook::where('user_id', $user->id);

        if ($request->filled('start_date')) {
            try {
                $startDate = Carbon::parse($request->start_date)->toDateString();
                $query->where('date', '>=', $startDate);
            } catch (\Exception $e) {
                $query->where('date', '>=', $request->start_date);
            }
        }
        if ($request->filled('end_date')) {
            try {
                $endDate = Carbon::parse($request->end_date)->toDateString();
                $query->where('date', '<=', $endDate);
            } catch (\Exception $e) {
                $query->where('date', '<=', $request->end_date);
            }
        }

        $logbooks = $query->orderBy('date', 'asc')->get();

        return view('intern.print', compact('logbooks', 'user'));
    }

    /**
     * Tampilkan halaman cetak rekap absensi.
     */
    public function printAbsensi(Request $request)
    {
        $user = Auth::user()->load('mentor');
        $query = Attendance::where('user_id', $user->id);

        if ($request->filled('start_date')) {
            try {
                $startDate = Carbon::parse($request->start_date)->toDateString();
                $query->where('date', '>=', $startDate);
            } catch (\Exception $e) {
                $query->where('date', '>=', $request->start_date);
            }
        }
        if ($request->filled('end_date')) {
            try {
                $endDate = Carbon::parse($request->end_date)->toDateString();
                $query->where('date', '<=', $endDate);
            } catch (\Exception $e) {
                $query->where('date', '<=', $request->end_date);
            }
        }

        $attendances = $query->orderBy('date', 'asc')->get();

        // Calculate statistics based on the selected period
        $totalHadir = $attendances->where('status', 'verified')->count();
        $totalIzin = $attendances->where('status', 'izin')
            ->filter(function ($att) {
                $notes = strtolower($att->notes ?? '');
                return !str_contains($notes, '[sakit]') &&
                       !str_contains($notes, 'sakit') &&
                       !str_contains($notes, '[cuti]') &&
                       !str_contains($notes, 'cuti');
            })->count();
        $totalSakit = $attendances->where('status', 'izin')
            ->filter(function ($att) {
                $notes = strtolower($att->notes ?? '');
                return str_contains($notes, '[sakit]') || str_contains($notes, 'sakit');
            })->count();
        $totalAlpa = $attendances->where('status', 'alpa')->count();

        return view('intern.print_absensi', compact('attendances', 'user', 'totalHadir', 'totalIzin', 'totalSakit', 'totalAlpa'));
    }

    /**
     * Tampilkan halaman rekap absensi intern.
     */
    public function rekap(Request $request)
    {
        $query = Attendance::where('user_id', Auth::id());

        if ($request->filled('month')) {
            $date = Carbon::parse($request->month.'-01', 'Asia/Makassar');
            $query->whereYear('date', $date->year)->whereMonth('date', $date->month);
        }

        $attendances = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $totalHadir = Attendance::where('user_id', Auth::id())->where('status', 'verified')->count();
        $totalIzin = Attendance::where('user_id', Auth::id())
            ->where('status', 'izin')
            ->where(function ($q) {
                $q->where('notes', 'not like', '%[SAKIT]%')
                    ->where('notes', 'not like', '%sakit%')
                    ->where('notes', 'not like', '%[CUTI]%')
                    ->where('notes', 'not like', '%cuti%');
            })->count();
        $totalSakit = Attendance::where('user_id', Auth::id())
            ->where('status', 'izin')
            ->where(function ($q) {
                $q->where('notes', 'like', '%[SAKIT]%')
                    ->orWhere('notes', 'like', '%sakit%');
            })->count();
        $totalAlpa = Attendance::where('user_id', Auth::id())->where('status', 'alpa')->count();

        return view('intern.rekap', compact('attendances', 'totalHadir', 'totalIzin', 'totalSakit', 'totalAlpa'));
    }

    /**
     * Helper privat untuk mengolah upload file attachment.
     */
    private function handleFileUploads(Request $request): array
    {
        $paths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store('logbook_attachments', 'public');
            }
        } elseif ($request->hasFile('attachment')) {
            $paths[] = $request->file('attachment')->store('logbook_attachments', 'public');
        }

        return $paths;
    }
}
    