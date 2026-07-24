<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900">
            Laporan Akhir / Skripsi (PDF)
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Unggah file Laporan Akhir / Skripsi Anda sebagai syarat penerbitan Sertifikat Magang (Wajib untuk peserta Magang Magenta & Kemnaker).
        </p>
    </header>

    <div class="mt-4 p-4 rounded-xl border {{ auth()->user()->skripsi_status === 'approved' ? 'bg-emerald-50 border-emerald-200' : (auth()->user()->skripsi_status === 'rejected' ? 'bg-red-50 border-red-200' : (auth()->user()->skripsi_status === 'pending' ? 'bg-yellow-50 border-yellow-200' : 'bg-gray-50 border-gray-200')) }}">
        @if(auth()->user()->skripsi_status === 'approved')
            <div class="flex items-center gap-3 text-emerald-700">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <p class="font-bold">Laporan Disetujui</p>
                    <p class="text-xs mt-0.5">Laporan Anda telah diperiksa dan disetujui oleh Admin. Anda telah memenuhi syarat dokumen sertifikat.</p>
                </div>
            </div>
            <div class="mt-3 flex gap-2">
                <a href="{{ asset('storage/' . auth()->user()->skripsi_file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white text-gray-700 text-xs font-bold rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                    Lihat File Terunggah
                </a>
            </div>
        @elseif(auth()->user()->skripsi_status === 'pending')
            <div class="flex items-center gap-3 text-yellow-700">
                <svg class="w-6 h-6 shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <p class="font-bold">Menunggu Review Admin</p>
                    <p class="text-xs mt-0.5">File PDF Anda berhasil diunggah dan sedang menuggu persetujuan dari Admin.</p>
                </div>
            </div>
            <div class="mt-3 flex gap-2">
                <a href="{{ asset('storage/' . auth()->user()->skripsi_file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white text-gray-700 text-xs font-bold rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                    Lihat File Terunggah
                </a>
            </div>
        @else
            @if(auth()->user()->skripsi_status === 'rejected')
                <div class="flex items-start gap-3 text-red-700 mb-4">
                    <svg class="w-6 h-6 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p class="font-bold">Laporan Ditolak</p>
                        <p class="text-xs mt-0.5 mb-2">Laporan yang Anda unggah sebelumnya ditolak oleh Admin. Silakan perbaiki dan unggah ulang.</p>
                        @if(auth()->user()->skripsi_rejection_reason)
                            <div class="bg-white/60 p-3 rounded-lg border border-red-100 text-sm">
                                <span class="font-bold">Alasan Penolakan:</span><br>
                                {{ auth()->user()->skripsi_rejection_reason }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <form method="post" action="{{ route('profile.skripsi.upload') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="skripsi_file" value="Pilih File (PDF, Maks 10MB)" />
                    <input type="file" id="skripsi_file" name="skripsi_file" accept=".pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-gray-200 rounded-xl" required>
                    <x-input-error class="mt-2" :messages="$errors->get('skripsi_file')" />
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Unggah File
                    </button>

                    @if (session('status') === 'skripsi-uploaded')
                        <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 3000)"
                            class="text-sm text-emerald-600 font-medium"
                        >Berhasil diunggah.</p>
                    @endif
                </div>
            </form>
        @endif
    </div>
</section>
