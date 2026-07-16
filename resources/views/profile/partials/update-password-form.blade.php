<section>
    <header class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">
            Perbarui Password
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Pastikan akun Anda menggunakan kata sandi panjang dan acak agar tetap aman.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-semibold text-gray-700 mb-1">Password Saat Ini</label>
            <input id="update_password_current_password" name="current_password" type="password" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-sm text-red-600" />
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-semibold text-gray-700 mb-1">Password Baru</label>
            <input id="update_password_password" name="password" type="password" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-sm text-red-600" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password Baru</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-sm text-red-600" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-gray-800 text-white font-bold rounded-xl text-sm hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors shadow-sm">
                Simpan Password
            </button>

            @if (session('status') === 'password-updated')
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
