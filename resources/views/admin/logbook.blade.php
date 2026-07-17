@extends('layouts.app')
@section('header', 'Review Logbook')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-gray-500 mb-1 tracking-wide uppercase">Total Logbook</p>
                <h3 class="text-4xl font-extrabold text-gray-900">{{ $totalLogbooks }}</h3>
            </div>
            <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 border border-blue-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-orange-500 mb-1 tracking-wide uppercase">Menunggu Review</p>
                <h3 class="text-4xl font-extrabold text-gray-900">{{ $pendingReviews }}</h3>
            </div>
            <div class="w-14 h-14 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500 border border-orange-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-emerald-600 mb-1 tracking-wide uppercase">Disetujui</p>
                <h3 class="text-4xl font-extrabold text-gray-900">{{ $approvedLogs }}</h3>
            </div>
            <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 border border-emerald-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col min-h-[500px]">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-gray-50/50">
            <div>
                <h3 class="font-bold text-gray-800">Riwayat Logbook Semua Intern</h3>
                @if($pendingReviews > 0)
                <div class="flex items-center gap-2 mt-2">
                    <button type="button" onclick="openBulkReviewModal('approve_all')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold transition-colors shadow-sm flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Setujui Semua Pending ({{ $pendingReviews }})
                    </button>
                    <button type="button" onclick="openBulkReviewModal('reject_all')" class="px-3 py-1.5 bg-red-50 hover:bg-red-600 text-red-700 hover:text-white border border-red-200 hover:border-red-600 rounded-lg text-[11px] font-bold transition-colors shadow-sm flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Tolak Semua Pending ({{ $pendingReviews }})
                    </button>
                </div>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto justify-start lg:justify-end mt-3 lg:mt-0">
                <form method="GET" action="{{ route('admin.logbook') }}" class="flex flex-wrap items-center gap-2">
                    <input type="text" name="filter_name" placeholder="Cari nama intern..." class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 text-gray-600 focus:ring-blue-500 focus:border-blue-500 w-36 sm:w-48" value="{{ request('filter_name') }}">
                    <input type="date" name="filter_date" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 text-gray-600 focus:ring-blue-500 focus:border-blue-500" value="{{ request('filter_date') }}">
                    <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">Filter</button>
                    @if(request('filter_date') || request('filter_name'))
                    <a href="{{ route('admin.logbook') }}" class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors">Reset</a>
                    @endif
                </form>
                <div class="h-5 w-px bg-gray-300 hidden sm:block"></div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.logbook.export', request()->query()) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                        Export Excel
                    </a>
                    <a href="{{ route('admin.logbook.print', request()->query()) }}" target="_blank" class="px-3 py-1.5 bg-blue-900 hover:bg-blue-950 text-white rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
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
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4 max-w-[300px]">Aktivitas</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logbooks as $log)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0 border-2 border-white shadow-sm">
                                    <span class="font-bold text-blue-700 text-sm">{{ substr($log->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $log->user->name }}</p>
                                    <p class="text-xs text-gray-500 font-medium">{{ $log->user->division ?? 'Belum diatur' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800">
                            {{ \Carbon\Carbon::parse($log->date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 max-w-[300px] whitespace-normal">
                            <p class="font-bold text-gray-800 text-sm mb-1">{{ $log->title }}</p>
                            <p class="text-xs text-gray-600 line-clamp-2" title="{{ $log->description }}">{{ $log->description }}</p>
                            
                            @if(count($log->attachments_list) > 0)
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($log->attachments_list as $idx => $img)
                                <a href="{{ asset('storage/' . $img) }}" target="_blank" class="px-2 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded text-[11px] font-bold flex items-center gap-1 border border-blue-200 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    Foto {{ $idx + 1 }}
                                </a>
                                @endforeach
                            </div>
                            @endif

                            @if($log->feedback)
                            <div class="mt-2 bg-blue-50/50 border border-blue-100 rounded-lg p-2">
                                <span class="text-xs font-bold text-gray-600">Feedback:</span>
                                <p class="text-xs text-gray-700 italic">"{{ $log->feedback }}"</p>
                                @if($log->grade)
                                <span class="text-xs font-extrabold text-blue-700 bg-blue-100 px-2 py-0.5 rounded mt-1 inline-block">Nilai: {{ $log->grade }}</span>
                                @endif
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($log->status === 'verified')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Disetujui</span>
                            @elseif($log->status === 'pending')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700">Menunggu</span>
                            @elseif($log->status === 'rejected')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <button type="button" onclick="openReviewModal({{ $log->id }}, '{{ addslashes($log->title) }}', '{{ addslashes($log->feedback) }}')" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white border border-blue-200 hover:border-blue-600 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                Review
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Belum ada logbook yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $logbooks->links() }}
        </div>
    </div>
</div>

<!-- Review Logbook Modal -->
<div id="reviewModal" class="fixed inset-0 z-50 hidden" aria-labelledby="review-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeReviewModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form id="reviewForm" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-1" id="review-modal-title">Review Logbook</h3>
                    <p class="text-sm text-gray-500 mb-4" id="review-logbook-title"></p>
                    <div class="space-y-4">

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Komentar / Feedback <span class="text-xs text-gray-400 font-normal">(Opsional)</span></label>
                            <textarea name="feedback" id="review-feedback" rows="3" placeholder="Tulis feedback untuk intern..." class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-col sm:flex-row-reverse gap-2">
                    <button type="submit" name="action" value="approve" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:text-sm transition-colors">
                        Setujui
                    </button>
                    <button type="submit" name="action" value="reject" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-red-300 shadow-sm px-4 py-2 bg-red-50 text-base font-medium text-red-700 hover:bg-red-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition-colors">
                        Tolak
                    </button>
                    <button type="button" onclick="closeReviewModal()" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Review Logbook Modal -->
<div id="bulkReviewModal" class="fixed inset-0 z-50 hidden" aria-labelledby="bulk-review-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeBulkReviewModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form id="bulkReviewForm" action="{{ route('admin.verify.logbook.bulk') }}" method="POST">
                @csrf
                <input type="hidden" name="action" id="bulk-review-action">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-1" id="bulk-review-modal-title">Bulk Review Logbook</h3>
                    <p class="text-sm text-gray-500 mb-4" id="bulk-review-modal-desc"></p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Komentar / Feedback <span class="text-xs text-gray-400 font-normal">(Opsional)</span></label>
                            <textarea name="feedback" rows="3" placeholder="Tulis feedback masal..." class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-col sm:flex-row-reverse gap-2">
                    <button type="submit" id="bulk-submit-btn" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white sm:text-sm transition-colors">
                        Konfirmasi
                    </button>
                    <button type="button" onclick="closeBulkReviewModal()" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openReviewModal(id, title, feedback) {
        document.getElementById('reviewForm').action = '{{ url("admin/verify-logbook") }}/' + id;
        document.getElementById('review-logbook-title').textContent = title;
        document.getElementById('review-feedback').value = feedback || '';
        document.getElementById('reviewModal').classList.remove('hidden');
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
    }

    function openBulkReviewModal(action) {
        document.getElementById('bulk-review-action').value = action;
        const title = document.getElementById('bulk-review-modal-title');
        const desc = document.getElementById('bulk-review-modal-desc');
        const submitBtn = document.getElementById('bulk-submit-btn');

        if (action === 'approve_all') {
            title.textContent = 'Setujui Semua Logbook Pending';
            desc.textContent = 'Apakah Anda yakin ingin menyetujui semua logbook pending? Anda dapat memberikan catatan/feedback masal (opsional) di bawah.';
            submitBtn.className = 'w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:text-sm transition-colors';
        } else {
            title.textContent = 'Tolak Semua Logbook Pending';
            desc.textContent = 'Apakah Anda yakin ingin menolak semua logbook pending? Anda dapat memberikan alasan penolakan masal (opsional) di bawah.';
            submitBtn.className = 'w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition-colors';
        }

        document.getElementById('bulkReviewModal').classList.remove('hidden');
    }

    function closeBulkReviewModal() {
        document.getElementById('bulkReviewModal').classList.add('hidden');
    }
</script>
@endsection
