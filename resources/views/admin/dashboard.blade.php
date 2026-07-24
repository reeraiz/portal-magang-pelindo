@extends('layouts.app')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- ========================================================= -->
    <!-- BARIS 1: RINGKASAN STATISTIK ABSENSI & LOGBOOK (2 KOLOM) -->
    <!-- ========================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kolom Kiri: Statistik Absensi & Intern -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-100 transition-all duration-300 flex flex-col justify-between">
            <h3 class="font-bold text-gray-800 text-base mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600 inline-block"></span>
                Ringkasan Absensi Magang
            </h3>
            <div class="grid grid-cols-3 gap-4 divide-x divide-gray-100 text-center items-center">
                <div class="px-2">
                    <p class="text-xs font-bold text-gray-400 mb-1 tracking-wide uppercase">Total Intern</p>
                    <h4 class="text-2xl font-extrabold text-blue-600">{{ number_format($totalInterns) }}</h4>
                </div>
                <div class="px-2">
                    <p class="text-xs font-bold text-gray-400 mb-1 tracking-wide uppercase">Aktif Hari Ini</p>
                    <h4 class="text-2xl font-extrabold text-emerald-600">{{ number_format($activeInterns) }}</h4>
                </div>
                <div class="px-2">
                    <p class="text-xs font-bold text-gray-400 mb-1 tracking-wide uppercase">Menunggu Review</p>
                    <h4 class="text-2xl font-extrabold text-orange-500">{{ number_format($pendingAbsensi) }}</h4>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Ringkasan Logbook -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-emerald-100 transition-all duration-300 flex flex-col justify-between">
            <h3 class="font-bold text-gray-800 text-base mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-600 inline-block"></span>
                Ringkasan Logbook
            </h3>
            <div class="grid grid-cols-3 gap-4 divide-x divide-gray-100 text-center items-center">
                <div class="px-2">
                    <p class="text-xs font-bold text-gray-400 mb-1 tracking-wide uppercase">Total Logbook</p>
                    <h4 class="text-2xl font-extrabold text-blue-600">{{ number_format($totalLogbooks) }}</h4>
                </div>
                <div class="px-2">
                    <p class="text-xs font-bold text-gray-400 mb-1 tracking-wide uppercase">Menunggu Review</p>
                    <h4 class="text-2xl font-extrabold text-orange-500">{{ number_format($pendingLogbooks) }}</h4>
                </div>
                <div class="px-2">
                    <p class="text-xs font-bold text-gray-400 mb-1 tracking-wide uppercase">Disetujui</p>
                    <h4 class="text-2xl font-extrabold text-emerald-600">{{ number_format($verifiedLogbooks) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- BARIS 2: AKTIVITAS TERBARU & DAFTAR INTERN (2 KOLOM) -->
    <!-- ========================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kolom Kiri: Aktivitas -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col max-h-[460px]">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 shrink-0">
                <h3 class="font-bold text-gray-800 text-base">Aktivitas</h3>
                <a href="{{ route('admin.absensi') }}" class="text-xs text-blue-600 font-semibold hover:text-blue-800 transition-colors">Lihat Semua</a>
            </div>
            <div class="p-6 overflow-y-auto space-y-5 flex-1 custom-scrollbar">
                @forelse($recentActivities as $activity)
                <div class="flex gap-3.5 items-start relative group">
                    <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center shrink-0 border border-blue-200 text-xs font-bold text-blue-700 shadow-sm">
                        {{ substr($activity->user->name, 0, 1) }}
                    </div>
                    <div class="flex-1 pb-4 border-b border-gray-50 group-last:border-0 group-last:pb-0">
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="font-bold text-gray-800 text-xs">{{ $activity->user->name }}</h4>
                            <span class="text-[11px] font-semibold text-gray-400">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            @if(in_array($activity->status, ['izin', 'sakit']) || ($activity->status === 'pending' && !$activity->check_in && !$activity->check_out))
                                mengajukan <span class="font-semibold text-amber-600">Izin / Sakit</span> pada pukul {{ \Carbon\Carbon::parse($activity->created_at)->format('H:i') }}
                            @else
                                melakukan <span class="font-semibold text-blue-600">{{ $activity->check_in && !$activity->check_out ? 'Absen Masuk' : 'Absen Pulang' }}</span> pada pukul {{ \Carbon\Carbon::parse($activity->created_at)->format('H:i') }}
                            @endif
                        </p>
                    </div>
                </div>
                @empty
                <div class="h-full flex flex-col items-center justify-center text-gray-400 py-12">
                    <svg class="w-10 h-10 mb-2 opacity-20 shrink-0" style="width: 40px; height: 40px; max-width: 40px; max-height: 40px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-xs font-medium">Belum ada aktivitas terbaru.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Intern -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col max-h-[460px]">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 shrink-0">
                <div>
                    <h3 class="font-bold text-gray-800 text-base">Daftar Intern</h3>
                    <p class="text-[11px] text-gray-400">Total peserta magang terdaftar</p>
                </div>
                <a href="{{ route('admin.interns') }}" class="text-xs text-blue-600 font-semibold hover:text-blue-800 transition-colors">Kelola Intern</a>
            </div>
            <div class="overflow-y-auto flex-1 custom-scrollbar">
                <table class="w-full text-left text-xs whitespace-nowrap">
                    <thead class="bg-gray-50/80 text-gray-500 font-semibold border-b border-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3">Nama & Divisi</th>
                            <th class="px-4 py-3">Mentor</th>
                            <th class="px-4 py-3 text-center">Statistik</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($interns as $intern)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 font-bold flex items-center justify-center shrink-0 border border-blue-100 text-xs shadow-sm overflow-hidden">
                                        @if($intern->avatar)
                                            <img src="{{ asset('storage/' . $intern->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                                        @else
                                            {{ substr($intern->name, 0, 1) }}
                                        @endif
                                    </div>
                                    <div class="overflow-hidden">
                                        <h4 class="font-bold text-gray-800 truncate">{{ $intern->name }}</h4>
                                        <p class="text-[11px] text-gray-500 truncate">{{ $intern->division ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($intern->mentor)
                                    <span class="font-semibold text-gray-700 block truncate">{{ $intern->mentor->name }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $intern->mentor->role === 'pembimbing' ? 'Pembimbing' : 'Admin' }}</span>
                                @else
                                    <span class="text-orange-500 italic">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-3">
                                    <div class="text-center">
                                        <span class="block text-sm font-extrabold text-blue-600">{{ $intern->attendances_count ?? 0 }}</span>
                                        <span class="block text-[9px] font-bold text-gray-400 uppercase">Absen</span>
                                    </div>
                                    <div class="w-px h-5 bg-gray-200"></div>
                                    <div class="text-center">
                                        <span class="block text-sm font-extrabold text-emerald-600">{{ $intern->logbooks_count ?? 0 }}</span>
                                        <span class="block text-[9px] font-bold text-gray-400 uppercase">Logbook</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-400">Belum ada intern terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- BARIS 3: FLAG KEHADIRAN (FULL WIDTH) -->
    <!-- ========================================================= -->
    <div class="bg-white p-6 rounded-2xl border border-red-100 shadow-sm relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-red-50 border border-red-100 flex items-center justify-center text-red-600 animate-pulse">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-base">Flag Kehadiran</h3>
                    <p class="text-xs text-gray-500">Daftar intern dengan akumulasi ketidakhadiran tanpa keterangan (Alpa) > 3 hari.</p>
                </div>
            </div>
            <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-xs font-bold border border-red-100 shrink-0">
                {{ $lowAttendanceInterns->count() }} Peringatan
            </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 max-h-[260px] overflow-y-auto pr-1 custom-scrollbar">
            @forelse($lowAttendanceInterns as $low)
            <div class="bg-red-50/60 hover:bg-red-50 transition-colors p-3.5 rounded-xl border border-red-200 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-red-600 flex items-center justify-center font-bold text-white shrink-0 text-xs shadow-sm overflow-hidden">
                        @if($low->avatar)
                            <img src="{{ asset('storage/' . $low->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            {{ substr($low->name, 0, 1) }}
                        @endif
                    </div>
                    <div class="overflow-hidden">
                        <h4 class="text-xs font-bold text-gray-800 truncate">{{ $low->name }}</h4>
                        <p class="text-[11px] text-red-600 font-semibold truncate">{{ $low->division ?? 'Belum diatur' }}</p>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="inline-block px-2.5 py-0.5 bg-red-600 text-white font-extrabold rounded text-xs mb-0.5 shadow-sm">
                        Alpa: {{ $low->alpa_count ?? 0 }} Hari
                    </span>
                    <div class="text-[10px] text-gray-500 font-semibold">{{ $low->attendance_rate ?? 0 }}% Kehadiran</div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-6 text-gray-400 bg-gray-50/40 rounded-xl border border-gray-100 flex flex-col items-center justify-center">
                <svg class="w-7 h-7 mx-auto mb-1.5 opacity-30 text-emerald-500" style="width: 28px; height: 28px; max-width: 28px; max-height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-xs font-semibold text-gray-500">Tidak ada intern dengan akumulasi Alpa > 3 hari</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- BARIS 4: SELESAI MAGANG (FULL WIDTH) -->
    <!-- ========================================================= -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-orange-50 border border-orange-100 flex items-center justify-center text-orange-600">
                    <svg class="w-4 h-4" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-base">Selesai Magang</h3>
                    <p class="text-xs text-gray-500">Daftar intern yang masa magangnya akan berakhir dalam kurun waktu 1 minggu ke depan.</p>
                </div>
            </div>
            <span class="px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-xs font-bold border border-orange-100 shrink-0">
                {{ $endingSoonInterns->count() }} Intern
            </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 max-h-[260px] overflow-y-auto pr-1 custom-scrollbar">
            @forelse($endingSoonInterns as $intern)
            <div class="bg-gray-50/70 hover:bg-orange-50/50 transition-colors p-3.5 rounded-xl border border-gray-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-orange-500 flex items-center justify-center font-bold text-white shrink-0 text-xs shadow-sm overflow-hidden">
                        @if($intern->avatar)
                            <img src="{{ asset('storage/' . $intern->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            {{ substr($intern->name, 0, 1) }}
                        @endif
                    </div>
                    <div class="overflow-hidden">
                        <h4 class="font-bold text-gray-800 text-xs truncate">{{ $intern->name }}</h4>
                        <p class="text-[11px] text-gray-500 truncate">{{ $intern->division ?? 'Divisi Umum' }}</p>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="block text-xs font-extrabold text-orange-600">
                        {{ \Carbon\Carbon::parse($intern->internship_end_date)->format('d M Y') }}
                    </span>
                    <span class="block text-[10px] text-gray-400 font-semibold">
                        {{ \Carbon\Carbon::parse($intern->internship_end_date)->diffForHumans() }}
                    </span>
                </div>
            </div>
            @empty
            <div class="col-span-full py-6 text-center bg-gray-50/40 rounded-xl border border-gray-100 flex flex-col items-center justify-center">
                <svg class="w-7 h-7 text-gray-300 mx-auto mb-1.5 shrink-0" style="width: 28px; height: 28px; max-width: 28px; max-height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-xs text-gray-500 font-medium">Tidak ada intern yang selesai magang dalam 1 minggu ke depan.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- BARIS 5: KETERLAMBATAN PER DIVISI & KEHADIRAN 6 BULAN (2 KOLOM) -->
    <!-- ========================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart 1: Keterlambatan per Divisi -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-base">Keterlambatan per Divisi</h3>
                            <p class="text-xs text-gray-500">Rata-rata menit keterlambatan check-in (>08:00/07:30 WITA)</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[11px] font-bold border border-indigo-100 shrink-0">
                        Menit / Divisi
                    </span>
                </div>
            </div>
            <div class="relative h-[280px] w-full">
                <canvas id="chartLateDivision"></canvas>
            </div>
        </div>

        <!-- Chart 2: Kehadiran 6 Bulan Terakhir -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-base">Kehadiran 6 Bulan Terakhir</h3>
                            <p class="text-xs text-gray-500">Perbandingan Hadir (Verified), Izin/Sakit, dan Alpa</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 text-xs font-semibold shrink-0">
                        <span class="flex items-center gap-1 text-emerald-600"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> Hadir</span>
                        <span class="flex items-center gap-1 text-yellow-600"><span class="w-2.5 h-2.5 rounded-full bg-yellow-500 inline-block"></span> Izin</span>
                        <span class="flex items-center gap-1 text-red-600"><span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span> Alpa</span>
                    </div>
                </div>
            </div>
            <div class="relative h-[280px] w-full">
                <canvas id="chartMonthlyAttendance"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Chart Tren Rata-rata Keterlambatan per Divisi
    const ctxLate = document.getElementById('chartLateDivision');
    if (ctxLate) {
        new Chart(ctxLate, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartDivisionLabels ?? []) !!},
                datasets: [{
                    label: 'Rata-rata Terlambat (Menit)',
                    data: {!! json_encode($chartDivisionLateAvg ?? []) !!},
                    backgroundColor: 'rgba(79, 70, 229, 0.85)',
                    hoverBackgroundColor: 'rgba(67, 56, 202, 1)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                    borderRadius: 8,
                    barPercentage: 0.55
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Terlambat ' + context.raw + ' menit dari batas absen';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(243, 244, 246, 1)' },
                        ticks: { font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }

    // 2. Chart Perbandingan Kehadiran Bulanan
    const ctxMonthly = document.getElementById('chartMonthlyAttendance');
    if (ctxMonthly) {
        new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartMonths ?? []) !!},
                datasets: [
                    {
                        label: 'Hadir (Verified)',
                        data: {!! json_encode($chartHadirData ?? []) !!},
                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                        hoverBackgroundColor: 'rgba(5, 150, 105, 1)',
                        borderRadius: 6
                    },
                    {
                        label: 'Izin / Sakit',
                        data: {!! json_encode($chartIzinData ?? []) !!},
                        backgroundColor: 'rgba(245, 158, 11, 0.85)',
                        hoverBackgroundColor: 'rgba(217, 119, 6, 1)',
                        borderRadius: 6
                    },
                    {
                        label: 'Alpa / Tanpa Ket',
                        data: {!! json_encode($chartAlpaData ?? []) !!},
                        backgroundColor: 'rgba(239, 68, 68, 0.85)',
                        hoverBackgroundColor: 'rgba(220, 38, 38, 1)',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(243, 244, 246, 1)' },
                        ticks: { font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }
});
</script>
@endsection
