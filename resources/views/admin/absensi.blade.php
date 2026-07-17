
@extends('layouts.app')
@section('header', 'Verifikasi Absensi')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-gray-500 mb-1 tracking-wide uppercase">Total Hadir</p>
                <h3 class="text-4xl font-extrabold text-gray-900">{{ $totalHadir }}</h3>
            </div>
            <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 border border-emerald-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-gray-500 mb-1 tracking-wide uppercase">Total Izin / Sakit</p>
                <h3 class="text-4xl font-extrabold text-gray-900">{{ $totalIzin }}</h3>
            </div>
            <div class="w-14 h-14 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 border border-purple-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><line x1="10" x2="14" y1="14" y2="18"/><line x1="14" x2="10" y1="14" y2="18"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-gray-500 mb-1 tracking-wide uppercase">Belum Verifikasi</p>
                <h3 class="text-4xl font-extrabold text-gray-900">{{ $totalPending }}</h3>
            </div>
            <div class="w-14 h-14 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500 border border-orange-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col min-h-[500px]">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-gray-50/50">
            <div>
                <h3 class="font-bold text-gray-800 text-base">Daftar Absensi</h3>
                <p class="text-xs text-gray-500 mt-0.5">Pantau dan verifikasi kehadiran harian peserta magang</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto justify-start lg:justify-end">
                <form method="GET" action="{{ route('admin.absensi') }}" class="flex flex-wrap items-center gap-2">
                    <input type="text" name="filter_name" placeholder="Cari nama intern..." class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 text-gray-600 focus:ring-blue-500 focus:border-blue-500 w-36 sm:w-48" value="{{ request('filter_name') }}">
                    <input type="date" name="filter_date" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 text-gray-600 focus:ring-blue-500 focus:border-blue-500" value="{{ request('filter_date') }}">
                    <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">Filter</button>
                    @if(request('filter_date') || request('filter_name'))
                    <a href="{{ route('admin.absensi') }}" class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors">Reset</a>
                    @endif
                </form>
                <div class="h-5 w-px bg-gray-300 hidden sm:block"></div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.absensi.export', request()->query()) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                        Export Excel
                    </a>
                    <a href="{{ route('admin.absensi.print', request()->query()) }}" target="_blank" class="px-3 py-1.5 bg-blue-900 hover:bg-blue-950 text-white rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                        Cetak Laporan Resmi
                    </a>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 font-semibold sticky top-0 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Intern</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Status & Keterangan</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attendances as $att)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0 border-2 border-white shadow-sm">
                                    <span class="font-bold text-blue-700 text-sm">{{ substr($att->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $att->user->name }}</p>
                                    <p class="text-xs text-gray-500 font-medium">{{ $att->user->division ?? 'Belum diatur' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500 font-medium mt-0.5">
                                {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '-' }} - {{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '-' }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            @if($att->status === 'verified')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 mb-1 inline-block">Terverifikasi</span>
                            @elseif($att->status === 'pending')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700 mb-1 inline-block">Menunggu</span>
                            @elseif($att->status === 'izin')
                                @if(str_contains(strtolower($att->notes ?? ''), 'sakit'))
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 mb-1 inline-block">Sakit</span>
                                @elseif(str_contains(strtolower($att->notes ?? ''), 'cuti'))
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 mb-1 inline-block">Cuti</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700 mb-1 inline-block">Izin</span>
                                @endif
                            @elseif($att->status === 'rejected')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 mb-1 inline-block">Ditolak</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 mb-1 inline-block">{{ ucfirst($att->status) }}</span>
                            @endif
                            <p class="text-xs text-gray-500 max-w-[200px] truncate" title="{{ $att->notes }}">{{ $att->notes ?? '-' }}</p>
                            @if($att->attachment)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $att->attachment) }}" target="_blank" class="text-blue-500 hover:text-blue-700 text-xs font-semibold flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    Lihat Lampiran
                                </a>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($att->status === 'pending')
                            <div class="flex gap-2">
                                <form action="{{ route('admin.verify.absensi', $att->id) }}" method="POST" class="confirm-form" data-confirm-msg="Yakin ingin menerima absensi/pengajuan izin ini?">
                                    @csrf
                                    <input type="hidden" name="status" value="verified">
                                    <button type="submit" class="px-4 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white border border-emerald-200 hover:border-emerald-600 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                        Terima
                                    </button>
                                </form>
                                <form action="{{ route('admin.verify.absensi', $att->id) }}" method="POST" class="confirm-form" data-confirm-msg="Yakin ingin menolak absensi/pengajuan izin ini?">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="px-4 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-200 hover:border-red-600 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                            @else
                            <span class="text-xs text-gray-400 font-medium italic">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data absensi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $attendances->links() }}
        </div>
    </div>
</div>
@endsection
