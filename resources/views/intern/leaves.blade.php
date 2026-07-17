@extends('layouts.app')
@section('header', 'Pengajuan Izin / Cuti')

@section('content')
<div class="space-y-6">
    <!-- Top Action Card -->
    <div class="relative overflow-hidden bg-blue-600 p-8 rounded-2xl shadow-lg text-white" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
        <!-- Decorative bg -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-blue-400 opacity-20 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="max-w-2xl text-left">
                <h2 class="text-3xl font-extrabold mb-2 text-white tracking-tight">Manajemen Izin & Cuti</h2>
                <p class="text-blue-100 text-sm md:text-base leading-relaxed">
                    Ajukan ketidakhadiran (Izin, Sakit, atau Cuti) dengan mudah. Pilih rentang tanggal dan sertakan lampiran bukti pendukung untuk proses persetujuan yang lebih cepat.
                </p>
            </div>
            <div class="shrink-0 w-full md:w-auto">
                <button type="button" onclick="document.getElementById('newLeaveModal').classList.remove('hidden')" class="w-full md:w-auto px-6 py-3 bg-white text-blue-700 font-bold rounded-xl shadow-md hover:bg-blue-50 hover:shadow-lg transition-all transform hover:-translate-y-1 flex justify-center items-center gap-2 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-600 focus:ring-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    Ajukan Izin Baru
                </button>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xl">
                {{ $totalLeaves }}
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500">Total Pengajuan</p>
                <h3 class="text-lg font-bold text-gray-800">Semua Izin/Sakit</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-600 font-bold text-xl" style="background-color: #fef9c3; color: #ca8a04;">
                {{ $pendingLeaves }}
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500">Menunggu Review</p>
                <h3 class="text-lg font-bold text-gray-800">Sedang Diproses</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600 font-bold text-xl">
                {{ $approvedLeaves }}
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500">Disetujui</p>
                <h3 class="text-lg font-bold text-gray-800">Izin Sah</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-indigo-100 shadow-sm flex items-center gap-4 relative overflow-hidden">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-xl">
                {{ max(0, 3 - ($usedQuota ?? 0)) }}
            </div>
            <div>
                <p class="text-xs font-semibold text-indigo-600">Sisa Kuota Bulan Ini</p>
                <h3 class="text-base font-bold text-gray-800">Maks 3 Hari/Bulan</h3>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-lg">Riwayat Pengajuan Izin Saya</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="p-4 pl-6">Jenis</th>
                        <th class="p-4">Periode Tanggal</th>
                        <th class="p-4">Durasi</th>
                        <th class="p-4">Keterangan / Alasan</th>
                        <th class="p-4">Lampiran</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 pr-6">Catatan Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($leaves as $leave)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 pl-6">
                            @if($leave->type === 'sakit')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-100">Sakit</span>
                            @elseif($leave->type === 'cuti')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-600 border border-purple-100">Cuti</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100">Izin</span>
                            @endif
                        </td>
                        <td class="p-4 font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}
                            <span class="text-gray-400 font-normal">s/d</span>
                            {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                        </td>
                        <td class="p-4 font-bold text-gray-700">
                            {{ $leave->duration_days }} Hari
                        </td>
                        <td class="p-4 text-gray-600 max-w-xs truncate" title="{{ $leave->notes }}">
                            {{ $leave->notes }}
                        </td>
                        <td class="p-4">
                            @if($leave->attachment)
                                <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 font-semibold text-xs bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                                    Lihat File
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($leave->status === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-100" style="background-color: #ecfdf5; color: #059669; border-color: #d1fae5;">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500" style="background-color: #10b981;"></span> Disetujui
                                </span>
                            @elseif($leave->status === 'rejected')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-100" style="background-color: #fef2f2; color: #dc2626; border-color: #fee2e2;">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500" style="background-color: #ef4444;"></span> Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100" style="background-color: #fffbeb; color: #d97706; border-color: #fef3c7;">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse" style="background-color: #f59e0b;"></span> Menunggu
                                </span>
                            @endif
                        </td>
                        <td class="p-4 pr-6 text-xs text-gray-500 italic">
                            {{ $leave->admin_note ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-400">
                            Belum ada riwayat pengajuan izin/cuti.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $leaves->links() }}
        </div>
    </div>
</div>

<!-- Modal Pengajuan Izin Baru -->
<div id="newLeaveModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('newLeaveModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form action="{{ route('intern.leaves.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="modal-title">Ajukan Izin / Cuti / Sakit</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Ketidakhadiran</label>
                            <select name="type" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="izin">Izin (Keperluan Pribadi/Keluarga)</option>
                                <option value="sakit">Sakit (Sertakan Surat Dokter)</option>
                                <option value="cuti">Cuti Magang</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date" name="start_date" required class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Selesai</label>
                                <input type="date" name="end_date" required class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 transition-colors">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan / Alasan Lengkap</label>
                            <textarea name="notes" required rows="3" placeholder="Jelaskan secara rinci alasan izin/cuti Anda..." class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 transition-colors resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Lampiran Bukti Pendukung <span class="text-xs text-gray-400 font-normal">(Opsional / Surat Dokter)</span></label>
                            <input type="file" name="attachment" accept="image/*,.pdf" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2 bg-gray-50 focus:bg-white transition-colors">
                            <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, atau PDF (Maks 2MB).</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-5 py-2.5 bg-blue-600 text-base font-bold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors cursor-pointer">
                        Kirim Pengajuan
                    </button>
                    <button type="button" onclick="document.getElementById('newLeaveModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
