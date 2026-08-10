@extends('layouts.app')
@section('header', 'Absensi Magang')

@section('content')
<div class="space-y-6">
    <!-- Alert Messages -->
    @if(session('status'))
        <div class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('status') }}
        </div>
    @endif
    @if($errors->any())
        <div class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Upper Grid (Profile and clock) -->
    <div class="grid grid-cols-12 gap-6">

        <!-- Profile Card -->
        <div class="col-span-12 lg:col-span-4 bg-blue-600 p-6 rounded-2xl flex flex-col justify-between relative overflow-hidden h-[220px] shadow-lg">
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-blue-700 flex items-center justify-center text-white font-bold text-2xl border border-white/10 shadow-inner overflow-hidden">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        {{ substr(Auth::user()->name, 0, 1) }}
                    @endif
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">{{ Auth::user()->name }}</h3>
                    <div class="flex flex-col gap-1 mt-1.5">
                        <p class="text-sm text-blue-200 font-medium">Divisi: {{ Auth::user()->division ?? 'Belum diatur' }}</p>
                        @php
                            $isFriday = \Carbon\Carbon::now()->isFriday();
                            $shiftStr = '';
                            if (Auth::user()->shift === 'siang') {
                                $shiftStr = $isFriday ? 'Siang (12:00 - 16:30)' : 'Siang (12:00 - 17:00)';
                            } elseif (Auth::user()->shift === 'full_day') {
                                $shiftStr = $isFriday ? 'Full Day (07:30 - 16:30)' : 'Full Day (08:00 - 17:00)';
                            } else {
                                $shiftStr = $isFriday ? 'Pagi (07:30 - 12:00)' : 'Pagi (08:00 - 12:00)';
                            }
                        @endphp
                        <p class="text-sm text-blue-200 font-medium">Shift: {{ $shiftStr }}</p>
                    </div>
                </div>
            </div>
            <div class="relative z-10 mt-6">
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 rounded-full text-white text-xs font-semibold backdrop-blur-sm border border-white/5">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                    Status Aktif
                </span>
            </div>
            <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-blue-400 rounded-full opacity-10 blur-2xl"></div>
        </div>

        <!-- Live Clock Card -->
        <div class="col-span-12 lg:col-span-8 bg-white border border-gray-100 p-6 rounded-2xl flex flex-col items-center justify-center h-[220px] shadow-sm relative overflow-hidden">
            <div class="text-center">
                <div class="text-5xl font-extrabold text-blue-600 tracking-tight font-mono mb-2" id="live-clock">--:--:--</div>
                <p class="text-sm font-semibold text-gray-500" id="live-date">-- -- --</p>
                <div class="mt-4">
                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        @if($todayAttendance)
                            @if(in_array($todayAttendance->status, ['pending', 'izin', 'sakit'])) Izin / Sakit
                            @elseif($todayAttendance->check_out) Selesai Bekerja
                            @else Sedang Bekerja
                            @endif
                        @else
                            Belum Absen
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Check In / Out Buttons -->
    
    @if(!$hasFaceRegistered)
    <div class="bg-red-50 border border-red-200 p-5 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect width="10" height="14" x="7" y="5" rx="2"/><path d="M12 12h.01"/></svg>
            </div>
            <div>
                <h4 class="text-red-700 font-bold text-lg">Pendaftaran Biometrik Wajah Wajib</h4>
                <p class="text-red-600 text-sm font-medium">Anda harus mendaftarkan wajah Anda terlebih dahulu sebelum dapat melakukan absensi.</p>
            </div>
        </div>
        <button type="button" onclick="openModal('register_face')" class="w-full md:w-auto px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-colors shadow-md shadow-red-500/20 flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
            Daftar Wajah Sekarang
        </button>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Form Check In -->
        <form id="form-check-in" action="{{ route('intern.absensi.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="check_in">
            <input type="hidden" name="client_time" class="client-time-input">
            <input type="hidden" name="face_photo" id="face-photo-check-in">
            <button type="button" id="btn-check-in" onclick="openModal('check_in')"
                @if($todayAttendance || !$hasFaceRegistered) disabled @endif
                class="w-full relative overflow-hidden group bg-white border border-gray-100 p-6 rounded-2xl flex flex-col items-center gap-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-500/10 hover:border-blue-200 disabled:opacity-50 disabled:pointer-events-none cursor-pointer">
                <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
                </div>
                <div class="text-center">
                    <h4 class="font-bold text-gray-800 text-lg mb-1">Absen Masuk</h4>
                    <p class="text-xs text-gray-500 font-medium">Mulai jam kerja Anda</p>
                </div>
                @if($todayAttendance && $todayAttendance->check_in)
                <div class="absolute inset-0 bg-white/60 backdrop-blur-[2px] flex items-center justify-center">
                    <div class="bg-white px-4 py-2 rounded-full shadow-sm border border-green-100 flex items-center gap-2 text-green-600 text-sm font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Tercatat: {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') }}
                    </div>
                </div>
                @endif
            </button>
        </form>

        <!-- Form Check Out -->
        <form id="form-check-out" action="{{ route('intern.absensi.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="check_out">
            <input type="hidden" name="client_time" class="client-time-input">
            <input type="hidden" name="face_photo" id="face-photo-check-out">
            <button type="button" id="btn-check-out" onclick="openModal('check_out')"
                @if(!$todayAttendance || $todayAttendance->check_out || in_array($todayAttendance->status, ['pending', 'izin', 'sakit']) || !$hasFaceRegistered) disabled @endif
                class="w-full relative overflow-hidden group bg-white border border-gray-100 p-6 rounded-2xl flex flex-col items-center gap-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-orange-500/10 hover:border-orange-200 disabled:opacity-50 disabled:pointer-events-none cursor-pointer">
                <div class="w-16 h-16 rounded-full bg-orange-50 flex items-center justify-center text-orange-500 group-hover:scale-110 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                </div>
                <div class="text-center">
                    <h4 class="font-bold text-gray-800 text-lg mb-1">Absen Pulang</h4>
                    <p class="text-xs text-gray-500 font-medium">Akhiri jam kerja Anda</p>
                </div>
                @if($todayAttendance && $todayAttendance->check_out)
                <div class="absolute inset-0 bg-white/60 backdrop-blur-[2px] flex items-center justify-center">
                    <div class="bg-white px-4 py-2 rounded-full shadow-sm border border-blue-100 flex items-center gap-2 text-blue-600 text-sm font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Selesai: {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i') }}
                    </div>
                </div>
                @endif
                @if($todayAttendance && in_array($todayAttendance->status, ['pending', 'izin', 'sakit']))
                <div class="absolute inset-0 bg-white/60 backdrop-blur-[2px] flex items-center justify-center">
                    <div class="bg-white px-4 py-2 rounded-full shadow-sm border border-purple-100 flex items-center gap-2 text-purple-600 text-sm font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        Izin / Sakit
                    </div>
                </div>
                @endif
            </button>
        </form>

        <form id="form-register-face" action="{{ route('intern.absensi.register-face') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="face_descriptor" id="face-descriptor-input">
        </form>

        <a href="{{ route('intern.leaves') }}" class="w-full relative overflow-hidden group bg-white border border-gray-100 p-6 rounded-2xl flex flex-col items-center gap-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-red-500/10 hover:border-red-200 cursor-pointer">
            <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center text-red-500 group-hover:scale-110 group-hover:bg-red-500 group-hover:text-white transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            </div>
            <div class="text-center">
                <h4 class="font-bold text-gray-800 text-lg mb-1">Izin / Sakit / Cuti</h4>
                <p class="text-xs text-gray-500 font-medium">Ajukan ketidakhadiran</p>
            </div>
        </a>
    </div>

    <!-- Milestone Magang Progress Bar -->
    @if($user->internship_start_date && $user->internship_end_date)
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mt-6 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-500 rounded-full mix-blend-screen opacity-10 blur-3xl"></div>
        <div class="flex justify-between items-end mb-3 relative z-10">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Milestone Magang</h3>
                <p class="text-sm text-gray-500">
                    {{ \Carbon\Carbon::parse($user->internship_start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($user->internship_end_date)->format('d M Y') }}
                </p>
            </div>
            <div class="text-right">
                <span class="text-3xl font-extrabold text-blue-600 leading-none">{{ $progress }}%</span>
            </div>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-3 mb-4 relative z-10 overflow-hidden shadow-inner">
            <div class="bg-gradient-to-r from-blue-500 to-emerald-400 h-3 rounded-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
        </div>
        <div class="grid grid-cols-3 gap-4 text-center relative z-10">
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                <p class="text-xs font-semibold text-gray-500 mb-1">Total Hari</p>
                <p class="text-lg font-bold text-gray-800">{{ $totalDays }}</p>
            </div>
            <div class="bg-blue-50 rounded-xl p-3 border border-blue-100">
                <p class="text-xs font-semibold text-blue-600 mb-1">Sudah Masuk</p>
                <p class="text-lg font-bold text-blue-800">{{ $daysAttended }} hari</p>
            </div>
            <div class="bg-orange-50 rounded-xl p-3 border border-orange-100">
                <p class="text-xs font-semibold text-orange-600 mb-1">Tersisa</p>
                <p class="text-lg font-bold text-orange-800">{{ $remainingDays }} hari</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Aktivitas Terbaru -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mt-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Aktivitas Terbaru</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($recentActivities as $activity)
                <div class="p-4 rounded-xl border border-gray-50 hover:bg-gray-50 transition-colors shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold text-gray-400">{{ \Carbon\Carbon::parse($activity->created_at)->format('d M Y, H:i') }}</span>
                        @if(isset($activity->title))
                            <span class="px-2 py-1 bg-purple-50 text-purple-600 rounded-md text-[10px] font-bold">Logbook</span>
                        @else
                            <span class="px-2 py-1 bg-green-50 text-green-600 rounded-md text-[10px] font-bold">Absensi</span>
                        @endif
                    </div>
                    <p class="text-sm font-bold text-gray-800 line-clamp-1">
                        @if(isset($activity->title)) {{ $activity->title }}
                        @else {{ $activity->check_in ? ($activity->check_out ? 'Absen Pulang' : 'Absen Masuk') : 'Izin / Sakit' }}
                        @endif
                    </p>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                        @if(isset($activity->title)) {{ $activity->description }}
                        @else Status: {{ ucfirst($activity->status) }}
                        @endif
                    </p>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-gray-400">Belum ada aktivitas terbaru.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- ===================== MODAL FOTO WAJAH ===================== -->
<div id="face-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                </div>
                <h3 class="text-white font-bold text-lg" id="modal-title">Foto Wajah</h3>
            </div>
            <button onclick="closeModal()" class="text-white/70 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6">

            <!-- Area preview foto -->
            <div class="relative rounded-xl overflow-hidden bg-gray-100 aspect-video mb-4 flex items-center justify-center" id="preview-area">
                <!-- Placeholder sebelum foto diambil -->
                <div id="placeholder" class="flex flex-col items-center gap-3 text-gray-400">
                    <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    </div>
                    <p class="text-sm font-medium">Belum ada foto</p>
                </div>
                
                <!-- Video webcam untuk desktop -->
                <video id="webcam-video" autoplay playsinline class="hidden w-full h-full object-cover absolute inset-0" style="transform: scaleX(-1);"></video>
                <canvas id="webcam-canvas" class="hidden"></canvas>

                <!-- Preview foto setelah diambil -->
                <img id="photo-preview" src="" alt="Preview" class="hidden w-full h-full object-cover absolute inset-0">
                <!-- Badge sukses -->
                <div id="photo-badge" class="hidden absolute top-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Foto siap
                </div>
            </div>

            <!-- Instruksi -->
            <p class="text-xs text-center text-gray-500 mb-5" id="modal-instruction">
                Klik tombol di bawah untuk membuka kamera dan mengambil foto wajah Anda
            </p>

            <!-- Input file tersembunyi — capture="user" = kamera depan, tanpa gallery -->
            <input type="file" id="camera-input" accept="image/*" capture="user" class="hidden">

            <!-- Status deteksi wajah -->
            <div id="detection-status" class="hidden"></div>

            <!-- Tombol aksi -->
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Tombol Buka Kamera / Ulangi -->
                <button id="btn-open-camera" type="button"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white font-bold rounded-xl transition-all duration-200 hover:bg-blue-700 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    <span id="btn-camera-text">Buka Kamera</span>
                </button>
                <!-- Tombol Konfirmasi (muncul setelah foto diambil) -->
                <button id="btn-confirm" type="button" onclick="confirmAbsensi()"
                    class="hidden flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-green-600 text-white font-bold rounded-xl transition-all duration-200 hover:bg-green-700 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Konfirmasi Absen
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    // ======================== LIVE CLOCK ========================
    function updateClock() {
        const now    = new Date();
        const days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        document.getElementById('live-clock').innerText = now.toLocaleTimeString('id-ID', { hour12: false });
        document.getElementById('live-date').innerText  = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ======================== MODAL & KAMERA ========================
    let currentAbsenType    = null;
    let capturedPhotoBase64 = null;
    let currentDescriptor   = null;
    let modelsLoaded        = false;
    let registeredDescriptors = [];
    let registerStep = 0;
    const registerStepNames = ['Depan', 'Kiri', 'Kanan'];

    const savedFaceDescriptor = {!! $userFaceDescriptor !!};
    const MODEL_URL = '/face-api-weights';

    // Preload model saat halaman siap
    async function loadModels() {
        try {
            await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
            await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
            modelsLoaded = true;
        } catch (e) {
            console.warn('Model face-api gagal dimuat:', e);
        }
    }
    loadModels();

    function setDetectionStatus(type, text) {
        const el = document.getElementById('detection-status');
        el.className = 'flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 mt-3';
        const icons = {
            loading: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>`,
            success: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`,
            error:   `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`
        };
        const classes = {
            loading: 'bg-yellow-50 border border-yellow-200 text-yellow-700',
            success: 'bg-green-50 border border-green-200 text-green-700',
            error:   'bg-red-50 border border-red-200 text-red-700'
        };
        el.classList.add(...classes[type].split(' '));
        el.innerHTML = `${icons[type]}<span>${text}</span>`;
        el.classList.remove('hidden');
    }

    let videoStream = null;

    async function openModal(type) {
        currentAbsenType    = type;
        capturedPhotoBase64 = null;
        registeredDescriptors = [];
        registerStep = 0;

        // Reset UI
        document.getElementById('photo-preview').classList.add('hidden');
        document.getElementById('photo-preview').src = '';
        document.getElementById('webcam-video').classList.add('hidden');
        document.getElementById('photo-badge').classList.add('hidden');
        document.getElementById('placeholder').classList.remove('hidden');
        document.getElementById('btn-confirm').classList.add('hidden');
        document.getElementById('detection-status').classList.add('hidden');
        document.getElementById('camera-input').value = '';

        const titles = { 
            check_in: 'Foto Wajah — Absen Masuk', 
            check_out: 'Foto Wajah — Absen Pulang',
            register_face: 'Pendaftaran Biometrik Wajah'
        };
        document.getElementById('modal-title').innerText = titles[type] || 'Foto Wajah';
        document.getElementById('face-modal').classList.remove('hidden');

        // Coba gunakan WebRTC (Laptop / HTTPS)
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            try {
                videoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                const video = document.getElementById('webcam-video');
                video.srcObject = videoStream;
                video.classList.remove('hidden');
                document.getElementById('placeholder').classList.add('hidden');
                
                document.getElementById('btn-camera-text').innerText = (type === 'register_face') ? 'Ambil Foto Depan' : 'Ambil Foto';
                document.getElementById('modal-instruction').innerText = (type === 'register_face') ? 'Posisikan wajah lurus ke depan, lalu klik Ambil Foto.' : 'Posisikan wajah Anda di kamera lalu klik Ambil Foto.';
                document.getElementById('btn-open-camera').onclick = captureFromVideo;
                return;
            } catch (err) {
                console.warn('WebRTC gagal, menggunakan fallback input file:', err);
            }
        }
        
        // Fallback untuk Mobile HTTP
        document.getElementById('btn-camera-text').innerText = (type === 'register_face') ? 'Buka Kamera' : 'Buka Kamera';
        document.getElementById('modal-instruction').innerText = (type === 'register_face') ? 'Klik tombol di bawah untuk memfoto wajah lurus ke depan.' : 'Klik tombol di bawah untuk membuka kamera HP Anda.';
        document.getElementById('btn-open-camera').onclick = openCameraInput;
    }

    function openCameraInput() {
        const btnText = document.getElementById('btn-camera-text').innerText;
        if (btnText === 'Ulangi Foto' || btnText === 'Lanjut' || btnText === 'Ulangi dari Awal') {
            document.getElementById('photo-preview').classList.add('hidden');
            document.getElementById('photo-badge').classList.add('hidden');
            document.getElementById('btn-confirm').classList.add('hidden');
            document.getElementById('detection-status').classList.add('hidden');
            document.getElementById('placeholder').classList.remove('hidden');
            
            if (btnText === 'Ulangi dari Awal') {
                registeredDescriptors = [];
                registerStep = 0;
            }
            
            if (currentAbsenType === 'register_face') {
                document.getElementById('btn-camera-text').innerText = `Buka Kamera`;
                document.getElementById('modal-instruction').innerText = `Klik tombol di bawah untuk memfoto wajah menoleh ke ${registerStepNames[registerStep].toLowerCase()}.`;
                if (registerStep === 0) document.getElementById('modal-instruction').innerText = `Klik tombol di bawah untuk memfoto wajah lurus ke depan.`;
            } else {
                document.getElementById('btn-camera-text').innerText = 'Buka Kamera';
                document.getElementById('modal-instruction').innerText = 'Klik tombol di bawah untuk membuka kamera HP Anda.';
            }
            capturedPhotoBase64 = null;
        }
        document.getElementById('camera-input').click();
    }
    
    function captureFromVideo() {
        const btnText = document.getElementById('btn-camera-text').innerText;
        if (btnText === 'Ulangi Foto' || btnText === 'Lanjut' || btnText === 'Ulangi dari Awal') {
            document.getElementById('photo-preview').classList.add('hidden');
            document.getElementById('photo-badge').classList.add('hidden');
            document.getElementById('btn-confirm').classList.add('hidden');
            document.getElementById('detection-status').classList.add('hidden');
            document.getElementById('webcam-video').classList.remove('hidden');
            
            if (btnText === 'Ulangi dari Awal') {
                registeredDescriptors = [];
                registerStep = 0;
            }
            
            if (currentAbsenType === 'register_face') {
                document.getElementById('btn-camera-text').innerText = `Ambil Foto ${registerStepNames[registerStep]}`;
                document.getElementById('modal-instruction').innerText = `Posisikan wajah Anda menoleh ke ${registerStepNames[registerStep].toLowerCase()}, lalu klik Ambil Foto.`;
                if (registerStep === 0) document.getElementById('modal-instruction').innerText = `Posisikan wajah Anda lurus ke depan, lalu klik Ambil Foto.`;
            } else {
                document.getElementById('btn-camera-text').innerText = 'Ambil Foto';
                document.getElementById('modal-instruction').innerText = 'Posisikan wajah Anda di kamera lalu klik Ambil Foto.';
            }
            capturedPhotoBase64 = null;
            return;
        }

        const video = document.getElementById('webcam-video');
        const canvas = document.getElementById('webcam-canvas');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        
        // Flip canvas secara horizontal karena video di-mirror
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        capturedPhotoBase64 = canvas.toDataURL('image/jpeg');
        
        const preview = document.getElementById('photo-preview');
        preview.src = capturedPhotoBase64;
        preview.classList.remove('hidden');
        video.classList.add('hidden');
        
        processCapturedPhoto(preview);
    }

    document.getElementById('camera-input').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const now = new Date().getTime();
        const fileTime = file.lastModified;
        const diffMinutes = (now - fileTime) / (1000 * 60);

        if (diffMinutes > 2) {
            setDetectionStatus('error', 'Akses ditolak: Tidak boleh menggunakan foto dari gallery. Silakan ambil foto langsung dari kamera sekarang.');
            document.getElementById('camera-input').value = '';
            capturedPhotoBase64 = null;
            return;
        }

        const reader = new FileReader();
        reader.onload = function (ev) {
            capturedPhotoBase64 = ev.target.result;
            const preview = document.getElementById('photo-preview');
            preview.src = capturedPhotoBase64;
            preview.classList.remove('hidden');
            document.getElementById('placeholder').classList.add('hidden');
            
            processCapturedPhoto(preview);
        };
        reader.readAsDataURL(file);
    });

    async function processCapturedPhoto(imgElement) {
        document.getElementById('btn-camera-text').innerText = 'Ulangi Foto';
        document.getElementById('btn-confirm').classList.add('hidden');
        document.getElementById('photo-badge').classList.add('hidden');

        setDetectionStatus('loading', 'Menganalisis biometrik wajah...');
        document.getElementById('modal-instruction').innerText = 'Harap tunggu, sedang memproses wajah...';

        if (!modelsLoaded) {
            try {
                await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
                modelsLoaded = true;
            } catch (e) {
                setDetectionStatus('error', 'Sistem pengenalan wajah gagal dimuat.');
                return;
            }
        }

        try {
            if (!imgElement.complete) {
                await new Promise(resolve => { imgElement.onload = resolve; });
            }

            const detection = await faceapi.detectSingleFace(
                imgElement,
                new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.45 })
            ).withFaceLandmarks().withFaceDescriptor();

            if (detection) {
                currentDescriptor = detection.descriptor;
                
                if (currentAbsenType === 'register_face') {
                    // Update step logic for 3 angles
                    if (document.getElementById('btn-camera-text').innerText === 'Ulangi dari Awal') {
                        // They reached the end but decided to redo. It was handled in the click, but just in case.
                    } else {
                        // Hapus hasil descriptor lama jika mereka klik Ulangi Foto pada step tertentu (ditangani dengan tidak men-push)
                        registeredDescriptors[registerStep] = Array.from(currentDescriptor);
                        registerStep++;
                    }
                    
                    if (registerStep < 3) {
                        setDetectionStatus('success', `Wajah ${registerStepNames[registerStep-1]} berhasil dipindai! Lanjut ke tahap berikutnya.`);
                        document.getElementById('btn-camera-text').innerText = 'Lanjut';
                        document.getElementById('modal-instruction').innerText = `Lanjut mengambil foto wajah ${registerStepNames[registerStep]}.`;
                    } else {
                        setDetectionStatus('success', 'Ketiga sudut wajah berhasil dipindai! Silakan simpan data biometrik Anda.');
                        showConfirmButton('Simpan Biometrik Wajah');
                        document.getElementById('btn-camera-text').innerText = 'Ulangi dari Awal';
                        document.getElementById('modal-instruction').innerText = 'Klik tombol di bawah untuk menyelesaikan pendaftaran.';
                    }
                } else {
                    // Verifikasi Wajah (Check In / Check Out)
                    let minDistance = 1.0;
                    
                    if (Array.isArray(savedFaceDescriptor) && savedFaceDescriptor.length > 0 && Array.isArray(savedFaceDescriptor[0])) {
                        for (let i = 0; i < savedFaceDescriptor.length; i++) {
                            const savedArray = new Float32Array(savedFaceDescriptor[i]);
                            const distance = faceapi.euclideanDistance(currentDescriptor, savedArray);
                            if (distance < minDistance) minDistance = distance;
                        }
                    } else if (Array.isArray(savedFaceDescriptor) && savedFaceDescriptor.length > 0) {
                        // Backwards compatibility for 1D array
                        const savedArray = new Float32Array(savedFaceDescriptor);
                        minDistance = faceapi.euclideanDistance(currentDescriptor, savedArray);
                    }
                    
                    if (minDistance < 0.55) { // Threshold: smaller means stricter (0.55 is good)
                        const matchPercent = Math.max(0, ((1 - minDistance) * 100)).toFixed(0);
                        setDetectionStatus('success', `Wajah terverifikasi cocok! (${matchPercent}% match)`);
                        showConfirmButton('Konfirmasi Absen');
                        document.getElementById('modal-instruction').innerText = 'Wajah diverifikasi. Klik "Konfirmasi Absen".';
                    } else {
                        capturedPhotoBase64 = null;
                        currentDescriptor = null;
                        const matchPercent = Math.max(0, ((1 - minDistance) * 100)).toFixed(0);
                        setDetectionStatus('error', `Wajah TIDAK COCOK dengan yang terdaftar! (${matchPercent}% match)`);
                        document.getElementById('modal-instruction').innerText = 'Pastikan Anda adalah pemilik akun ini atau posisikan wajah lebih jelas.';
                    }
                }
            } else {
                capturedPhotoBase64 = null;
                currentDescriptor = null;
                setDetectionStatus('error', 'Wajah tidak terdeteksi. Pastikan wajah terlihat jelas dan ulangi foto.');
                document.getElementById('modal-instruction').innerText = 'Posisikan wajah di tengah frame dan hindari ruangan gelap.';
            }
        } catch (err) {
            console.error(err);
            capturedPhotoBase64 = null;
            currentDescriptor = null;
            setDetectionStatus('error', 'Gagal memproses gambar. Ulangi foto.');
        }
    }

    function showConfirmButton(text) {
        const btn = document.getElementById('btn-confirm');
        btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> ${text}`;
        btn.classList.remove('hidden');
        btn.classList.add('flex');
        
        const badge = document.getElementById('photo-badge');
        badge.classList.remove('hidden');
        badge.classList.add('flex');
    }

    function confirmAbsensi() {
        if (currentAbsenType === 'register_face') {
            if (registeredDescriptors.length !== 3) return;
            document.getElementById('face-descriptor-input').value = JSON.stringify(registeredDescriptors);
            document.getElementById('form-register-face').submit();
            closeModal();
            return;
        }

        if (!capturedPhotoBase64 || !currentAbsenType) return;

        const inputId = 'face-photo-' + currentAbsenType.replace('_', '-');
        document.getElementById(inputId).value = capturedPhotoBase64;

        const formId = 'form-' + currentAbsenType.replace('_', '-');
        const form   = document.getElementById(formId);
        const clientTimeInput = form.querySelector('.client-time-input');
        if (clientTimeInput) {
            const now = new Date();
            const pad = n => String(n).padStart(2, '0');
            clientTimeInput.value = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
        }

        closeModal();
        form.submit();
    }

    function closeModal() {
        document.getElementById('face-modal').classList.add('hidden');
        currentAbsenType    = null;
        capturedPhotoBase64 = null;
        
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
    }

    document.getElementById('face-modal').addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });
</script>

@endsection
