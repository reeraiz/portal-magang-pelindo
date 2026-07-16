@extends('layouts.app')
@section('header', 'Logbook Magang')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Form Section -->
    <div class="lg:col-span-4">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/></svg>
                Tambah Logbook Baru
            </h3>
            
            <form action="{{ route('intern.logbook.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" onsubmit="return handleLogbookSubmit(this);">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Aktivitas</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <p class="text-xs text-gray-400 mt-1">Lupa input kemarin? Pilih tanggal yang sesuai.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Aktivitas</label>
                    <input type="text" name="title" required placeholder="Contoh: Mengerjakan Modul Login" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Aktivitas</label>
                    <textarea name="description" required rows="4" placeholder="Jelaskan detail aktivitas yang Anda lakukan hari ini..." class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Bukti <span class="text-xs text-gray-400 font-normal">(Bisa pilih lebih dari 1 foto)</span></label>
                    <div class="relative">
                        <input type="file" name="attachments[]" multiple accept="image/*" id="attachment-input" class="hidden" onchange="previewImages(event)">
                        <label for="attachment-input" class="w-full flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:border-blue-300 cursor-pointer transition-all duration-200 group">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 group-hover:bg-blue-100 transition-colors shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-600 group-hover:text-blue-600 transition-colors" id="file-label">Klik untuk upload foto</p>
                                <p class="text-xs text-gray-400">Bisa pilih beberapa foto (JPG, PNG, maks 10MB)</p>
                            </div>
                        </label>
                        <div id="image-preview-container" class="hidden mt-3 relative">
                            <div id="image-preview-grid" class="grid grid-cols-2 gap-2"></div>
                            <button type="button" onclick="removeImages()" class="mt-2 text-xs text-red-600 hover:text-red-800 font-semibold flex items-center gap-1">&times; Hapus semua foto terpilih</button>
                        </div>
                    </div>
                </div>
                
                <button type="submit" id="logbook-submit-btn" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors mt-2">
                    Simpan Logbook
                </button>
            </form>

            <script>
                let createLogbookTransfer = new DataTransfer();

                function previewImages(event) {
                    const newFiles = event.target.files;
                    if (newFiles && newFiles.length > 0) {
                        Array.from(newFiles).forEach(file => {
                            if (file.size > 10 * 1024 * 1024) {
                                Swal.fire('File Terlalu Besar', `Foto "${file.name}" melebihi batas maksimal 10MB.`, 'warning');
                                return;
                            }
                            createLogbookTransfer.items.add(file);
                        });
                        updateCreateImageInput();
                    }
                }

                function removeCreateImage(index) {
                    const dt = new DataTransfer();
                    const files = createLogbookTransfer.files;
                    for (let i = 0; i < files.length; i++) {
                        if (i !== index) dt.items.add(files[i]);
                    }
                    createLogbookTransfer = dt;
                    updateCreateImageInput();
                }

                function removeImages() {
                    createLogbookTransfer = new DataTransfer();
                    updateCreateImageInput();
                }

                function updateCreateImageInput() {
                    const input = document.getElementById('attachment-input');
                    input.files = createLogbookTransfer.files;
                    
                    const container = document.getElementById('image-preview-container');
                    const grid = document.getElementById('image-preview-grid');
                    grid.innerHTML = '';
                    
                    if (createLogbookTransfer.files.length > 0) {
                        document.getElementById('file-label').textContent = createLogbookTransfer.files.length + ' foto terpilih';
                        container.classList.remove('hidden');
                        
                        Array.from(createLogbookTransfer.files).forEach((file, index) => {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const div = document.createElement('div');
                                div.className = 'relative rounded-lg overflow-hidden border border-gray-200 group/img';
                                div.innerHTML = `
                                    <img src="${e.target.result}" alt="Preview" class="w-full h-24 object-cover">
                                    <button type="button" onclick="removeCreateImage(${index})" class="absolute top-1 right-1 bg-red-600/90 hover:bg-red-700 text-white rounded-full p-1 shadow transition-colors" title="Hapus foto ini">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                    <div class="absolute bottom-0 inset-x-0 bg-black/60 text-white text-[10px] truncate px-1.5 py-0.5">${file.name}</div>
                                `;
                                grid.appendChild(div);
                            };
                            reader.readAsDataURL(file);
                        });
                    } else {
                        container.classList.add('hidden');
                        document.getElementById('file-label').textContent = 'Klik untuk upload foto';
                    }
                }

                function handleLogbookSubmit(form) {
                    const btn = document.getElementById('logbook-submit-btn');
                    if (btn.dataset.submitting === 'true') return false;
                    btn.dataset.submitting = 'true';
                    btn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...';
                    btn.classList.add('opacity-75', 'pointer-events-none');
                    return true;
                }
            </script>
        </div>
    </div>

    <!-- List Section -->
    <div class="lg:col-span-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 min-h-[500px] flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-800">Riwayat Logbook</h3>
                <button type="button" onclick="openPrintModal()" class="px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-xs font-bold transition-colors flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                    Cetak
                </button>
            </div>
            
            <div class="flex-1 space-y-4">
                @forelse($logbooks as $logbook)
                <div class="p-5 rounded-xl border border-gray-100 hover:border-blue-100 hover:shadow-md transition-all duration-200 group relative">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex gap-4 flex-1 min-w-0">
                            <!-- Date Badge -->
                            <div class="w-14 h-14 rounded-lg bg-gray-50 border border-gray-100 flex flex-col items-center justify-center shrink-0">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ \Carbon\Carbon::parse($logbook->date)->format('M') }}</span>
                                <span class="text-xl font-bold text-blue-600 leading-none mt-0.5">{{ \Carbon\Carbon::parse($logbook->date)->format('d') }}</span>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <h4 class="font-bold text-gray-800 text-base truncate">{{ $logbook->title }}</h4>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-500 shrink-0">{{ $logbook->time }}</span>
                                </div>
                                <p class="text-sm text-gray-600 leading-relaxed">{{ $logbook->description }}</p>
                                
                                @if(count($logbook->attachments_list) > 0)
                                <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @foreach($logbook->attachments_list as $img)
                                    <a href="{{ asset('storage/' . $img) }}" target="_blank" class="block overflow-hidden rounded-lg border border-gray-200 hover:opacity-90 transition-opacity">
                                        <img src="{{ asset('storage/' . $img) }}" alt="Foto Bukti" class="w-full h-28 object-cover">
                                    </a>
                                    @endforeach
                                </div>
                                @endif
                                
                                @if($logbook->grade || $logbook->feedback)
                                <div class="mt-3 bg-blue-50/50 border border-blue-100 rounded-lg p-3">
                                    @if($logbook->grade)
                                    <div class="mb-1 flex items-center gap-2">
                                        <span class="text-xs font-bold text-gray-600">Nilai:</span>
                                        <span class="text-xs font-extrabold text-blue-700 bg-blue-100 px-2 py-0.5 rounded">{{ $logbook->grade }}</span>
                                    </div>
                                    @endif
                                    @if($logbook->feedback)
                                    <div>
                                        <span class="text-xs font-bold text-gray-600 block mb-0.5">Komentar Mentor:</span>
                                        <p class="text-xs text-gray-700 italic">"{{ $logbook->feedback }}"</p>
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Status Badge & Actions -->
                        <div class="shrink-0 flex flex-col items-end gap-2 mt-1">
                            @if($logbook->status === 'verified')
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-100 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Disetujui
                            </span>
                            @elseif($logbook->status === 'pending')
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-orange-50 text-orange-600 border border-orange-100 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Menunggu
                            </span>
                            @elseif($logbook->status === 'rejected')
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-100 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Ditolak
                            </span>
                            @endif
                            <div class="flex items-center gap-2 mt-2">
                                @if($logbook->status !== 'verified')
                                <button type="button" onclick='openEditModal({{ $logbook->id }}, @json($logbook->date), @json($logbook->title), @json($logbook->description), @json($logbook->attachments_list))' class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('intern.logbook.destroy', $logbook->id) }}" method="POST" class="inline confirm-form" data-confirm-msg="Apakah Anda yakin ingin menghapus logbook ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-12 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h4 class="font-bold text-gray-800">Belum ada Logbook</h4>
                    <p class="text-sm text-gray-500 mt-1">Anda belum mencatat aktivitas apapun.</p>
                </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($logbooks->hasPages())
            <div class="mt-6 pt-4 border-t border-gray-100">
                {{ $logbooks->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Logbook Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeEditModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="modal-title">Edit Logbook</h3>
                    <div class="space-y-4">
                        <input type="hidden" name="date" id="edit-date">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Aktivitas</label>
                            <input type="text" name="title" id="edit-title" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Aktivitas</label>
                            <textarea name="description" id="edit-description" required rows="4" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Saat Ini <span class="text-xs text-gray-400 font-normal">(Klik tombol Hapus jika ingin menghapus foto lama)</span></label>
                            <div id="edit-existing-photos" class="grid grid-cols-3 gap-2 mb-3"></div>
                            <div id="edit-deleted-photos-inputs"></div>

                            <label class="block text-sm font-semibold text-gray-700 mb-1 mt-4">Tambah Foto Baru <span class="text-xs text-gray-400 font-normal">(Maks 10MB per foto)</span></label>
                            <div class="relative">
                                <input type="file" name="attachments[]" multiple accept="image/*" id="edit-attachment-input" class="hidden" onchange="previewEditImages(event)">
                                <label for="edit-attachment-input" class="w-full flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:border-blue-300 cursor-pointer transition-all duration-200 group">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 group-hover:bg-blue-100 transition-colors shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600 group-hover:text-blue-600 transition-colors" id="edit-file-label">Klik untuk upload foto baru</p>
                                        <p class="text-[11px] text-gray-400">JPG, PNG maks 10MB</p>
                                    </div>
                                </label>
                                <div id="edit-image-preview-container" class="hidden mt-3 relative">
                                    <div id="edit-image-preview-grid" class="grid grid-cols-2 gap-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let editLogbookTransfer = new DataTransfer();

    function openEditModal(id, date, title, description, attachments) {
        document.getElementById('editForm').action = '{{ url("intern/logbook") }}/' + id;
        document.getElementById('edit-date').value = date;
        document.getElementById('edit-title').value = title;
        document.getElementById('edit-description').value = description;
        
        // Render existing photos
        const existingContainer = document.getElementById('edit-existing-photos');
        const deletedInputs = document.getElementById('edit-deleted-photos-inputs');
        existingContainer.innerHTML = '';
        deletedInputs.innerHTML = '';

        if (attachments && Array.isArray(attachments) && attachments.length > 0) {
            attachments.forEach((path, idx) => {
                const div = document.createElement('div');
                div.className = 'relative rounded-lg overflow-hidden border border-gray-200 group/img h-20';
                div.id = `existing-photo-${idx}`;
                div.innerHTML = `
                    <img src="{{ asset('storage') }}/${path}" class="w-full h-full object-cover">
                    <button type="button" onclick="markDeleteExistingPhoto('${path}', ${idx})" class="absolute top-1 right-1 bg-red-600/90 hover:bg-red-700 text-white rounded-full px-1.5 py-0.5 text-[10px] font-bold shadow transition-colors" title="Hapus foto ini">
                        &times; Hapus
                    </button>
                `;
                existingContainer.appendChild(div);
            });
        } else {
            existingContainer.innerHTML = '<p class="text-xs text-gray-400 italic col-span-3">Tidak ada foto terlampir sebelumnya.</p>';
        }

        // Reset new file input
        editLogbookTransfer = new DataTransfer();
        updateEditImageInput();

        document.getElementById('editModal').classList.remove('hidden');
    }

    function markDeleteExistingPhoto(path, idx) {
        const deletedInputs = document.getElementById('edit-deleted-photos-inputs');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_photos[]';
        input.value = path;
        deletedInputs.appendChild(input);

        const card = document.getElementById(`existing-photo-${idx}`);
        if (card) {
            card.innerHTML += `<div class="absolute inset-0 bg-red-900/70 flex items-center justify-center text-white text-xs font-bold">Terhapus</div>`;
        }
    }

    function previewEditImages(event) {
        const newFiles = event.target.files;
        if (newFiles && newFiles.length > 0) {
            Array.from(newFiles).forEach(file => {
                if (file.size > 10 * 1024 * 1024) {
                    Swal.fire('File Terlalu Besar', `Foto "${file.name}" melebihi batas maksimal 10MB.`, 'warning');
                    return;
                }
                editLogbookTransfer.items.add(file);
            });
            updateEditImageInput();
        }
    }

    function removeEditImage(index) {
        const dt = new DataTransfer();
        const files = editLogbookTransfer.files;
        for (let i = 0; i < files.length; i++) {
            if (i !== index) dt.items.add(files[i]);
        }
        editLogbookTransfer = dt;
        updateEditImageInput();
    }

    function updateEditImageInput() {
        const input = document.getElementById('edit-attachment-input');
        input.files = editLogbookTransfer.files;
        
        const container = document.getElementById('edit-image-preview-container');
        const grid = document.getElementById('edit-image-preview-grid');
        grid.innerHTML = '';
        
        if (editLogbookTransfer.files.length > 0) {
            document.getElementById('edit-file-label').textContent = editLogbookTransfer.files.length + ' foto baru terpilih';
            container.classList.remove('hidden');
            
            Array.from(editLogbookTransfer.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative rounded-lg overflow-hidden border border-gray-200 group/img';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="w-full h-20 object-cover">
                        <button type="button" onclick="removeEditImage(${index})" class="absolute top-1 right-1 bg-red-600/90 hover:bg-red-700 text-white rounded-full p-1 shadow transition-colors" title="Batal upload foto ini">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                        <div class="absolute bottom-0 inset-x-0 bg-black/60 text-white text-[10px] truncate px-1 py-0.5">${file.name}</div>
                    `;
                    grid.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        } else {
            container.classList.add('hidden');
            document.getElementById('edit-file-label').textContent = 'Klik untuk upload foto baru';
        }
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

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

<!-- Print Logbook Modal -->
<div id="printModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closePrintModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form action="{{ route('intern.logbook.print') }}" method="GET" target="_blank" id="printForm" onsubmit="closePrintModal()">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="modal-title">Cetak Logbook</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="checkbox" id="print_all" name="print_all" value="1" onchange="toggleDateInputs(this)" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="print_all" class="text-sm font-semibold text-gray-700">Cetak Semua Logbook</label>
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
@endsection
