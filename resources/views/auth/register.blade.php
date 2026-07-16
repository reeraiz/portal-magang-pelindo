<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Nama Lengkap') }}</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                </div>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-[#0A1128] focus:border-[#0A1128] sm:text-sm transition duration-150 ease-in-out" placeholder="Nama Lengkap">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Email') }}</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" /><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" /></svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-[#0A1128] focus:border-[#0A1128] sm:text-sm transition duration-150 ease-in-out" placeholder="you@example.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Divisi (Optional) -->
        <div>
            <label for="division" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Divisi / Departemen Magang') }} <span class="text-xs text-gray-400 font-normal">(Opsional)</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5Z"/><path d="M12 14c-4.42 0-8 3.58-8 8h16c0-4.42-3.58-8-8-8Z"/></svg>
                </div>
                <select id="division" name="division" class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl leading-5 bg-gray-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-[#0A1128] focus:border-[#0A1128] sm:text-sm transition duration-150 ease-in-out">
                    <option value="">-- Pilih Divisi / Departemen --</option>
                    @foreach(\App\Models\Division::orderBy('name')->get() as $div)
                        <option value="{{ $div->name }}" {{ old('division') == $div->name ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Password') }}</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                </div>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-[#0A1128] focus:border-[#0A1128] sm:text-sm transition duration-150 ease-in-out" placeholder="Minimal 8 karakter">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Konfirmasi Password') }}</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                </div>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-[#0A1128] focus:border-[#0A1128] sm:text-sm transition duration-150 ease-in-out" placeholder="Ulangi password">
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div>
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-[#0A1128] hover:bg-[#131f4a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0A1128] transition duration-200">
                {{ __('Daftar') }}
            </button>
        </div>

        <div class="mt-6 text-center text-sm font-medium text-gray-600">
            {{ __('Sudah punya akun?') }} 
            <a href="{{ route('login') }}" class="text-[#0A1128] font-bold hover:underline transition duration-150">
                {{ __('Masuk di sini') }}
            </a>
        </div>
    </form>
</x-guest-layout>
