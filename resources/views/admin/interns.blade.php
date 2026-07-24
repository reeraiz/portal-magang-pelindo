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

    <div class="flex justify-end gap-3">
        <button type="button" onclick="exportSelected('excel')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-sm transition-all hover:shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Ekspor Excel
        </button>
        <button type="button" onclick="exportSelected('pdf')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-xl shadow-sm transition-all hover:shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M12 18v-6"/><path d="M9 15l3 3 3-3"/></svg>
            Ekspor PDF
        </button>
        @if(auth()->user()->role === 'admin')
        <button type="button" onclick="openCreateUser()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-sm transition-all hover:shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            Buat User Baru
        </button>
        @endif
    </div>

    <!-- Intern List -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="font-bold text-gray-800">Daftar Intern</h3>
            <form action="{{ route('admin.interns') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email..." class="text-sm border border-gray-200 rounded-lg px-3 py-2 text-gray-600 focus:ring-blue-500 focus:border-blue-500 w-48 sm:w-64">
                
                <div class="relative">
                    <select name="filter_division" id="filter_division" onchange="filterDepartments('filter_division', 'filter_department'); this.form.submit()" class="text-sm border border-gray-200 rounded-lg px-3 py-2 text-gray-600 focus:ring-blue-500 focus:border-blue-500 bg-white min-w-[140px]">
                        <option value="">Semua Divisi</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->name }}" {{ request('filter_division') == $div->name ? 'selected' : '' }}>{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relative">
                    <select name="filter_department" id="filter_department" data-selected="{{ request('filter_department') }}" onchange="this.form.submit()" class="text-sm border border-gray-200 rounded-lg px-3 py-2 text-gray-600 focus:ring-blue-500 focus:border-blue-500 bg-white min-w-[140px]">
                        <option value="">Semua Departemen</option>
                    </select>
                </div>

                <div class="relative">
                    <select name="shift" onchange="this.form.submit()" class="text-sm border border-gray-200 rounded-lg px-3 py-2 text-gray-600 focus:ring-blue-500 focus:border-blue-500 bg-white min-w-[140px]">
                        <option value="">Semua Shift</option>
                        <option value="pagi" {{ request('shift') === 'pagi' ? 'selected' : '' }}>Shift Pagi</option>
                        <option value="siang" {{ request('shift') === 'siang' ? 'selected' : '' }}>Shift Siang</option>
                        <option value="full_day" {{ request('shift') === 'full_day' ? 'selected' : '' }}>Shift Full Day</option>
                    </select>
                </div>
                
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">Filter</button>
                @if(request('search') || request('shift') || request('filter_division') || request('filter_department'))
                <a href="{{ route('admin.interns') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors">Reset</a>
                @endif
            </form>
        </div>

        <div class="px-6 py-3 bg-blue-50/50 border-b border-gray-100 hidden items-center justify-between transition-all" id="bulk-action-bar">
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-blue-800 bg-blue-100 px-2 py-0.5 rounded-md"><span id="selected-count">0</span> terpilih</span>
                <span class="text-sm text-gray-600">Ubah shift secara massal:</span>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.interns.bulk-shift') }}" method="POST" id="bulk-form-pagi" class="inline">
                    @csrf
                    <input type="hidden" name="shift" value="pagi">
                    <!-- Hidden inputs for IDs will be appended here via JS -->
                    <button type="button" onclick="submitBulkShift('pagi')" class="px-3 py-1.5 bg-white border border-blue-200 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-xs font-bold transition-colors shadow-sm">
                        Set ke Pagi
                    </button>
                </form>
                <form action="{{ route('admin.interns.bulk-shift') }}" method="POST" id="bulk-form-siang" class="inline">
                    @csrf
                    <input type="hidden" name="shift" value="siang">
                    <button type="button" onclick="submitBulkShift('siang')" class="px-3 py-1.5 bg-white border border-orange-200 text-orange-600 hover:bg-orange-500 hover:text-white rounded-lg text-xs font-bold transition-colors shadow-sm">
                        Set ke Siang
                    </button>
                </form>
                <form action="{{ route('admin.interns.bulk-shift') }}" method="POST" id="bulk-form-full_day" class="inline">
                    @csrf
                    <input type="hidden" name="shift" value="full_day">
                    <button type="button" onclick="submitBulkShift('full_day')" class="px-3 py-1.5 bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-lg text-xs font-bold transition-colors shadow-sm">
                        Set ke Full Day
                    </button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-4 w-16 text-center align-middle">
                            <input type="checkbox" id="selectAllInterns" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer" onclick="toggleAllCheckboxes(this)" title="Pilih Semua">
                        </th>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Divisi & Departemen</th>
                        <th class="px-6 py-4">Shift</th>
                        <th class="px-6 py-4">Periode Magang</th>
                        <th class="px-6 py-4">Mentor</th>
                        <th class="px-6 py-4">Statistik</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($interns as $intern)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-4">
                            <input type="checkbox" name="intern_ids[]" value="{{ $intern->id }}" class="intern-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer" onchange="updateBulkActionUI()">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0 border-2 border-white shadow-sm overflow-hidden">
                                    @if($intern->avatar)
                                        <img src="{{ asset('storage/' . $intern->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                                    @else
                                        <span class="font-bold text-blue-700 text-sm">{{ substr($intern->name, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $intern->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $intern->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700 font-medium">{{ $intern->division ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $intern->department ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.interns.update', $intern->id) }}" method="POST" class="m-0">
                                @csrf
                                @method('PUT')
                                <div class="relative inline-block">
                                    @if($intern->shift === 'siang')
                                        <svg class="w-3 h-3 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-orange-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                                    @elseif($intern->shift === 'full_day')
                                        <svg class="w-3 h-3 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                    @else
                                        <svg class="w-3 h-3 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12h4"/><path d="M18 12h4"/><path d="M12 2v4"/><path d="M12 18v4"/><path d="m4.93 4.93 2.83 2.83"/><path d="m16.24 16.24 2.83 2.83"/><path d="m4.93 19.07 2.83-2.83"/><path d="m16.24 7.76 2.83-2.83"/></svg>
                                    @endif
                                    
                                    <select name="shift" onchange="this.form.submit()" class="text-xs border {{ $intern->shift === 'siang' ? 'border-orange-200 bg-orange-50 text-orange-600' : ($intern->shift === 'full_day' ? 'border-emerald-200 bg-emerald-50 text-emerald-600' : 'border-blue-200 bg-blue-50 text-blue-600') }} rounded-full pl-7 pr-6 py-1 font-bold appearance-none bg-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors m-0">
                                        <option value="pagi" {{ $intern->shift === 'pagi' || is_null($intern->shift) ? 'selected' : '' }}>Pagi</option>
                                        <option value="siang" {{ $intern->shift === 'siang' ? 'selected' : '' }}>Siang</option>
                                        <option value="full_day" {{ $intern->shift === 'full_day' ? 'selected' : '' }}>Full Day</option>
                                    </select>
                                    
                                    <svg class="w-3 h-3 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none {{ $intern->shift === 'siang' ? 'text-orange-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </form>
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
                                <a href="{{ route('admin.interns.cv', $intern->id) }}" target="_blank" class="px-3 py-1.5 bg-purple-50 text-purple-600 hover:bg-purple-600 hover:text-white border border-purple-200 hover:border-purple-600 rounded-lg text-xs font-bold transition-colors shadow-sm flex items-center gap-1" title="Generate CV">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Cetak CV
                                </a>
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
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            @if(request('search') || request('shift'))
                                Tidak ada data intern yang sesuai dengan filter/pencarian Anda.
                            @else
                                Belum ada intern terdaftar.
                            @endif
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
<div id="createUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" onclick="closeCreateUser()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 rounded-t-2xl">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-1">Buat User Baru</h3>
                    <p class="text-sm text-gray-500 mb-4">Tambahkan akun pengguna baru untuk Intern, Pembimbing, atau Admin.</p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Masukkan nama lengkap">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="email@domain.com">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon (WA)</label>
                                <input type="text" name="phone" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Contoh: 081234567890">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Role / Peran <span class="text-red-500">*</span></label>
                            <select name="role" id="create-role" required onchange="toggleInternFields()" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="intern">Intern / Peserta Magang</option>
                                <option value="pembimbing">Pembimbing / Mentor</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Divisi</label>
                                <select name="division" id="create-division" onchange="filterMentors('create-division', 'create-mentor'); filterDepartments('create-division', 'create-department');" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">-- Pilih Divisi --</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->name }}">{{ $div->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Departemen</label>
                                <select name="department" id="create-department" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">-- Pilih Departemen --</option>
                                </select>
                            </div>
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
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Shift Magang</label>
                                <select name="shift" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="pagi">Pagi (08:00 - 12:00)</option>
                                    <option value="siang">Siang (12:00 - 17:00)</option>
                                    <option value="full_day">Full Day (08:00 - 17:00)</option>
                                </select>
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
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Pendidikan</label>
                                    <div class="flex items-center space-x-2">
                                        <input name="education_start_year" type="number" min="1900" max="2099" step="1" class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Mulai">
                                        <span class="text-gray-500">-</span>
                                        <input name="education_end_year" type="number" min="1900" max="2099" step="1" class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Selesai">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin (Gender)</label>
                                    <select name="gender_id" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">-- Pilih Jenis Kelamin --</option>
                                        @foreach($genders as $gend)
                                            <option value="{{ $gend->id }}">{{ $gend->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Fakultas</label>
                                    <select name="faculty" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">-- Pilih Fakultas --</option>
                                        @foreach($faculties as $fac)
                                            <option value="{{ $fac->name }}">{{ $fac->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jurusan</label>
                                    <select name="major" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">-- Pilih Jurusan --</option>
                                        @foreach($majors as $maj)
                                            <option value="{{ $maj->name }}">{{ $maj->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Program Studi (Prodi)</label>
                                    <select name="study_program" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">-- Pilih Prodi --</option>
                                        @foreach($studyPrograms as $sp)
                                            <option value="{{ $sp->name }}">{{ $sp->name }}</option>
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
<div id="editInternModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" onclick="closeEditIntern()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form id="editInternForm" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 rounded-t-2xl">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-1">Edit Data Intern</h3>
                    <p class="text-sm text-gray-500 mb-4" id="edit-intern-name"></p>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Divisi</label>
                                <select name="division" id="edit-division" onchange="filterMentors('edit-division', 'edit-mentor'); filterDepartments('edit-division', 'edit-department');" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">-- Pilih Divisi --</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->name }}">{{ $div->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Departemen</label>
                                <select name="department" id="edit-department" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">-- Pilih Departemen --</option>
                                </select>
                            </div>
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
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Shift Magang</label>
                            <select name="shift" id="edit-shift" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="pagi">Pagi (08:00 - 12:00)</option>
                                <option value="siang">Siang (12:00 - 17:00)</option>
                                <option value="full_day">Full Day (08:00 - 17:00)</option>
                            </select>
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
<div id="editMentorModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" onclick="closeEditMentor()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form id="editMentorForm" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 rounded-t-2xl">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-1">Edit Data Pembimbing</h3>
                    <p class="text-sm text-gray-500 mb-4" id="edit-mentor-info"></p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" id="edit-mentor-name" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Divisi</label>
                                <select name="division" id="edit-mentor-division" onchange="filterDepartments('edit-mentor-division', 'edit-mentor-department');" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">-- Pilih Divisi --</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->name }}">{{ $div->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Departemen</label>
                                <select name="department" id="edit-mentor-department" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">-- Pilih Departemen --</option>
                                </select>
                            </div>
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
        filterDepartments('edit-division', 'edit-department');
        document.getElementById('edit-department').value = intern.department || '';
        filterMentors('edit-division', 'edit-mentor');
        document.getElementById('edit-start-date').value = intern.internship_start_date ? intern.internship_start_date.split('T')[0] : '';
        document.getElementById('edit-end-date').value = intern.internship_end_date ? intern.internship_end_date.split('T')[0] : '';
        document.getElementById('edit-shift').value = intern.shift || 'pagi';
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
        filterDepartments('edit-mentor-division', 'edit-mentor-department');
        document.getElementById('edit-mentor-department').value = mentor.department || '';
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

    // Bulk Action Logic
    function toggleAllCheckboxes(source) {
        const checkboxes = document.querySelectorAll('.intern-checkbox');
        checkboxes.forEach(cb => cb.checked = source.checked);
        updateBulkActionUI();
    }

    function exportSelected(type) {
        const checkboxes = document.querySelectorAll('.intern-checkbox:checked');
        let ids = Array.from(checkboxes).map(cb => cb.value).join(',');
        let url = type === 'excel' ? '{{ route('admin.interns.export.excel') }}' : '{{ route('admin.interns.export.pdf') }}';
        
        if (ids) {
            url += '?ids=' + ids;
        }

        if (type === 'pdf') {
            window.open(url, '_blank');
        } else {
            window.location.href = url;
        }
    }

    function updateBulkActionUI() {
        const checkboxes = document.querySelectorAll('.intern-checkbox:checked');
        const count = checkboxes.length;
        const actionBar = document.getElementById('bulk-action-bar');
        const countSpan = document.getElementById('selected-count');
        
        if (count > 0) {
            countSpan.textContent = count;
            actionBar.classList.remove('hidden');
            actionBar.classList.add('flex');
        } else {
            actionBar.classList.add('hidden');
            actionBar.classList.remove('flex');
            document.getElementById('selectAllInterns').checked = false;
        }
    }

    function submitBulkShift(shiftType) {
        const checkboxes = document.querySelectorAll('.intern-checkbox:checked');
        if (checkboxes.length === 0) return;
        
        const formId = 'bulk-form-' + shiftType;
        const form = document.getElementById(formId);
        
        // Remove old hidden inputs if any
        form.querySelectorAll('.dyn-input').forEach(el => el.remove());
        
        // Add new hidden inputs for each selected ID
        checkboxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'intern_ids[]';
            input.value = cb.value;
            input.className = 'dyn-input';
            form.appendChild(input);
        });
        
        form.submit();
    }

    const divisionsData = @json($divisions);

    function filterDepartments(divisionSelectId, departmentSelectId) {
        const divSelect = document.getElementById(divisionSelectId);
        const deptSelect = document.getElementById(departmentSelectId);
        const selectedDivName = divSelect.value;
        const currentDept = deptSelect.getAttribute('data-selected') || deptSelect.value;
        
        // Kosongkan options departemen
        deptSelect.innerHTML = '<option value="">-- Pilih Departemen --</option>';
        
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

    // Initialize department filters on page load
    window.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('filter_division').value) {
            filterDepartments('filter_division', 'filter_department');
        }
    });
</script>
@endsection
