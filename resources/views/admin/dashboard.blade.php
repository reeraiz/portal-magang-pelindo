@extends('layouts.app')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-blue-600 mb-1 tracking-wi
                de uppercase">Total Intern</p>
                <h3 class="text-4xl font-extrabold text-gray-900">{{ number_format($totalInterns) }}</h3>
            </div>
            <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 border border-blue-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-emerald-600 mb-1 tracking-wide uppercase">Aktif Hari Ini</p>
                <h3 class="text-4xl font-extrabold text-gray-900">{{ number_format($activeInterns) }}</h3>
            </div>
            <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 border border-emerald-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-orange-500 mb-1 tracking-wide uppercase">Menunggu Review</p>
                <h3 class="text-4xl font-extrabold text-gray-900">{{ number_format($pendingAbsensi) }}</h3>
            </div>
            <div class="w-14 h-14 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500 border border-orange-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <!-- Recent Activities -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col min-h-[400px]">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
                <h3 class="font-bold text-gray-800 text-lg">Aktivitas Terbaru</h3>
                <a href="{{ route('admin.absensi') }}" class="text-sm text-blue-600 font-semibold hover:text-blue-800 transition-colors">Lihat Semua</a>
            </div>
            <div class="p-6 flex-1 space-y-6">
                @forelse($recentActivities as $activity)
                <div class="flex gap-4 items-start relative group">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0 border-2 border-white shadow-sm z-10">
                        <span class="font-bold text-blue-700 text-sm">{{ substr($activity->user->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 pb-6 border-b border-gray-50 group-last:border-0 group-last:pb-0">
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="font-bold text-gray-800 text-sm">{{ $activity->user->name }}</h4>
                            <span class="text-xs font-semibold text-gray-400">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-600">
                            @if(in_array($activity->status, ['izin', 'sakit']) || ($activity->status === 'pending' && !$activity->check_in && !$activity->check_out))
                                mengajukan <span class="font-semibold">Izin / Sakit</span> pada pukul {{ \Carbon\Carbon::parse($activity->created_at)->format('H:i') }}
                            @else
                                melakukan <span class="font-semibold">{{ $activity->check_in && !$activity->check_out ? 'Absen Masuk' : 'Absen Pulang' }}</span> pada pukul {{ \Carbon\Carbon::parse($activity->created_at)->format('H:i') }}
                            @endif
                        </p>
                    </div>
                </div>
                @empty
                <div class="h-full flex flex-col items-center justify-center text-gray-400 py-12">
                    <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-sm font-medium">Belum ada aktivitas terbaru.</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <!-- Logbook Overview -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="font-bold text-gray-800 text-lg mb-6">Ringkasan Logbook</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 rounded-xl bg-blue-50 border border-blue-100">
                        <span class="font-semibold text-sm text-gray-700">Total Logbook</span>
                        <span class="text-lg font-extrabold text-blue-600">{{ $totalLogbooks }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 rounded-xl bg-orange-50 border border-orange-100">
                        <span class="font-semibold text-sm text-gray-700">Menunggu Review</span>
                        <span class="text-lg font-extrabold text-orange-500">{{ $pendingLogbooks }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                        <span class="font-semibold text-sm text-gray-700">Disetujui</span>
                        <span class="text-lg font-extrabold text-emerald-600">{{ $verifiedLogbooks }}</span>
                    </div>
                </div>
            </div>

            <!-- Notifikasi Masa Magang Akan Berakhir -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 border border-orange-100 flex items-center justify-center text-orange-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="font-bold text-gray-800 text-lg">Selesai Magang</h3>
                    </div>
                    <span class="px-2.5 py-1 bg-orange-50 text-orange-600 rounded-full text-xs font-bold border border-orange-100">
                        {{ $endingSoonInterns->count() }} Intern
                    </span>
                </div>
                <p class="text-xs text-gray-500 mb-4">Daftar intern yang masa magangnya akan berakhir dalam kurun waktu 1 minggu ke depan.</p>
                
                <div class="space-y-3 max-h-[280px] overflow-y-auto pr-1 custom-scrollbar">
                    @forelse($endingSoonInterns as $intern)
                    <div class="bg-gray-50/70 hover:bg-orange-50/50 transition-colors p-3.5 rounded-xl border border-gray-100 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-9 h-9 rounded-full bg-orange-500 flex items-center justify-center font-bold text-white shrink-0 text-sm shadow-sm">
                                {{ substr($intern->name, 0, 1) }}
                            </div>
                            <div class="overflow-hidden">
                                <h4 class="font-bold text-gray-800 text-sm truncate">{{ $intern->name }}</h4>
                                <p class="text-xs text-gray-500 truncate">{{ $intern->division ?? 'Divisi Umum' }}</p>
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
                    <div class="py-8 text-center bg-gray-50/50 rounded-xl border border-gray-100">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-xs text-gray-500 font-medium">Tidak ada intern yang selesai magang dalam 1 minggu ke depan.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Intern (Tanpa Aksi Edit & Reset Password) -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Daftar Intern</h3>
                <p class="text-xs text-gray-500">Ringkasan data peserta magang beserta statistik absensi dan logbook.</p>
            </div>
            <a href="{{ route('admin.interns') }}" class="text-sm text-blue-600 font-semibold hover:text-blue-800 transition-colors">Kelola Intern</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Divisi</th>
                        <th class="px-6 py-4">Periode Magang</th>
                        <th class="px-6 py-4">Mentor</th>
                        <th class="px-6 py-4 text-center">Statistik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($interns as $intern)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 font-bold flex items-center justify-center shrink-0 border border-blue-100 text-base shadow-sm">
                                    {{ substr($intern->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">{{ $intern->name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $intern->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-gray-700">{{ $intern->division ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($intern->internship_start_date && $intern->internship_end_date)
                                <div class="text-sm font-semibold text-gray-800">{{ \Carbon\Carbon::parse($intern->internship_start_date)->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400">s/d {{ \Carbon\Carbon::parse($intern->internship_end_date)->format('d M Y') }}</div>
                            @else
                                <span class="text-xs text-gray-400 italic">Belum diatur</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($intern->mentor)
                                <span class="text-sm font-semibold text-gray-800">{{ $intern->mentor->name }}</span>
                                <span class="block text-[11px] text-gray-400">({{ $intern->mentor->role === 'pembimbing' ? 'Pembimbing Magang' : 'Admin' }})</span>
                            @else
                                <span class="text-xs text-orange-500 italic">Belum ada mentor</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex items-center gap-5">
                                <div class="text-center">
                                    <span class="block text-base font-extrabold text-blue-600">{{ $intern->attendances_count ?? 0 }}</span>
                                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Absensi</span>
                                </div>
                                <div class="w-px h-6 bg-gray-200"></div>
                                <div class="text-center">
                                    <span class="block text-base font-extrabold text-emerald-600">{{ $intern->logbooks_count ?? 0 }}</span>
                                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Logbook</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada intern terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
