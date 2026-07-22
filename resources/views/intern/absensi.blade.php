@extends('layouts.app')
@section('header', 'Absensi Magang')

@section('content')
<div class="space-y-6">
    <!-- Upper Grid (Profile and clock) -->
    <div class="grid grid-cols-12 gap-6">
        
        <!-- Profile Card -->
        <div class="col-span-12 lg:col-span-4 bg-blue-600 p-6 rounded-2xl flex flex-col justify-between relative overflow-hidden h-[220px] shadow-lg">
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-blue-700 flex items-center justify-center text-white font-bold text-2xl border border-white/10 shadow-inner">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">{{ Auth::user()->name }}</h3>
                    <div class="flex flex-col gap-1 mt-1.5">
                        <p class="text-sm text-blue-200 font-medium">Divisi: {{ Auth::user()->division ?? 'Belum diatur' }}</p>
                        <p class="text-sm text-blue-200 font-medium">Shift: {{ Auth::user()->shift === 'siang' ? 'Siang (12:00 - 17:00)' : 'Pagi (08:00 - 12:00)' }}</p>
                    </div>
                </div>
            </div>

            <div class="relative z-10 mt-6">
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 rounded-full text-white text-xs font-semibold backdrop-blur-sm border border-white/5">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                    Status Aktif
                </span>
            </div>

            <!-- Decorative background circle -->
            <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-blue-400 rounded-full opacity-10 blur-2xl"></div>
        </div>

        <!-- Live Clock Card -->
        <div class="col-span-12 lg:col-span-8 bg-white border border-gray-100 p-6 rounded-2xl flex flex-col items-center justify-center h-[220px] shadow-sm relative overflow-hidden">
            <div class="text-center">
                <div class="text-5xl font-extrabold text-blue-600 tracking-tight font-mono mb-2" id="live-clock">
                    --:--:--
                </div>
                <p class="text-sm font-semibold text-gray-500" id="live-date">
                    -- -- --
                </p>
                
                <div class="mt-4">
                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        @if($todayAttendance)
                            @if(in_array($todayAttendance->status, ['pending', 'izin', 'sakit']))
                                Izin / Sakit
                            @elseif($todayAttendance->check_out)
                                Selesai Bekerja
                            @else
                                Sedang Bekerja
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <button type="button" onclick="openCameraModal('check_in')" @if($todayAttendance) disabled @endif class="w-full relative overflow-hidden group bg-white border border-gray-100 p-6 rounded-2xl flex flex-col items-center gap-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-500/10 hover:border-blue-200 disabled:opacity-50 disabled:pointer-events-none cursor-pointer">
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

        <button type="button" onclick="openCameraModal('check_out')" @if(!$todayAttendance || $todayAttendance->check_out || in_array($todayAttendance->status, ['pending', 'izin', 'sakit'])) disabled @endif class="w-full relative overflow-hidden group bg-white border border-gray-100 p-6 rounded-2xl flex flex-col items-center gap-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-orange-500/10 hover:border-orange-200 disabled:opacity-50 disabled:pointer-events-none cursor-pointer">
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
                        @if(isset($activity->title))
                            {{ $activity->title }}
                        @else
                            {{ $activity->check_in ? ($activity->check_out ? 'Absen Pulang' : 'Absen Masuk') : 'Izin / Sakit' }}
                        @endif
                    </p>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                        @if(isset($activity->title))
                            {{ $activity->description }}
                        @else
                            Status: {{ ucfirst($activity->status) }}
                        @endif
                    </p>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-gray-400">
                    Belum ada aktivitas terbaru.
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { hour12: false });
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const dateString = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
        
        document.getElementById('live-clock').innerText = timeString;
        document.getElementById('live-date').innerText = dateString;
    }
    
    setInterval(updateClock, 1000);
    updateClock();

    let stream = null;
    let locationData = { lat: null, lng: null, name: null };

    function openCameraModal(type) {
        document.getElementById('absensiType').value = type;
        document.getElementById('cameraModalTitle').innerText = type === 'check_in' ? 'Absen Masuk (Selfie)' : 'Absen Pulang (Selfie)';
        document.getElementById('cameraModal').classList.remove('hidden');
        document.getElementById('btnCapture').disabled = true;
        document.getElementById('locationStatus').innerText = 'Meminta akses lokasi GPS...';

        // Get Geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                async function(position) {
                    locationData.lat = position.coords.latitude;
                    locationData.lng = position.coords.longitude;
                    document.getElementById('absensiLat').value = locationData.lat;
                    document.getElementById('absensiLng').value = locationData.lng;
                    document.getElementById('locationStatus').innerHTML = `<span class="text-emerald-400">Lokasi didapatkan:</span> ${locationData.lat.toFixed(4)}, ${locationData.lng.toFixed(4)}`;
                    
                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${locationData.lat}&lon=${locationData.lng}&zoom=18&addressdetails=1`);
                        const data = await response.json();
                        if (data && data.display_name) {
                            locationData.name = data.display_name;
                            document.getElementById('locationStatus').innerHTML = `<span class="text-emerald-400">Lokasi:</span> ${data.display_name.substring(0, 40)}...`;
                        }
                    } catch(e) {
                        console.error('Reverse geocode error:', e);
                    }
                    
                    checkReady();
                },
                function(error) {
                    document.getElementById('locationStatus').innerHTML = `<span class="text-red-400">Gagal mendapatkan lokasi GPS. Mohon izinkan akses lokasi di browser.</span>`;
                    checkReady(); 
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            document.getElementById('locationStatus').innerHTML = `<span class="text-red-400">Browser tidak mendukung fitur lokasi GPS.</span>`;
            checkReady();
        }

        // Get Camera
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false })
            .then(function(s) {
                stream = s;
                const video = document.getElementById('cameraVideo');
                video.srcObject = stream;
                video.play();
                checkReady();
            })
            .catch(function(err) {
                alert('Gagal mengakses kamera: ' + err.message + '. Pastikan Anda telah memberikan izin kamera.');
                closeCameraModal();
            });
    }

    function checkReady() {
        if (stream && stream.active && locationData.lat !== null) {
            document.getElementById('btnCapture').disabled = false;
        } else {
            document.getElementById('btnCapture').disabled = true;
        }
    }

    function closeCameraModal() {
        document.getElementById('cameraModal').classList.add('hidden');
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        locationData = { lat: null, lng: null, name: null };
    }

    function takeSnapshotAndSubmit() {
        const video = document.getElementById('cameraVideo');
        const canvas = document.getElementById('cameraCanvas');
        const context = canvas.getContext('2d');
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const now = new Date();
        const clientTime = now.getFullYear() + '-' + 
            String(now.getMonth() + 1).padStart(2, '0') + '-' + 
            String(now.getDate()).padStart(2, '0') + ' ' + 
            String(now.getHours()).padStart(2, '0') + ':' + 
            String(now.getMinutes()).padStart(2, '0') + ':' + 
            String(now.getSeconds()).padStart(2, '0');
            
        const internName = "{{ Auth::user()->name }}";
        
        // Add Watermark Overlay
        context.fillStyle = "rgba(0, 0, 0, 0.6)";
        context.fillRect(0, canvas.height - 90, canvas.width, 90);
        
        context.font = "bold 14px Arial";
        context.fillStyle = "#ffffff";
        context.fillText(`Nama: ${internName}`, 15, canvas.height - 70);
        context.fillText(`Waktu: ${clientTime}`, 15, canvas.height - 50);
        
        if (locationData.lat !== null && locationData.lng !== null) {
            context.font = "12px Arial";
            context.fillStyle = "#a7f3d0"; // emerald-200
            
            if (locationData.name) {
                const locName = locationData.name.length > 50 ? locationData.name.substring(0, 50) + "..." : locationData.name;
                context.fillText(`Lokasi: ${locName}`, 15, canvas.height - 30);
                context.fillText(`GPS: ${locationData.lat.toFixed(6)}, ${locationData.lng.toFixed(6)}`, 15, canvas.height - 10);
            } else {
                context.fillText(`GPS: ${locationData.lat.toFixed(6)}, ${locationData.lng.toFixed(6)}`, 15, canvas.height - 30);
            }
        }
        
        const photoDataUrl = canvas.toDataURL('image/jpeg', 0.8);
        document.getElementById('absensiPhoto').value = photoDataUrl;
        
        document.getElementById('absensiClientTime').value = clientTime;

        document.getElementById('btnCapture').innerText = 'Menyimpan...';
        document.getElementById('btnCapture').disabled = true;

        document.getElementById('absensiForm').submit();
    }
</script>

<!-- Camera Modal -->
<div id="cameraModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity" onclick="closeCameraModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-center">
                <h3 class="text-lg leading-6 font-bold text-gray-900 mb-2" id="cameraModalTitle">Absen Wajah (Selfie)</h3>
                <p class="text-xs text-gray-500 mb-4">Pastikan wajah Anda terlihat jelas dan izinkan akses lokasi (GPS).</p>
                
                <div class="relative w-full aspect-[3/4] bg-gray-100 rounded-xl overflow-hidden mb-4 border-2 border-dashed border-gray-300">
                    <video id="cameraVideo" class="w-full h-full object-cover" autoplay playsinline></video>
                    <canvas id="cameraCanvas" class="hidden"></canvas>
                    <div id="locationStatus" class="absolute bottom-2 left-2 right-2 bg-black/60 text-white text-[10px] py-1 px-2 rounded-md backdrop-blur-sm text-center">
                        Mendapatkan lokasi GPS...
                    </div>
                </div>

                <form id="absensiForm" action="{{ route('intern.absensi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" id="absensiType" value="">
                    <input type="hidden" name="client_time" id="absensiClientTime" value="">
                    <input type="hidden" name="photo" id="absensiPhoto" value="">
                    <input type="hidden" name="lat" id="absensiLat" value="">
                    <input type="hidden" name="lng" id="absensiLng" value="">
                    
                    <button type="button" id="btnCapture" onclick="takeSnapshotAndSubmit()" disabled class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-3 bg-blue-600 text-base font-bold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm transition-colors disabled:opacity-50">
                        Ambil Foto & Absen
                    </button>
                    <button type="button" onclick="closeCameraModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-3 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm transition-colors">
                        Batal
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
