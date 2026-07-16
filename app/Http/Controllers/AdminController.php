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
                $query->where(function ($q) use ($mentor) {
                    $q->where('mentor_id', $mentor->id);
                    if ($mentor->division) {
                        $q->orWhere('division', $mentor->division);
                    }
                });
            } else {
                $query->whereHas($relation, function ($q) use ($mentor) {
                    $q->where('mentor_id', $mentor->id);
                    if ($mentor->division) {
                        $q->orWhere('division', $mentor->division);
                    }
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

        return view('admin.dashboard', compact(
            'totalInterns', 'activeInterns', 'pendingAbsensi', 'recentActivities',
            'totalLogbooks', 'pendingLogbooks', 'verifiedLogbooks', 'endingSoonInterns', 'interns'
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

        $attendances = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $totalHadir = (clone $query)->where('status', 'verified')->count();
        $totalIzin = (clone $query)->where('status', 'izin')->count();
        $totalPending = (clone $query)->where('status', 'pending')->count();

        return view('admin.absensi', compact('attendances', 'totalHadir', 'totalIzin', 'totalPending'));
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

        $logbooks = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(8)->withQueryString();
        $totalLogbooks = (clone $query)->count();
        $pendingReviews = (clone $query)->where('status', 'pending')->count();
        $approvedLogs = (clone $query)->where('status', 'verified')->count();

        return view('admin.logbook', compact('logbooks', 'totalLogbooks', 'pendingReviews', 'approvedLogs'));
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

        $query = User::where('role', 'intern')->withCount(['attendances', 'logbooks']);
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('division', 'like', "%{$search}%")
                  ->orWhere('intern_id', 'like', "%{$search}%");
            });
        }

        $this->applyMentorScope($query, 'self');
        $interns = $query->orderBy('name')->paginate(10)->withQueryString();

        if (auth()->user()->role === 'admin') {
            $mentors = User::whereIn('role', ['admin', 'pembimbing'])->get();
        } else {
            $mentors = User::where('role', 'pembimbing')->get();
        }
        $divisions = Division::all();
        $internshipTypes = InternshipType::all();
        $educationLevels = EducationLevel::all();
        $universities = University::orderBy('name')->get();
        $genders = Gender::all();

        $allInternsQuery = User::where('role', 'intern')
                               ->whereNotNull('internship_end_date')
                               ->whereDate('internship_end_date', '<=', \Carbon\Carbon::today('Asia/Makassar'))
                               ->has('logbooks', '>=', 22)
                               ->orderBy('name');
        $this->applyMentorScope($allInternsQuery, 'self');
        $allInterns = $allInternsQuery->get();

        return view('admin.interns', compact('interns', 'mentors', 'divisions', 'internshipTypes', 'educationLevels', 'universities', 'genders', 'allInterns'));
    }

    /**
     * Perbarui data penempatan, masa magang, atau pembimbing intern.
     */
    public function updateIntern(Request $request, $id)
    {
        $request->validate([
            'division' => 'nullable|string|max:255',
            'internship_start_date' => 'nullable|date',
            'internship_end_date' => 'nullable|date|after_or_equal:internship_start_date',
            'mentor_id' => 'nullable|exists:users,id',
        ]);

        $query = User::where('id', $id)->where('role', 'intern');
        $this->applyMentorScope($query, 'self');
        $intern = $query->firstOrFail();

        $intern->update($request->only(['division', 'internship_start_date', 'internship_end_date', 'mentor_id']));

        return redirect()->back()->with('status', 'Data intern berhasil diupdate.');
    }

    /**
     * Perbarui data divisi atau nama pembimbing.
     */
    public function updateMentor(Request $request, $id)
    {
        $request->validate([
            'division' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
        ]);

        $query = User::where('id', $id)->whereIn('role', ['admin', 'pembimbing']);
        if (auth()->user()->role === 'pembimbing' && auth()->id() != $id) {
            abort(403, 'Anda hanya dapat mengubah data profil Anda sendiri.');
        }
        $mentor = $query->firstOrFail();
        $mentor->update($request->only(['division', 'name']));

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
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:intern,pembimbing,admin',
            'division' => 'nullable|string|max:255',
            'mentor_id' => 'nullable|exists:users,id',
            'internship_start_date' => 'nullable|date',
            'internship_end_date' => 'nullable|date|after_or_equal:internship_start_date',
            'internship_type_id' => 'nullable|exists:internship_types,id',
            'education_level_id' => 'nullable|exists:education_levels,id',
            'university_id' => 'nullable|exists:universities,id',
            'gender_id' => 'nullable|exists:genders,id',
            'password' => 'nullable|string|min:8',
        ]);

        $password = $request->filled('password') ? $request->password : Str::password(8, true, true, true, false);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'division' => $request->division,
            'mentor_id' => $request->role === 'intern' ? $request->mentor_id : null,
            'internship_start_date' => $request->role === 'intern' ? $request->internship_start_date : null,
            'internship_end_date' => $request->role === 'intern' ? $request->internship_end_date : null,
            'internship_type_id' => $request->role === 'intern' ? $request->internship_type_id : null,
            'education_level_id' => $request->role === 'intern' ? $request->education_level_id : null,
            'university_id' => $request->role === 'intern' ? $request->university_id : null,
            'gender_id' => $request->role === 'intern' ? $request->gender_id : null,
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
}
