<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekap Absensi - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: auto;
                margin: 0;
            }
            html, body {
                height: auto !important;
                min-height: 0 !important;
            }
            body {
                font-size: 11pt;
                margin: 1cm !important;
                padding: 0 !important;
            }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
            tr, td, th {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            table th, table td {
                padding: 6px 8px !important;
            }
            .mb-8 { margin-bottom: 1rem !important; }
            .mt-16 { margin-top: 1.5rem !important; }
            .mt-8 { margin-top: 1rem !important; }
            .pb-6 { padding-bottom: 0.5rem !important; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 p-8 max-w-4xl mx-auto bg-white min-h-screen shadow-lg my-8 print:my-0 print:shadow-none print:p-0">
    
    <!-- Action buttons -->
    <div class="mb-8 flex justify-end gap-4 no-print border-b pb-4">
        <a href="{{ route('intern.rekap') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg text-sm font-bold hover:bg-gray-300">Kembali</a>
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
            Cetak PDF
        </button>
    </div>

    <!-- Header / Kop Surat -->
    <div class="border-b-2 border-gray-800 pb-6 mb-8 flex items-center justify-between">
        <div class="flex items-center">
            <img src="{{ asset('logo-pelindo.png') }}" alt="Logo Pelindo" class="object-contain" style="height: 90px; width: auto;">
        </div>
        <div class="text-right">
            <h1 class="text-2xl font-extrabold uppercase tracking-widest text-gray-900">Rekap Absensi Magang</h1>
            <p class="text-sm text-gray-600 mt-1">PT Pelabuhan Indonesia (Persero)</p>
            <p class="text-sm text-gray-600">Departemen: {{ $user->division }}</p>
        </div>
    </div>

    <!-- Intern Info -->
    <div class="grid grid-cols-2 gap-6 mb-6 text-sm">
        <div>
            <table class="w-full">
                <tr><td class="py-1 font-bold text-gray-600 w-32">Nama Peserta</td><td class="font-bold">: {{ $user->name }}</td></tr>
                <tr><td class="py-1 font-bold text-gray-600">Divisi</td><td class="font-bold">: {{ $user->division ?? '-' }}</td></tr>
                <tr><td class="py-1 font-bold text-gray-600">Email</td><td>: {{ $user->email }}</td></tr>
                <tr><td class="py-1 font-bold text-gray-600">Pembimbing</td><td class="font-bold">: {{ $user->mentor ? $user->mentor->name : '-' }}</td></tr>
            </table>
        </div>
        <div>
            <table class="w-full">
                <tr>
                    <td class="py-1 font-bold text-gray-600 w-32 align-top">Masa Magang</td>
                    <td>: 
                        @if($user->internship_start_date && $user->internship_end_date)
                            {{ \Carbon\Carbon::parse($user->internship_start_date)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($user->internship_end_date)->translatedFormat('d M Y') }}
                        @else
                            <span class="text-gray-400 italic">Belum diatur</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="py-1 font-bold text-gray-600">Total Hari</td>
                    <td class="font-bold">: 
                        @if($user->internship_start_date && $user->internship_end_date)
                            {{ \Carbon\Carbon::parse($user->internship_start_date)->diffInDays(\Carbon\Carbon::parse($user->internship_end_date)) + 1 }} Hari
                        @else
                            <span class="text-gray-400 italic font-normal">-</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Stats Summary Block -->
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-6 grid grid-cols-4 gap-4 text-center">
        <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Kehadiran</p>
            <h4 class="text-xl font-extrabold text-blue-600">{{ $totalHadir }} Hari</h4>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Izin</p>
            <h4 class="text-xl font-extrabold text-purple-600">{{ $totalIzin }} Hari</h4>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Sakit</p>
            <h4 class="text-xl font-extrabold text-rose-600">{{ $totalSakit }} Hari</h4>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Alpa</p>
            <h4 class="text-xl font-extrabold text-red-600">{{ $totalAlpa }} Hari</h4>
        </div>
    </div>

    <!-- Attendance Table -->
    <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 flex justify-between items-baseline">
        <span>Rincian Absensi Kehadiran</span>
        @if(request()->filled('start_date') && request()->filled('end_date'))
            <span class="text-xs font-normal text-gray-500">
                Periode: {{ \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse(request('end_date'))->translatedFormat('d M Y') }}
            </span>
        @endif
    </h2>
    <table class="w-full text-left text-sm border-collapse border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="border border-gray-300 px-4 py-3 w-40">Hari & Tanggal</th>
                <th class="border border-gray-300 px-4 py-3 text-center w-28">Jam Masuk</th>
                <th class="border border-gray-300 px-4 py-3 text-center w-28">Jam Pulang</th>
                <th class="border border-gray-300 px-4 py-3 w-32">Status</th>
                <th class="border border-gray-300 px-4 py-3">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $att)
            <tr>
                <td class="border border-gray-300 px-4 py-3 whitespace-nowrap font-medium">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d M Y') }}</td>
                <td class="border border-gray-300 px-4 py-3 text-center">
                    {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '-' }}
                </td>
                <td class="border border-gray-300 px-4 py-3 text-center">
                    {{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '-' }}
                </td>
                <td class="border border-gray-300 px-4 py-3">
                    @if($att->status === 'verified' || $att->status === 'hadir')
                        <span class="font-bold text-green-700">Hadir</span>
                    @elseif($att->status === 'pending')
                        <span class="font-bold text-orange-600">Menunggu</span>
                    @elseif($att->status === 'izin')
                        @if(str_contains(strtolower($att->notes ?? ''), 'sakit'))
                            <span class="font-bold text-rose-600">Sakit</span>
                        @elseif(str_contains(strtolower($att->notes ?? ''), 'cuti'))
                            <span class="font-bold text-indigo-600">Cuti</span>
                        @else
                            <span class="font-bold text-purple-600">Izin</span>
                        @endif
                    @elseif($att->status === 'rejected')
                        <span class="font-bold text-red-600">Ditolak</span>
                    @else
                        <span class="font-bold text-red-600">Alpa</span>
                    @endif
                </td>
                <td class="border border-gray-300 px-4 py-3 text-gray-700">
                    {{ $att->notes ? $att->notes : '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="border border-gray-300 px-4 py-8 text-center text-gray-500">Belum ada data absensi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Section -->
    <div class="mt-12 flex justify-between px-10" style="page-break-inside: avoid; break-inside: avoid;">
        <div class="text-center">
            <p class="mb-20 text-sm">Peserta Magang,</p>
            <p class="font-bold underline">{{ $user->name }}</p>
            <p class="text-sm text-gray-500">{{ $user->division ?? '' }}</p>
        </div>
        <div class="text-center">
            <p class="mb-20 text-sm">Mengetahui,<br>Mentor / Pembimbing</p>
            @if($user->mentor)
                <p class="font-bold underline">{{ $user->mentor->name }}</p>
                <p class="text-sm text-gray-500">{{ $user->mentor->division ?? 'Departemen TI' }}</p>
            @else
                <p class="font-bold underline">_________________________</p>
                <p class="text-sm text-gray-500">Pembimbing Magang</p>
            @endif
        </div>
    </div>
</body>
</html>
