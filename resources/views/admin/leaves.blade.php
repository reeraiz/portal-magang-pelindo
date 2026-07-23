@extends('layouts.app')
@section('header', 'Verifikasi Izin & Cuti Intern')

@section('content')
<div class="space-y-6">
    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xl">
                {{ $totalLeaves }}
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500">Total Pengajuan</p>
                <h3 class="text-lg font-bold text-gray-800">Semua Masuk</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-600 font-bold text-xl" style="background-color: #fefce8; color: #ca8a04;">
                {{ $pendingReviews }}
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500">Menunggu Review</p>
                <h3 class="text-lg font-bold text-gray-800">Perlu Diproses</h3>
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
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <h3 class="font-bold text-gray-800 text-base">Daftar Pengajuan Izin / Cuti</h3>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">{{ $leaves->total() }}</span>
        </div>
        <form method="GET" action="{{ route('admin.leaves') }}" class="flex flex-wrap items-center gap-2">
            <input type="text" name="filter_name" value="{{ request('filter_name') }}" placeholder="Cari nama intern..." class="text-sm border border-gray-200 rounded-xl px-3 py-1.5 text-gray-600 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
            <input type="date" name="filter_date" value="{{ request('filter_date') }}" class="text-sm border border-gray-200 rounded-xl px-3 py-1.5 text-gray-600 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
            
            <select name="filter_division" id="filter_division" onchange="filterDepartments('filter_division', 'filter_department'); this.form.submit()" class="text-sm border border-gray-200 rounded-xl px-3 py-1.5 text-gray-600 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                <option value="">Semua Divisi</option>
                @foreach($divisions as $div)
                    <option value="{{ $div->name }}" {{ request('filter_division') == $div->name ? 'selected' : '' }}>{{ $div->name }}</option>
                @endforeach
            </select>
            
            <select name="filter_department" id="filter_department" data-selected="{{ request('filter_department') }}" onchange="this.form.submit()" class="text-sm border border-gray-200 rounded-xl px-3 py-1.5 text-gray-600 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 min-w-[140px]">
                <option value="">Semua Departemen</option>
            </select>
            <button type="submit" class="px-4 py-1.5 bg-gray-800 text-white rounded-xl text-sm font-semibold hover:bg-gray-900 transition-colors cursor-pointer">Filter</button>
            @if(request()->hasAny(['filter_date', 'filter_name', 'filter_division', 'filter_department']))
                <a href="{{ route('admin.leaves') }}" class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-xl text-sm font-semibold hover:bg-gray-200 transition-colors">Reset</a>
            @endif
        </form>
    </div>


    <!-- Verification Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="p-4 pl-6">Intern</th>
                        <th class="p-4">Jenis</th>
                        <th class="p-4">Periode Tanggal</th>
                        <th class="p-4">Durasi</th>
                        <th class="p-4">Alasan / Keterangan</th>
                        <th class="p-4">Lampiran</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 pr-6 text-center">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($leaves as $leave)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 pl-6">
                            <div class="font-bold text-gray-800">{{ $leave->user->name ?? 'User Hapus' }}</div>
                            <div class="text-xs text-gray-400">{{ $leave->user->division ?? 'Divisi Umum' }}</div>
                        </td>
                        <td class="p-4">
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
                                    Lihat Bukti
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
                        <td class="p-4 pr-6 text-center">
                            @if($leave->status === 'pending')
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('admin.verify.leave', $leave->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white font-bold text-xs rounded-xl shadow-sm transition-colors cursor-pointer" style="background-color: #10b981; color: #ffffff; border: 1px solid #059669;">
                                            Setujui
                                        </button>
                                    </form>
                                    <button type="button" onclick="document.getElementById('rejectModal-{{ $leave->id }}').classList.remove('hidden')" class="px-3 py-1.5 bg-red-50 hover:bg-red-500 text-red-600 hover:text-white border border-red-200 font-bold text-xs rounded-xl transition-all cursor-pointer" style="background-color: #fef2f2; color: #dc2626; border: 1px solid #fca5a5;">
                                        Tolak
                                    </button>
                                </div>

                                <!-- Modal Tolak dengan Catatan -->
                                <div id="rejectModal-{{ $leave->id }}" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('rejectModal-{{ $leave->id }}').classList.add('hidden')"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                                            <form action="{{ route('admin.verify.leave', $leave->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-2">Alasan Penolakan Izin</h3>
                                                    <p class="text-xs text-gray-500 mb-4">Berikan alasan kepada intern mengapa pengajuan izin/cuti ini ditolak.</p>
                                                    <textarea name="admin_note" required rows="3" placeholder="Contoh: Tanggal bertabrakan dengan jadwal event penting divisi..." class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-red-500 transition-colors resize-none"></textarea>
                                                </div>
                                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-bold text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors cursor-pointer">
                                                        Tolak Pengajuan
                                                    </button>
                                                    <button type="button" onclick="document.getElementById('rejectModal-{{ $leave->id }}').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                                        Batal
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Selesai Diverifikasi</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-400">
                            Belum ada pengajuan izin/cuti dari intern.
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
</div>

<script>
    const divisionsData = @json($divisions);

    function filterDepartments(divisionSelectId, departmentSelectId) {
        const divSelect = document.getElementById(divisionSelectId);
        const deptSelect = document.getElementById(departmentSelectId);
        const selectedDivName = divSelect.value;
        const currentDept = deptSelect.getAttribute('data-selected') || deptSelect.value;
        
        deptSelect.innerHTML = '<option value="">Semua Departemen</option>';
        
        if (!selectedDivName) return;
        
        const selectedDiv = divisionsData.find(d => d.name === selectedDivName);
        if (selectedDiv && selectedDiv.departments) {
            selectedDiv.departments.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.name;
                option.textContent = dept.name;
                if (dept.name === currentDept) {
                    option.selected = true;
                }
                deptSelect.appendChild(option);
            });
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('filter_division').value) {
            filterDepartments('filter_division', 'filter_department');
        }
    });
</script>
@endsection
