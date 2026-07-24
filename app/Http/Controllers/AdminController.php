<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Division;
use App\Models\Logbook;
use App\Models\User;
use App\Models\InternshipType;
use App\Models\EducationLevel;
use App\Models\University;
use App\Models\Gender;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\InternshipCertificateMail;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    /**
     * Helper privat untuk membatasi query hanya pada intern bimbingan jika user adalah pembimbing (VULN-03).
     */
    private function applyMentorScope($query, string $relation = 'user')
    {
        if (auth()->check() && auth()->user()->role === 'pembimbing') {
            $mentor = auth()->user();
            if ($relation === 'self') {
                $query->where('mentor_id', $mentor->id);
            } else {
                $query->whereHas($relation, function ($q) use ($mentor) {
                    $q->where('mentor_id', $mentor->id);
                });
            }
        }

        return $query;
    }

    /**
     * Tampilkan halaman dashboard admin / pembimbing.
     */
    public function dashboard()
    {
        $internQuery = User::where('role', 'intern');
        $this->applyMentorScope($internQuery, 'self');
        $totalInterns = $internQuery->count();

        $activeQuery = Attendance::whereDate('date', Carbon::today('Asia/Makassar'));
        $this->applyMentorScope($activeQuery);
        $activeInterns = $activeQuery->count();

        $pendingAbsensiQuery = Attendance::where('status', 'pending');
        $this->applyMentorScope($pendingAbsensiQuery);
        $pendingAbsensi = $pendingAbsensiQuery->count();

        $recentQuery = Attendance::with('user')->orderBy('created_at', 'desc');
        $this->applyMentorScope($recentQuery);
        $recentActivities = $recentQuery->take(5)->get();

        $logbookQuery = Logbook::query();
        $this->applyMentorScope($logbookQuery);
        $totalLogbooks = (clone $logbookQuery)->count();
        $pendingLogbooks = (clone $logbookQuery)->where('status', 'pending')->count();
        $verifiedLogbooks = (clone $logbookQuery)->where('status', 'verified')->count();

        $today = Carbon::today('Asia/Makassar');
        $nextWeek = Carbon::today('Asia/Makassar')->addDays(7);

        $endingSoonQuery = User::where('role', 'intern')
            ->whereNotNull('internship_end_date')
            ->whereDate('internship_end_date', '>=', $today)
            ->whereDate('internship_end_date', '<=', $nextWeek)
            ->orderBy('internship_end_date', 'asc');
        $this->applyMentorScope($endingSoonQuery, 'self');
        $endingSoonInterns = $endingSoonQuery->get();

        $internsQuery = User::where('role', 'intern')
            ->with('mentor')
            ->withCount(['attendances', 'logbooks'])
            ->orderBy('name', 'asc');
        $this->applyMentorScope($internsQuery, 'self');
        $interns = $internsQuery->get();

        // Peringatan Sering Alpa (> 3 Hari) - 100% Sesuai Data Database
        $lowAttendanceInterns = collect();
        foreach ($interns as $intern) {
            $explicitAlpa = Attendance::where('user_id', $intern->id)->where('status', 'alpa')->count();
            $missingDays = 0;
            $attendanceRate = 100;

            if ($intern->internship_start_date && $intern->internship_end_date) {
                $start = Carbon::parse($intern->internship_start_date, 'Asia/Makassar')->startOfDay();
                $today = Carbon::now('Asia/Makassar')->startOfDay();
                
                if ($today->gt($start)) {
                    $elapsedDays = max(1, $start->diffInDaysFiltered(function (Carbon $date) {
                        return ! $date->isWeekend();
                    }, min($today, Carbon::parse($intern->internship_end_date, 'Asia/Makassar')->endOfDay())));
                    
                    $validAttendances = Attendance::where('user_id', $intern->id)->where(function ($q) {
                        $q->whereNotNull('check_in')->orWhereIn('status', ['verified', 'izin']);
                    })->count();
                    
                    $missingDays = max(0, $elapsedDays - $validAttendances);
                    $attendanceRate = min(100, round(($validAttendances / $elapsedDays) * 100));
                }
            }

            // Alpa sejati = maksimal antara catatan status 'alpa' eksplisit di database & hari kerja yang dilewatkan tanpa keterangan
            $alpaCount = max($explicitAlpa, $missingDays);

            // Tampilkan HANYA jika Alpa > 3 (persis sesuai permintaan user)
            if ($alpaCount > 3) {
                $intern->alpa_count = $alpaCount;
                $intern->attendance_rate = $attendanceRate;
                $lowAttendanceInterns->push($intern);
            }
        }

        // -----------------------------------------------------------------
        // DATA GRAFIK VISUALISASI KINERJA & KEHADIRAN (MURNI DATA DATABASE)
        // -----------------------------------------------------------------
        $divisionsList = Division::pluck('name')->toArray();
        if (empty($divisionsList)) {
            $divisionsList = User::where('role', 'intern')->whereNotNull('division')->pluck('division')->unique()->values()->toArray();
        }
        $chartDivisionLabels = [];
        $chartDivisionLateAvg = [];

        foreach ($divisionsList as $divName) {
            $chartDivisionLabels[] = Str::limit($divName, 22);
            $divUserQuery = User::where('role', 'intern')->where('division', $divName);
            $this->applyMentorScope($divUserQuery, 'self');
            $divUserIds = $divUserQuery->pluck('id');

            if ($divUserIds->isNotEmpty()) {
                $attendancesDiv = Attendance::whereIn('user_id', $divUserIds)
                    ->whereNotNull('check_in')
                    ->get();
                    
                $totalLateMinutes = 0;
                foreach ($attendancesDiv as $att) {
                    $dateObj = Carbon::parse($att->date);
                    $targetTime = $dateObj->isFriday() ? '07:30:00' : '08:00:00';
                    if ($att->check_in > $targetTime) {
                        $diff = Carbon::parse($targetTime)->diffInMinutes(Carbon::parse($att->check_in));
                        $totalLateMinutes += $diff;
                    }
                }
                $avg = $attendancesDiv->count() > 0 ? round($totalLateMinutes / $attendancesDiv->count(), 1) : 0;
                $chartDivisionLateAvg[] = $avg;
            } else {
                $chartDivisionLateAvg[] = 0;
            }
        }

        $chartUnivLabels = [];
        $chartHadirData = [];
        $chartIzinData = [];
        $chartAlpaData = [];

        // Ambil semua universitas yang ada intern aktif di bawah mentor ini
        $universities = \App\Models\University::whereHas('users', function ($q) {
            $q->where('role', 'intern');
            $this->applyMentorScope($q, 'self');
        })->get();

        foreach ($universities as $univ) {
            $chartUnivLabels[] = \Illuminate\Support\Str::limit($univ->name, 20);

            $attQuery = Attendance::whereHas('user', function ($q) use ($univ) {
                $q->where('university_id', $univ->id);
                $this->applyMentorScope($q, 'self');
            });

            $hadirCount = (clone $attQuery)->where('status', 'verified')->count();
            $izinCount = (clone $attQuery)->where('status', 'izin')->count();
            $alpaCount = (clone $attQuery)->where('status', 'alpa')->count();

            $chartHadirData[] = $hadirCount;
            $chartIzinData[] = $izinCount;
            $chartAlpaData[] = $alpaCount;
        }

        $recentNotifications = NotificationLog::with('user')->orderBy('created_at', 'desc')->take(6)->get();

        return view('admin.dashboard', compact(
            'totalInterns', 'activeInterns', 'pendingAbsensi', 'recentActivities',
            'totalLogbooks', 'pendingLogbooks', 'verifiedLogbooks', 'endingSoonInterns', 'interns', 'lowAttendanceInterns',
            'chartDivisionLabels', 'chartDivisionLateAvg', 'chartUnivLabels', 'chartHadirData', 'chartIzinData', 'chartAlpaData', 'recentNotifications'
        ));
    }

    /**
     * Tampilkan daftar absensi intern.
     */
    public function absensi(Request $request)
    {
        $query = Attendance::with('user');
        $this->applyMentorScope($query);

        if ($request->filled('filter_date')) {
            $query->whereDate('date', $request->filter_date);
        }
        if ($request->filled('filter_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->filter_name.'%');
            });
        }
        if ($request->filled('filter_division')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('division', $request->filter_division);
            });
        }
        if ($request->filled('filter_department')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department', $request->filter_department);
            });
        }

        $attendances = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $totalHadir = (clone $query)->where('status', 'verified')->count();
        $totalIzin = (clone $query)->where('status', 'izin')->count();
        $totalPending = (clone $query)->where('status', 'pending')->count();

        $divisions = \App\Models\Division::with('departments')->get();
        return view('admin.absensi', compact('attendances', 'totalHadir', 'totalIzin', 'totalPending', 'divisions'));
    }

    /**
     * Tampilkan daftar logbook intern.
     */
    public function logbook(Request $request)
    {
        $query = Logbook::with('user');
        $this->applyMentorScope($query);

        if ($request->filled('filter_date')) {
            $query->whereDate('date', $request->filter_date);
        }
        if ($request->filled('filter_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->filter_name.'%');
            });
        }
        if ($request->filled('filter_division')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('division', $request->filter_division);
            });
        }
        if ($request->filled('filter_department')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department', $request->filter_department);
            });
        }

        $logbooks = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(8)->withQueryString();
        $totalLogbooks = (clone $query)->count();
        $pendingReviews = (clone $query)->where('status', 'pending')->count();
        $approvedLogs = (clone $query)->where('status', 'verified')->count();

        $divisions = \App\Models\Division::with('departments')->get();
        return view('admin.logbook', compact('logbooks', 'totalLogbooks', 'pendingReviews', 'approvedLogs', 'divisions'));
    }

    /**
     * Verifikasi status absensi intern (setujui / tolak).
     */
    public function verifyAbsensi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
        ]);

        $query = Attendance::where('id', $id);
        $this->applyMentorScope($query);
        $attendance = $query->firstOrFail();

        if ($request->status === 'verified') {
            if (str_contains(strtolower($attendance->notes), '[izin]') || str_contains(strtolower($attendance->notes), '[sakit]')) {
                $attendance->status = 'izin';
            } else {
                $attendance->status = 'verified';
            }
        } else {
            $attendance->status = 'rejected';
        }

        $attendance->save();

        return redirect()->back()->with('status', 'Status absensi berhasil diupdate.');
    }

    /**
     * Verifikasi logbook intern (approve / reject & komentar).
     */
    public function verifyLogbook(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $query = Logbook::where('id', $id);
        $this->applyMentorScope($query);
        $logbook = $query->firstOrFail();

        if ($request->action === 'approve') {
            $logbook->status = 'verified';
        } else {
            $logbook->status = 'rejected';
            $logbook->reject_reason = $request->feedback;
        }

        if ($request->filled('feedback')) {
            $logbook->feedback = $request->feedback;
        }

        $logbook->save();

        $message = $request->action === 'approve' ? 'Logbook berhasil disetujui.' : 'Logbook ditolak.';

        return redirect()->back()->with('status', $message);
    }

    /**
     * Tampilkan daftar intern dan pembimbing.
     */
    public function interns(Request $request)
    {
        $search = $request->input('search');
        $shiftFilter = $request->input('shift');

        $query = User::where('role', 'intern')->withCount(['attendances', 'logbooks']);
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('division', 'like', "%{$search}%")
                  ->orWhere('intern_id', 'like', "%{$search}%");
            });
        }

        if ($shiftFilter) {
            if ($shiftFilter === 'pagi') {
                $query->where(function ($q) {
                    $q->where('shift', 'pagi')->orWhereNull('shift');
                });
            } else {
                $query->where('shift', $shiftFilter);
            }
        }
        if ($request->filled('filter_division')) {
            $query->where('division', $request->filter_division);
        }
        if ($request->filled('filter_department')) {
            $query->where('department', $request->filter_department);
        }

        $this->applyMentorScope($query, 'self');
        $interns = $query->orderBy('name')->paginate(10)->withQueryString();

        if (auth()->user()->role === 'admin') {
            $mentors = User::whereIn('role', ['admin', 'pembimbing'])->get();
        } else {
            $mentors = User::where('role', 'pembimbing')->get();
        }
        $divisions = \App\Models\Division::with('departments')->get();
        $internshipTypes = InternshipType::all();
        $educationLevels = EducationLevel::all();
        $universities = University::orderBy('name')->get();
        $genders = Gender::all();
        $faculties = \App\Models\Faculty::orderBy('name')->get();
        $majors = \App\Models\Major::orderBy('name')->get();
        $studyPrograms = \App\Models\StudyProgram::orderBy('name')->get();

        $allInternsQuery = User::where('role', 'intern')
                               ->whereNotNull('internship_end_date')
                               ->whereDate('internship_end_date', '<=', \Carbon\Carbon::today('Asia/Makassar'))
                               ->has('logbooks', '>=', 22)
                               ->orderBy('name');
        $this->applyMentorScope($allInternsQuery, 'self');
        $allInterns = $allInternsQuery->get();

        return view('admin.interns', compact(
            'interns', 'mentors', 'divisions', 'internshipTypes', 'educationLevels', 
            'universities', 'genders', 'faculties', 'majors', 'studyPrograms', 'allInterns'
        ));
    }

    /**
     * Generate CV untuk intern (tanpa periode magang).
     */
    public function generateCv($id)
    {
        $query = User::where('id', $id)->where('role', 'intern')->with(['university', 'gender', 'internshipType', 'educationLevel']);
        $this->applyMentorScope($query, 'self');
        $intern = $query->firstOrFail();

        return view('admin.cv', compact('intern'));
    }

    /**
     * Perbarui data penempatan, masa magang, atau pembimbing intern.
     */
    public function updateIntern(Request $request, $id)
    {
        $request->validate([
            'division' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'internship_start_date' => 'nullable|date',
            'internship_end_date' => 'nullable|date|after_or_equal:internship_start_date',
            'mentor_id' => 'nullable|exists:users,id',
            'shift' => 'nullable|in:pagi,siang,full_day',
        ]);

        $query = User::where('id', $id)->where('role', 'intern');
        $this->applyMentorScope($query, 'self');
        $intern = $query->firstOrFail();

        $intern->update($request->only(['division', 'department', 'internship_start_date', 'internship_end_date', 'mentor_id', 'shift']));

        return redirect()->back()->with('status', 'Data intern berhasil diupdate.');
    }

    /**
     * Update shift intern secara massal.
     */
    public function bulkUpdateShift(Request $request)
    {
        $request->validate([
            'intern_ids' => 'required|array',
            'intern_ids.*' => 'exists:users,id',
            'shift' => 'required|in:pagi,siang,full_day',
        ]);

        $query = User::whereIn('id', $request->intern_ids)->where('role', 'intern');
        $this->applyMentorScope($query, 'self');
        
        $updatedCount = $query->update(['shift' => $request->shift]);

        return redirect()->back()->with('status', $updatedCount . ' intern berhasil diubah ke shift ' . ucfirst($request->shift) . '.');
    }

    /**
     * Perbarui data divisi atau nama pembimbing.
     */
    public function updateMentor(Request $request, $id)
    {
        $request->validate([
            'division' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
        ]);

        $query = User::where('id', $id)->whereIn('role', ['admin', 'pembimbing']);
        if (auth()->user()->role === 'pembimbing' && auth()->id() != $id) {
            abort(403, 'Anda hanya dapat mengubah data profil Anda sendiri.');
        }
        $mentor = $query->firstOrFail();
        $mentor->update($request->only(['division', 'department', 'name']));

        return redirect()->back()->with('status', 'Data pembimbing berhasil diupdate.');
    }

    /**
     * Reset password intern menjadi password acak baru (VULN-02).
     */
    public function resetPassword($id)
    {
        $query = User::where('id', $id);

        if (auth()->user()->role === 'admin') {
            $user = $query->firstOrFail();
        } else {
            $query->where('role', 'intern');
            $this->applyMentorScope($query, 'self');
            $user = $query->firstOrFail();
        }

        $tempPassword = Str::password(8, true, true, true, false);
        $user->update(['password' => Hash::make($tempPassword)]);

        return redirect()->back()->with('status', 'Password '.$user->name.' ('.strtoupper($user->role).') berhasil di-reset menjadi: '.$tempPassword);
    }

    /**
     * Buat user baru (hanya Admin).
     */
    public function storeUser(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya Admin yang dapat membuat user baru.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:intern,pembimbing,admin',
            'division' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'mentor_id' => 'nullable|exists:users,id',
            'internship_start_date' => 'nullable|date',
            'internship_end_date' => 'nullable|date|after_or_equal:internship_start_date',
            'internship_type_id' => 'nullable|exists:internship_types,id',
            'education_level_id' => 'nullable|exists:education_levels,id',
            'university_id' => 'nullable|exists:universities,id',
            'faculty' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'study_program' => 'nullable|string|max:255',
            'gender_id' => 'nullable|exists:genders,id',
            'education_start_year' => 'nullable|digits:4',
            'education_end_year' => 'nullable|digits:4|gte:education_start_year',
            'shift' => 'nullable|in:pagi,siang,full_day',
            'password' => 'nullable|string|min:8',
        ]);

        $password = $request->filled('password') ? $request->password : Str::password(8, true, true, true, false);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'role' => $request->role,
            'division' => $request->division,
            'department' => $request->department,
            'mentor_id' => $request->role === 'intern' ? $request->mentor_id : null,
            'internship_start_date' => $request->role === 'intern' ? $request->internship_start_date : null,
            'internship_end_date' => $request->role === 'intern' ? $request->internship_end_date : null,
            'internship_type_id' => $request->role === 'intern' ? $request->internship_type_id : null,
            'education_level_id' => $request->role === 'intern' ? $request->education_level_id : null,
            'university_id' => $request->role === 'intern' ? $request->university_id : null,
            'faculty' => $request->role === 'intern' ? $request->faculty : null,
            'major' => $request->role === 'intern' ? $request->major : null,
            'study_program' => $request->role === 'intern' ? $request->study_program : null,
            'education_start_year' => $request->role === 'intern' ? $request->education_start_year : null,
            'education_end_year' => $request->role === 'intern' ? $request->education_end_year : null,
            'gender_id' => $request->role === 'intern' ? $request->gender_id : null,
            'shift' => $request->role === 'intern' ? ($request->shift ?? 'pagi') : null,
            'password' => Hash::make($password),
        ]);

        $msg = 'User baru ('.$user->name.' - Role: '.strtoupper($user->role).') berhasil dibuat.';
        if (! $request->filled('password')) {
            $msg .= ' Password acak sementara: '.$password;
        }

        return redirect()->back()->with('status', $msg);
    }

    /**
     * Verifikasi masal logbook intern (setujui semua atau tolak semua).
     */
    public function bulkVerifyLogbook(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve_all,reject_all',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $query = Logbook::where('status', 'pending');
        $this->applyMentorScope($query);

        if ($request->filled('filter_date')) {
            $query->whereDate('date', $request->filter_date);
        }
        if ($request->filled('filter_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->filter_name.'%');
            });
        }

        $pendingLogbooks = $query->get();

        if ($pendingLogbooks->isEmpty()) {
            return redirect()->back()->withErrors(['error' => 'Tidak ada logbook dengan status pending untuk diproses.']);
        }

        foreach ($pendingLogbooks as $logbook) {
            if ($request->action === 'approve_all') {
                $logbook->status = 'verified';
            } else {
                $logbook->status = 'rejected';
                $logbook->reject_reason = $request->feedback;
            }

            if ($request->filled('feedback')) {
                $logbook->feedback = $request->feedback;
            }

            $logbook->save();
        }

        $message = $request->action === 'approve_all' 
            ? 'Semua logbook pending berhasil disetujui.' 
            : 'Semua logbook pending berhasil ditolak.';

        return redirect()->back()->with('status', $message);
    }

    /**
     * Hapus user secara permanen (Hanya Admin).
     */
    public function destroyUser($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya Admin yang dapat menghapus akun.');
        }

        $user = User::findOrFail($id);
        
        // Cek agar admin tidak menghapus dirinya sendiri
        if (auth()->id() == $user->id) {
            return redirect()->back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        $userName = $user->name;
        
        // Hapus data absensi, logbook, dll akan ikut terhapus jika migration menggunakan onDelete('cascade')
        // Namun untuk berjaga-jaga:
        Attendance::where('user_id', $user->id)->delete();
        Logbook::where('user_id', $user->id)->delete();
        \App\Models\LeaveRequest::where('user_id', $user->id)->delete();

        // Kosongkan mentor_id pada anak magang jika yang dihapus adalah pembimbing
        if ($user->role === 'pembimbing') {
            User::where('mentor_id', $user->id)->update(['mentor_id' => null]);
        }

        $user->delete();

        return redirect()->back()->with('status', 'Akun ' . $userName . ' berhasil dihapus secara permanen.');
    }

    /**
     * Review laporan akhir/skripsi intern (Approve/Reject).
     */
    public function reviewSkripsi(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya Admin yang dapat mereview laporan.');
        }

        $request->validate([
            'intern_id' => 'required|exists:users,id',
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|string|max:1000'
        ]);

        $intern = User::where('role', 'intern')->findOrFail($request->intern_id);
        
        $intern->skripsi_status = $request->status;
        $intern->skripsi_rejection_reason = $request->status === 'rejected' ? $request->rejection_reason : null;
        $intern->save();

        $message = $request->status === 'approved' 
            ? 'Laporan Akhir/Skripsi berhasil disetujui.' 
            : 'Laporan Akhir/Skripsi telah ditolak.';

        return redirect()->back()->with('status', $message);
    }

    /**
     * Upload dan kirim sertifikat magang.
     */
    public function sendCertificate(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya Admin yang dapat mengirim sertifikat.');
        }

        $request->validate([
            'intern_id' => 'required|exists:users,id',
            'certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
        ], [
            'certificate.max' => 'Gagal: Ukuran file sertifikat melebihi batas maksimal 5MB.',
            'certificate.mimes' => 'Gagal: Format file harus berupa PDF, JPG, JPEG, atau PNG.',
            'certificate.required' => 'Gagal: Anda belum memilih file sertifikat.',
            'certificate.uploaded' => 'Gagal: File tidak dapat diunggah. Ukuran file kemungkinan melebihi batas maksimal server.',
            'intern_id.required' => 'Gagal: Anda belum memilih Intern penerima sertifikat.'
        ]);

        $intern = User::where('role', 'intern')->findOrFail($request->intern_id);

        // Validasi 1: Masa magang sudah selesai
        if ($intern->internship_end_date && $intern->internship_end_date > now()->format('Y-m-d')) {
            return redirect()->back()->withErrors(['certificate' => 'Gagal: Masa magang peserta belum selesai. Sertifikat hanya dapat dikirim jika periode magang telah berakhir.']);
        }

        // Validasi 2: Logbook minimal 22 hari terisi (dan diverifikasi)
        $verifiedLogbooksCount = $intern->logbooks()->where('status', 'verified')->count();
        if ($verifiedLogbooksCount < 22) {
            return redirect()->back()->withErrors(['certificate' => 'Gagal: Peserta belum memenuhi syarat minimal 22 hari kehadiran/logbook (saat ini: ' . $verifiedLogbooksCount . ' hari).']);
        }

        // Validasi 3: Khusus Magenta & Kemnaker, harus upload skripsi dan disetujui
        if ($intern->internshipType && in_array(strtolower($intern->internshipType->name), ['magenta', 'kemnaker'])) {
            if ($intern->skripsi_status !== 'approved') {
                return redirect()->back()->withErrors(['certificate' => 'Gagal: Peserta program ' . $intern->internshipType->name . ' wajib mengunggah Laporan Akhir/Skripsi dan harus disetujui oleh Admin terlebih dahulu.']);
            }
        }

        $file = $request->file('certificate');
        $fileName = 'Sertifikat_Magang_' . Str::slug($intern->name) . '.' . $file->getClientOriginalExtension();
        
        // Simpan file ke storage lokal untuk lampiran
        $path = $file->storeAs('certificates', $fileName, 'local');
        $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($path);

        // Override SMTP configuration if custom settings exist
        $customMailAddress = \App\Models\Setting::where('key', 'mail_from_address')->value('value');
        $customMailPassword = \App\Models\Setting::where('key', 'mail_password')->value('value');
        
        if ($customMailAddress && $customMailPassword) {
            config([
                'mail.mailers.smtp.username' => $customMailAddress,
                'mail.mailers.smtp.password' => $customMailPassword,
            ]);
        }

        // Kirim email dengan lampiran
        Mail::to($intern->email)->send(new InternshipCertificateMail($intern, $fullPath, $fileName));

        // Hapus file setelah dikirim untuk menghemat space (opsional, tapi baik untuk praktik)
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        return redirect()->back()->with('status', 'Sertifikat berhasil dikirimkan ke email ' . $intern->email);
    }

    /**
     * Simpan pembaruan pengaturan aplikasi.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'mail_from_name' => 'required|string|max:255',
            'mail_from_address' => 'required|email|max:255',
            'mail_password' => 'nullable|string',
        ]);

        Setting::updateOrCreate(
            ['key' => 'mail_from_name'],
            ['value' => $request->mail_from_name]
        );

        Setting::updateOrCreate(
            ['key' => 'mail_from_address'],
            ['value' => $request->mail_from_address]
        );

        if ($request->filled('mail_password')) {
            Setting::updateOrCreate(
                ['key' => 'mail_password'],
                ['value' => $request->mail_password]
            );
        }

        return back()->with('status', 'settings-updated');
    }

    /**
     * Ekspor data absensi intern ke format CSV / Excel (.xlsx compatible).
     */
    public function exportAbsensi(Request $request)
    {
        $query = Attendance::with('user');
        $this->applyMentorScope($query);

        if ($request->filled('filter_date')) {
            $query->whereDate('date', $request->filter_date);
        }
        if ($request->filled('filter_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->filter_name.'%');
            });
        }

        $attendances = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();

        $fileName = 'Rekap_Absensi_Intern_Pelindo_' . date('Y-m-d_H-i') . '.csv';

        return new StreamedResponse(function () use ($attendances) {
            $handle = fopen('php://output', 'w');
            // Tambahkan UTF-8 BOM agar langsung terbaca rapi oleh Microsoft Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No', 'ID Intern', 'Nama Intern', 'Divisi', 'Mentor', 
                'Tanggal', 'Jam Masuk', 'Jam Pulang', 'Status Kehadiran', 'Lokasi', 'Catatan / Keterlambatan'
            ]);

            foreach ($attendances as $index => $row) {
                fputcsv($handle, [
                    $index + 1,
                    $row->user->intern_id ?? '-',
                    $row->user->name ?? '-',
                    $row->user->division ?? '-',
                    $row->user->mentor->name ?? '-',
                    $row->date ? Carbon::parse($row->date)->format('Y-m-d') : '-',
                    $row->check_in ?? '-',
                    $row->check_out ?? '-',
                    strtoupper($row->status ?? '-'),
                    $row->location ?? '-',
                    $row->notes ?? '-'
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Ekspor data logbook intern ke format CSV / Excel (.xlsx compatible).
     */
    public function exportLogbook(Request $request)
    {
        $query = Logbook::with('user');
        $this->applyMentorScope($query);

        if ($request->filled('filter_date')) {
            $query->whereDate('date', $request->filter_date);
        }
        if ($request->filled('filter_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->filter_name.'%');
            });
        }

        $logbooks = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();

        $fileName = 'Rekap_Logbook_Intern_Pelindo_' . date('Y-m-d_H-i') . '.csv';

        return new StreamedResponse(function () use ($logbooks) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No', 'ID Intern', 'Nama Intern', 'Divisi', 'Mentor', 
                'Tanggal', 'Waktu', 'Kategori', 'Judul Aktivitas', 'Deskripsi', 'Status Review', 'Nilai (Grade)', 'Feedback Mentor'
            ]);

            foreach ($logbooks as $index => $row) {
                fputcsv($handle, [
                    $index + 1,
                    $row->user->intern_id ?? '-',
                    $row->user->name ?? '-',
                    $row->user->division ?? '-',
                    $row->user->mentor->name ?? '-',
                    $row->date ? Carbon::parse($row->date)->format('Y-m-d') : '-',
                    $row->time ?? '-',
                    $row->category ?? '-',
                    $row->title ?? '-',
                    $row->description ?? '-',
                    strtoupper($row->status ?? '-'),
                    $row->grade ?? '-',
                    $row->feedback ?? '-'
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Tampilkan halaman cetak resmi rekap absensi dengan kop surat Pelindo.
     */
    public function printAbsensi(Request $request)
    {
        $query = Attendance::with('user');
        $this->applyMentorScope($query);

        if ($request->filled('filter_date')) {
            $query->whereDate('date', $request->filter_date);
        }
        if ($request->filled('filter_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->filter_name.'%');
            });
        }

        $attendances = $query->orderBy('date', 'desc')->get();
        
        $totalHadir = (clone $query)->where('status', 'verified')->count();
        $totalIzin = (clone $query)->where('status', 'izin')->count();
        $totalAlpa = (clone $query)->where('status', 'alpa')->count();

        return view('admin.print_absensi', compact('attendances', 'totalHadir', 'totalIzin', 'totalAlpa'));
    }

    /**
     * Tampilkan halaman cetak resmi rekap logbook dengan kop surat Pelindo.
     */
    public function printLogbook(Request $request)
    {
        $query = Logbook::with('user');
        $this->applyMentorScope($query);

        if ($request->filled('filter_date')) {
            $query->whereDate('date', $request->filter_date);
        }
        if ($request->filled('filter_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->filter_name.'%');
            });
        }

        $logbooks = $query->orderBy('date', 'desc')->get();
        $totalLogbooks = (clone $query)->count();
        $verifiedLogbooks = (clone $query)->where('status', 'verified')->count();

        return view('admin.print_logbook', compact('logbooks', 'totalLogbooks', 'verifiedLogbooks'));
    }

    /**
     * Jalankan dan pemicu manual Notification Gateway (Email & WhatsApp Reminders).
     */
    public function triggerNotificationGateway()
    {
        Artisan::call('notifications:send-reminders', ['--force' => true]);
        $output = Artisan::output();

        return redirect()->route('admin.dashboard')->with('status', 'Notification Gateway (Email / WhatsApp) berhasil dijalankan! ' . trim(explode("\n", $output)[0] ?? 'Reminders terkirim.'));
    }

}
