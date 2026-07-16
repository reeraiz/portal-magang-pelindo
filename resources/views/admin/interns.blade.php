@extends('layouts.app')
@section('header', 'Kelola Intern')

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-sm font-bold text-blue-600 mb-1 tracking-wide uppercase">Total Intern Terdaftar</p>
            <h3 class="text-4xl font-extrabold text-gray-900">{{ $interns->total() }}</h3>
        </div>
        <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 border border-blue-100">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
    <div class="flex justify-end">
        <button type="button" onclick="openCreateUser()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-sm transition-all hover:shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            Buat User Baru
        </button>
    </div>
    @endif

    <!-- Intern List -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="font-bold text-gray-800">Daftar Intern</h3>
            <form action="{{ route('admin.interns') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, divisi..." class="text-sm border border-gray-200 rounded-lg px-3 py-2 text-gray-600 focus:ring-blue-500 focus:border-blue-500 w-48 sm:w-64">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">Filter</button>
                @if(request('search'))
                <a href="{{ route('admin.interns') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Divisi</th>
                        <th class="px-6 py-4">Periode Magang</th>
                        <th class="px-6 py-4">Mentor</th>
                        <th class="px-6 py-4">Statistik</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($interns as $intern)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0 border-2 border-white shadow-sm">
                                    <span class="font-bold text-blue-700 text-sm">{{ substr($intern->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $intern->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $intern->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700 font-medium">{{ $intern->division ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($intern->internship_start_date && $intern->internship_end_date)
                                <p class="text-sm font-medium text-gray-800">{{ \Carbon\Carbon::parse($intern->internship_start_date)->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">s/d {{ \Carbon\Carbon::parse($intern->internship_end_date)->format('d M Y') }}</p>
                            @else
                                <span class="text-xs text-orange-500 font-semibold bg-orange-50 px-2 py-1 rounded-md">Belum diatur</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700 font-medium">{{ $intern->mentor ? $intern->mentor->name : '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-3">
                                <div class="text-center">
                                    <p class="text-lg font-bold text-blue-600">{{ $intern->attendances_count }}</p>
                                    <p class="text-[10px] text-gray-400 font-semibold uppercase">Absensi</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-bold text-emerald-600">{{ $intern->logbooks_count }}</p>
                                    <p class="text-[10px] text-gray-400 font-semibold uppercase">Logbook</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="openEditIntern({{ json_encode($intern) }})" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white border border-blue-200 hover:border-blue-600 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                    Edit
                                </button>
                                <form action="{{ route('admin.interns.reset-password', $intern->id) }}" method="POST" class="confirm-form" data-confirm-msg="Anda yakin ingin mereset password {{ addslashes($intern->name) }} menjadi password acak baru?">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-200 hover:border-red-600 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                        Reset Password
                                    </button>
                                </form>
                                @if(auth()->user()->role === 'admin')
                                <form action="{{ route('admin.users.destroy', $intern->id) }}" method="POST" class="confirm-form" data-confirm-msg="Anda yakin ingin MENGHAPUS akun {{ addslashes($intern->name) }} secara permanen? Semua data absensi dan logbook juga akan ikut terhapus!">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white hover:bg-red-700 border border-red-600 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                        Hapus Akun
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Belum ada intern terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($interns->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $interns->links() }}
        </div>
        @endif
    </div>

    <!-- Mentors List -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800">Daftar Pembimbing Magang & Divisi</h3>
                <p class="text-xs text-gray-500">Daftar pembimbing/mentor magang beserta divisi atau departemen yang diampu.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Nama Pembimbing</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Divisi / Departemen</th>
                        @if(auth()->user()->role === 'admin')
                        <th class="px-6 py-4">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($mentors as $mentor)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-800">{{ $mentor->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $mentor->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase {{ $mentor->role === 'admin' ? 'bg-purple-50 text-purple-600 border border-purple-100' : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                                {{ $mentor->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold {{ $mentor->division ? 'text-gray-800' : 'text-orange-500 italic' }}">
                                {{ $mentor->division ?? 'Belum dipilih' }}
                            </span>
                        </td>
                        @if(auth()->user()->role === 'admin')
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="openEditMentor({{ json_encode($mentor) }})" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white border border-blue-200 hover:border-blue-600 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                    Edit Divisi
                                </button>
                                <form action="{{ route('admin.users.reset-password', $mentor->id) }}" method="POST" class="confirm-form" data-confirm-msg="Anda yakin ingin mereset password {{ addslashes($mentor->name) }} menjadi password acak baru?">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white border border-orange-200 hover:border-orange-600 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                        Reset Password
                                    </button>
                                </form>
                                @if(auth()->id() !== $mentor->id)
                                <form action="{{ route('admin.users.destroy', $mentor->id) }}" method="POST" class="confirm-form" data-confirm-msg="Apakah Anda yakin ingin menghapus akun {{ addslashes($mentor->name) }} secara permanen?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-200 hover:border-red-600 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                        Hapus Akun
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->role === 'admin' ? '5' : '4' }}" class="px-6 py-8 text-center text-gray-500">Belum ada pembimbing terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Upload Sertifikat Magang -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-800">Upload & Kirim Sertifikat Magang</h3>
            <p class="text-xs text-gray-500">Kirim sertifikat melalui email kepada peserta yang status magangnya sudah selesai.</p>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.certificate.send') }}" method="POST" enctype="multipart/form-data" class="space-y-4" onsubmit="return validateCertificateSize(this)">
                @csrf
                @if($errors->has('certificate') || $errors->has('intern_id'))
                <div class="bg-red-50 text-red-700 p-3 rounded-xl border border-red-200 text-sm flex items-start gap-2">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p class="font-bold">Pengiriman Gagal</p>
                        <ul class="list-disc list-inside text-xs mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <!-- JS Validation Error Alert (Hidden by default) -->
                <div id="js-error-alert" class="hidden bg-red-50 text-red-700 p-3 rounded-xl border border-red-200 text-sm flex items-start gap-2 transition-all">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p class="font-bold">Validasi Gagal</p>
                        <p id="js-error-message" class="text-xs mt-1"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Intern (Selesai Magang)</label>
                        <input type="hidden" name="intern_id" id="selected_intern_id" required>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="openSelectInternModal()" class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-800 text-sm font-bold rounded-xl border border-gray-200 transition-colors shadow-sm">
                                Pilih Intern
                            </button>
                            <span id="selected_intern_name" class="text-sm text-gray-500 font-medium italic">Belum ada yang dipilih</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">File Sertifikat (PDF/Image)</label>
                        <input type="file" name="certificate" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-1.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <div class="mt-2 flex items-start gap-2 bg-yellow-50 text-yellow-700 p-2.5 rounded-lg border border-yellow-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            <p class="text-xs">Maksimal ukuran file <strong>5MB</strong> (PDF/JPG/PNG). Jika lebih, sistem akan menolak upload.</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-sm transition-all hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                        Kirim via Email
                    </button>
                </div>
            </form>
        </div>
    </div>

<!-- Create User Modal -->
<div id="createUserModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" onclick="closeCreateUser()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-1">Buat User Baru</h3>
                    <p class="text-sm text-gray-500 mb-4">Tambahkan akun pengguna baru untuk Intern, Pembimbing, atau Admin.</p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Masukkan nama lengkap">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="email@domain.com">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Role / Peran <span class="text-red-500">*</span></label>
                            <select name="role" id="create-role" required onchange="toggleInternFields()" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="intern">Intern / Peserta Magang</option>
                                <option value="pembimbing">Pembimbing / Mentor</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Divisi / Departemen</label>
                            <select name="division" id="create-division" onchange="filterMentors('create-division', 'create-mentor')" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">-- Pilih Divisi / Departemen --</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->name }}">{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="intern-fields-container" class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Mentor / Pembimbing</label>
                                <select name="mentor_id" id="create-mentor" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">-- Pilih Mentor --</option>
                                    @foreach($mentors as $mentor)
                                        @if($mentor->role !== 'admin')
                                        <option value="{{ $mentor->id }}" data-division="{{ $mentor->division }}">{{ $mentor->name }} ({{ $mentor->division ?? '-' }})</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mulai Magang</label>
                                    <input type="date" name="internship_start_date" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Selesai Magang</label>
                                    <input type="date" name="internship_end_date" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Magang</label>
                                    <select name="internship_type_id" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">-- Pilih Jenis Magang --</option>
                                        @foreach($internshipTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jenjang Pemagangan</label>
                                    <select name="education_level_id" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">-- Pilih Jenjang --</option>
                                        @foreach($educationLevels as $level)
                                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Asal Universitas / Sekolah</label>
                                    <select name="university_id" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">-- Pilih Asal Universitas --</option>
                                        @foreach($universities as $uni)
                                            <option value="{{ $uni->id }}">{{ $uni->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin (Gender)</label>
                                    <select name="gender_id" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">-- Pilih Gender --</option>
                                        @foreach($genders as $gender)
                                            <option value="{{ $gender->id }}">{{ $gender->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                            <input type="password" name="password" minlength="8" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Opsional (Kosongkan untuk password acak)">
                            <p class="text-[11px] text-gray-400 mt-1">* Jika dikosongkan, sistem akan membuat password acak 8 karakter secara otomatis.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Buat User
                    </button>
                    <button type="button" onclick="closeCreateUser()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Intern Modal -->
<div id="editInternModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" onclick="closeEditIntern()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form id="editInternForm" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-1">Edit Data Intern</h3>
                    <p class="text-sm text-gray-500 mb-4" id="edit-intern-name"></p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Divisi / Departemen</label>
                            <select name="division" id="edit-division" onchange="filterMentors('edit-division', 'edit-mentor')" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Pilih Divisi / Departemen</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->name }}">{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date" name="internship_start_date" id="edit-start-date" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Selesai</label>
                                <input type="date" name="internship_end_date" id="edit-end-date" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Mentor / Pembimbing</label>
                            <select name="mentor_id" id="edit-mentor" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">-- Pilih Mentor --</option>
                                @foreach($mentors as $mentor)
                                @if($mentor->role !== 'admin')
                                <option value="{{ $mentor->id }}" data-division="{{ $mentor->division }}">{{ $mentor->name }} ({{ $mentor->division ?? '-' }})</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="closeEditIntern()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Select Intern Modal -->
<div id="selectInternModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" onclick="closeSelectInternModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-bold text-gray-900">Pilih Intern</h3>
                    <button type="button" onclick="closeSelectInternModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="max-h-96 overflow-y-auto border border-gray-100 rounded-xl divide-y divide-gray-100">
                    @foreach($allInterns as $intern)
                        <button type="button" onclick="selectIntern({{ $intern->id }}, '{{ addslashes($intern->name) }} ({{ addslashes($intern->email) }})')" class="w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center justify-between transition-colors">
                            <div>
                                <p class="font-bold text-gray-800 text-sm">{{ $intern->name }}</p>
                                <p class="text-xs text-gray-500">{{ $intern->email }} - {{ $intern->division ?? 'Tidak ada divisi' }}</p>
                            </div>
                            <span class="text-blue-600 bg-blue-50 px-2 py-1 rounded-lg text-xs font-bold">Pilih</span>
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeSelectInternModal()" class="w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Mentor Modal -->
<div id="editMentorModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" onclick="closeEditMentor()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form id="editMentorForm" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-1">Edit Divisi Pembimbing</h3>
                    <p class="text-sm text-gray-500 mb-4" id="edit-mentor-info"></p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" id="edit-mentor-name" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Divisi / Departemen</label>
                            <select name="division" id="edit-mentor-division" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">-- Pilih Divisi / Departemen --</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->name }}">{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="closeEditMentor()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openCreateUser() {
        document.getElementById('createUserModal').classList.remove('hidden');
        document.getElementById('create-division').value = '';
        filterMentors('create-division', 'create-mentor');
        toggleInternFields();
    }

    function filterMentors(divisionSelectId, mentorSelectId) {
        const division = document.getElementById(divisionSelectId).value;
        const mentorSelect = document.getElementById(mentorSelectId);
        const options = mentorSelect.options;

        for (let i = 1; i < options.length; i++) {
            const option = options[i];
            if (division === "" || option.dataset.division === division) {
                option.hidden = false;
                option.disabled = false;
            } else {
                option.hidden = true;
                option.disabled = true;
            }
        }
        
        if (mentorSelect.selectedIndex > 0 && mentorSelect.options[mentorSelect.selectedIndex].hidden) {
            mentorSelect.value = '';
        }
    }

    function closeCreateUser() {
        document.getElementById('createUserModal').classList.add('hidden');
    }

    function toggleInternFields() {
        const role = document.getElementById('create-role').value;
        const container = document.getElementById('intern-fields-container');
        if (role === 'intern') {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    function openEditIntern(intern) {
        document.getElementById('editInternForm').action = '{{ url("admin/interns") }}/' + intern.id;
        document.getElementById('edit-intern-name').textContent = intern.name + ' (' + intern.email + ')';
        document.getElementById('edit-division').value = intern.division || '';
        filterMentors('edit-division', 'edit-mentor');
        document.getElementById('edit-start-date').value = intern.internship_start_date ? intern.internship_start_date.split('T')[0] : '';
        document.getElementById('edit-end-date').value = intern.internship_end_date ? intern.internship_end_date.split('T')[0] : '';
        document.getElementById('edit-mentor').value = intern.mentor_id || '';
        document.getElementById('editInternModal').classList.remove('hidden');
    }

    function closeEditIntern() {
        document.getElementById('editInternModal').classList.add('hidden');
    }

    function openEditMentor(mentor) {
        document.getElementById('editMentorForm').action = '{{ url("admin/mentors") }}/' + mentor.id;
        document.getElementById('edit-mentor-info').textContent = mentor.email;
        document.getElementById('edit-mentor-name').value = mentor.name || '';
        document.getElementById('edit-mentor-division').value = mentor.division || '';
        document.getElementById('editMentorModal').classList.remove('hidden');
    }

    function closeEditMentor() {
        document.getElementById('editMentorModal').classList.add('hidden');
    }
    function openSelectInternModal() {
        document.getElementById('selectInternModal').classList.remove('hidden');
    }

    function closeSelectInternModal() {
        document.getElementById('selectInternModal').classList.add('hidden');
    }

    function selectIntern(id, name) {
        document.getElementById('selected_intern_id').value = id;
        document.getElementById('selected_intern_name').textContent = name;
        document.getElementById('selected_intern_name').classList.remove('italic', 'text-gray-500');
        document.getElementById('selected_intern_name').classList.add('text-blue-700', 'font-bold');
        closeSelectInternModal();
    }

    function validateCertificateSize(form) {
        const fileInput = form.querySelector('input[name="certificate"]');
        const errorAlert = document.getElementById('js-error-alert');
        const errorMessage = document.getElementById('js-error-message');
        
        // Sembunyikan alert sebelumnya jika ada
        errorAlert.classList.add('hidden');
        
        if (fileInput.files.length > 0) {
            const fileSize = fileInput.files[0].size;
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes
            
            if (fileSize > maxSize) {
                const sizeInMB = (fileSize / (1024*1024)).toFixed(2);
                errorMessage.textContent = `Ukuran file sertifikat yang Anda pilih adalah ${sizeInMB} MB. Batas maksimal adalah 5 MB. Silakan kompres file Anda terlebih dahulu.`;
                errorAlert.classList.remove('hidden');
                
                // Scroll sedikit ke atas agar alert terlihat
                errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                return false;
            }
        }
        return true;
    }
</script>
@endsection
