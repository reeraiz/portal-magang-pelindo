@extends('layouts.app')
@section('header', 'Rekap Absensi')

@section('content')
<div class="space-y-6">
    <!-- Compact Stats Bar -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 overflow-hidden">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 divide-y sm:divide-y-0 sm:divide-x divide-gray-100" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem;">
            <!-- Kehadiran -->
            <div class="flex items-center justify-between pt-3 sm:pt-0 sm:pl-4 first:pt-0 first:pl-0">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Kehadiran</p>
                    <h3 class="text-3xl font-extrabold text-blue-600">{{ $totalHadir }} <span class="text-xs font-semibold text-gray-500">Hari</span></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 font-bold shrink-0" style="background-color: #eff6ff; color: #2563eb;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Izin -->
            <div class="flex items-center justify-between pt-3 sm:pt-0 sm:pl-4">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Izin</p>
                    <h3 class="text-3xl font-extrabold text-purple-600">{{ $totalIzin }} <span class="text-xs font-semibold text-gray-500">Hari</span></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 font-bold shrink-0" style="background-color: #f3e8ff; color: #9333ea;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Sakit -->
            <div class="flex items-center justify-between pt-3 sm:pt-0 sm:pl-4">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Sakit</p>
                    <h3 class="text-3xl font-extrabold text-rose-600">{{ $totalSakit ?? 0 }} <span class="text-xs font-semibold text-gray-500">Hari</span></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 font-bold shrink-0" style="background-color: #ffe4e6; color: #e11d48;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>

            <!-- Alpa -->
            <div class="flex items-center justify-between pt-3 sm:pt-0 sm:pl-4">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Alpa</p>
                    <h3 class="text-3xl font-extrabold text-red-600">{{ $totalAlpa }} <span class="text-xs font-semibold text-gray-500">Hari</span></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-600 font-bold shrink-0" style="background-color: #fee2e2; color: #dc2626;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col min-h-[500px]">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-gray-50/50">
            <div class="flex items-center gap-3">
                <h3 class="font-bold text-gray-800">Riwayat Kehadiran</h3>
                <button type="button" onclick="openPrintModal()" class="px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-xs font-bold transition-colors flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                    Cetak
                </button>
            </div>
            
            <form method="GET" action="{{ route('intern.rekap') }}" class="flex gap-2">
                <input type="month" name="month" class="text-sm border border-gray-200 rounded-lg text-gray-600 focus:ring-blue-500 focus:border-blue-500" value="{{ request('month', date('Y-m')) }}">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">Filter</button>
                @if(request('month'))
                <a href="{{ route('intern.rekap') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors">Reset</a>
                @endif
            </form>
        </div>
        
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 font-semibold sticky top-0 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Jam Masuk</th>
                        <th class="px-6 py-4">Jam Pulang</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Validasi</th>
                        <th class="px-6 py-4">Bukti & Lokasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attendances as $att)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d M Y') }}</td>
                        <td class="px-6 py-4">
                            @if($att->check_in)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-green-50 text-green-700 border border-green-100">
                                    {{ \Carbon\Carbon::parse($att->check_in)->format('H:i') }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($att->check_out)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ \Carbon\Carbon::parse($att->check_out)->format('H:i') }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($att->status === 'verified' || $att->status === 'hadir')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Hadir</span>
                            @elseif($att->status === 'pending')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700">Menunggu</span>
                            @elseif($att->status === 'izin')
                                @if(str_contains(strtolower($att->notes ?? ''), 'sakit'))
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">Sakit</span>
                                @elseif(str_contains(strtolower($att->notes ?? ''), 'cuti'))
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">Cuti</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700">Izin</span>
                                @endif
                            @elseif($att->status === 'rejected')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Ditolak</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Alpa</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($att->status === 'verified')
                                <span class="inline-flex items-center gap-1 text-green-600 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Terverifikasi
                                </span>
                            @elseif($att->status === 'pending')
                                <span class="inline-flex items-center gap-1 text-orange-500 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Menunggu
                                </span>
                            @elseif($att->status === 'rejected')
                                <span class="inline-flex items-center gap-1 text-red-500 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Ditolak
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-2">
                                @if($att->photo_in)
                                    <a href="{{ asset('storage/' . $att->photo_in) }}" target="_blank" class="text-blue-500 hover:text-blue-700 text-[11px] font-semibold flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Selfie Masuk
                                    </a>
                                @endif
                                @if($att->photo_out)
                                    <a href="{{ asset('storage/' . $att->photo_out) }}" target="_blank" class="text-orange-500 hover:text-orange-700 text-[11px] font-semibold flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Selfie Pulang
                                    </a>
                                @endif
                                
                                @if(!$att->photo_in && !$att->photo_out && !$att->lat_in && !$att->lat_out)
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Belum ada data kehadiran.
                            </div>
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

<!-- Print Absensi Modal -->
<div id="printModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closePrintModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form action="{{ route('intern.absensi.print') }}" method="GET" target="_blank" id="printForm" onsubmit="closePrintModal()">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="modal-title">Cetak Rekap Absensi</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="checkbox" id="print_all" name="print_all" value="1" onchange="toggleDateInputs(this)" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="print_all" class="text-sm font-semibold text-gray-700">Cetak Semua Absensi</label>
                        </div>
                        <div class="grid grid-cols-2 gap-4" id="date_inputs_container">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Mulai Tanggal</label>
                                <input type="date" name="start_date" id="print_start_date" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date" name="end_date" id="print_end_date" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Cetak
                    </button>
                    <button type="button" onclick="closePrintModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openPrintModal() {
        document.getElementById('printModal').classList.remove('hidden');
    }

    function closePrintModal() {
        document.getElementById('printModal').classList.add('hidden');
    }

    function toggleDateInputs(checkbox) {
        const container = document.getElementById('date_inputs_container');
        const startDate = document.getElementById('print_start_date');
        const endDate = document.getElementById('print_end_date');
        
        if (checkbox.checked) {
            container.classList.add('opacity-50', 'pointer-events-none');
            startDate.required = false;
            endDate.required = false;
            startDate.value = '';
            endDate.value = '';
        } else {
            container.classList.remove('opacity-50', 'pointer-events-none');
            startDate.required = true;
            endDate.required = true;
        }
    }
</script>
@endsection

