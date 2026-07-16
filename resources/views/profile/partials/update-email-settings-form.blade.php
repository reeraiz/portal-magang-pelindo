<section>
    <header class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">
            Pengaturan Email (Sertifikat Magang)
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Atur nama pengirim dan alamat email yang akan muncul saat peserta menerima email sertifikat.
        </p>
    </header>

    <form method="post" action="{{ route('admin.settings.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="mail_from_address" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Email Pengirim (From Address)</label>
            <input id="mail_from_address" name="mail_from_address" type="email" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" value="{{ old('mail_from_address', $mailFromAddress) }}" required autocomplete="off" />
            <p class="text-xs text-gray-500 mt-2">Alamat email yang digunakan untuk mengirim pesan.</p>
            @error('mail_from_address')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="mail_password" class="block text-sm font-semibold text-gray-700 mb-1">Password Aplikasi (App Password)</label>
            <input id="mail_password" name="mail_password" type="password" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Biarkan kosong jika tidak ingin mengubah password" autocomplete="new-password" />
            <p class="text-xs text-gray-500 mt-2">Jika menggunakan Gmail, masukkan <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-blue-600 font-bold hover:underline">App Password</a> dari email tersebut.</p>
            @error('mail_password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="mail_from_name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Pengirim (From Name)</label>
            <input id="mail_from_name" name="mail_from_name" type="text" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" value="{{ old('mail_from_name', $mailFromName) }}" required autocomplete="off" />
            <p class="text-xs text-gray-500 mt-2">Nama ini akan terlihat di inbox email peserta. Contoh: <strong>"Pelindo"</strong> atau <strong>"HRD Pelindo"</strong>.</p>
            @error('mail_from_name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-gray-800 text-white font-bold rounded-xl text-sm hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors shadow-sm">
                Simpan Pengaturan
            </button>

            @if (session('status') === 'settings-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg"
                >Berhasil diperbarui.</p>
            @endif
        </div>
    </form>
</section>
