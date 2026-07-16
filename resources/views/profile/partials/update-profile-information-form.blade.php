<section>
    <header class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">
            Informasi Profil
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Perbarui informasi profil dan alamat email akun Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
            <input id="name" name="name" type="text" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 text-sm text-red-600" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="division" class="block text-sm font-semibold text-gray-700 mb-1">Divisi / Departemen</label>
            <select id="division" name="division" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                <option value="">-- Pilih Divisi / Departemen --</option>
                @php
                    $divs = $divisions ?? \App\Models\Division::orderBy('name')->get();
                @endphp
                @foreach($divs as $div)
                    <option value="{{ $div->name }}" {{ old('division', $user->division) == $div->name ? 'selected' : '' }}>{{ $div->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2 text-sm text-red-600" :messages="$errors->get('division')" />
        </div>

        @if($user->role === 'intern')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="internship_start_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mulai Magang</label>
                <input id="internship_start_date" name="internship_start_date" type="date" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" value="{{ old('internship_start_date', optional($user->internship_start_date)->format('Y-m-d')) }}" />
                <x-input-error class="mt-2 text-sm text-red-600" :messages="$errors->get('internship_start_date')" />
            </div>
            <div>
                <label for="internship_end_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Selesai Magang</label>
                <input id="internship_end_date" name="internship_end_date" type="date" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" value="{{ old('internship_end_date', optional($user->internship_end_date)->format('Y-m-d')) }}" />
                <x-input-error class="mt-2 text-sm text-red-600" :messages="$errors->get('internship_end_date')" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label for="internship_type_id" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Magang</label>
                <select id="internship_type_id" name="internship_type_id" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="">-- Pilih Jenis Magang --</option>
                    @php
                        $types = $internshipTypes ?? \App\Models\InternshipType::orderBy('name')->get();
                    @endphp
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ old('internship_type_id', $user->internship_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2 text-sm text-red-600" :messages="$errors->get('internship_type_id')" />
            </div>
            <div>
                <label for="education_level_id" class="block text-sm font-semibold text-gray-700 mb-1">Jenjang Pemagangan</label>
                <select id="education_level_id" name="education_level_id" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="">-- Pilih Jenjang Pemagangan --</option>
                    @php
                        $levels = $educationLevels ?? \App\Models\EducationLevel::orderBy('name')->get();
                    @endphp
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}" {{ old('education_level_id', $user->education_level_id) == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2 text-sm text-red-600" :messages="$errors->get('education_level_id')" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label for="university_id" class="block text-sm font-semibold text-gray-700 mb-1">Asal Universitas / Sekolah</label>
                <select id="university_id" name="university_id" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="">-- Pilih Asal Universitas --</option>
                    @php
                        $unis = $universities ?? \App\Models\University::orderBy('name')->get();
                    @endphp
                    @foreach($unis as $uni)
                        <option value="{{ $uni->id }}" {{ old('university_id', $user->university_id) == $uni->id ? 'selected' : '' }}>{{ $uni->name }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2 text-sm text-red-600" :messages="$errors->get('university_id')" />
            </div>
            <div>
                <label for="gender_id" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin (Gender)</label>
                <select id="gender_id" name="gender_id" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    @php
                        $gends = $genders ?? \App\Models\Gender::orderBy('name')->get();
                    @endphp
                    @foreach($gends as $gend)
                        <option value="{{ $gend->id }}" {{ old('gender_id', $user->gender_id) == $gend->id ? 'selected' : '' }}>{{ $gend->name }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2 text-sm text-red-600" :messages="$errors->get('gender_id')" />
            </div>
        </div>
        @endif

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Email</label>
            <input id="email" name="email" type="email" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-2 text-sm text-red-600" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-4 rounded-xl bg-orange-50 border border-orange-100">
                    <p class="text-sm font-medium text-orange-800">
                        Email Anda belum diverifikasi.
                        <button form="send-verification" class="underline font-bold text-orange-600 hover:text-orange-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 ml-1">
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            Tautan verifikasi baru telah dikirim ke email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg"
                >Berhasil disimpan.</p>
            @endif
        </div>
    </form>
</section>
