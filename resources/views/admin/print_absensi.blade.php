<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Absensi Peserta Magang - PT Pelabuhan Indonesia (Persero)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 p-6 md:p-12 font-sans">
    <!-- Action Bar (No Print) -->
    <div class="max-w-5xl mx-auto mb-6 flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-200 no-print">
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-bold">Laporan Resmi PT Pelindo</span>
            <span class="text-sm text-gray-500">Siap dicetak atau disimpan sebagai PDF</span>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.absensi') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold text-sm transition-colors">
                Kembali
            </a>
            <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm shadow flex items-center gap-2 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Official Printable Area -->
    <div class="max-w-5xl mx-auto bg-white p-8 md:p-12 shadow-md border border-gray-300 rounded-none">
        <!-- Kop Surat Pelindo -->
        <div class="border-b-4 border-double border-blue-900 pb-6 mb-6 flex items-center justify-between">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 bg-blue-900 rounded-xl flex items-center justify-center text-white font-extrabold text-2xl tracking-tighter">
                    PELINDO
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-blue-900 tracking-wide uppercase">PT PELABUHAN INDONESIA (PERSERO)</h1>
                    <p class="text-xs font-semibold text-gray-600 uppercase mt-0.5">Regional / Cabang Pengelola Program Magang & Prakerin</p>
                    <p class="text-xs text-gray-500 mt-1">Jl. Pasumpahan No. 1, Pelabuhan Trisakti, Banjarmasin / Kantor Pusat PT Pelabuhan Indonesia</p>
                    <p class="text-xs text-gray-500">Website: www.pelindo.co.id | Email: humas@pelindo.co.id</p>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <span class="inline-block border border-gray-400 px-3 py-1 text-xs font-mono font-bold text-gray-600 uppercase">
                    DOKUMEN ARSIP RESMI
                </span>
                <p class="text-[11px] text-gray-400 mt-1">Dicetak pada: {{ \Carbon\Carbon::now('Asia/Makassar')->format('d M Y, H:i') }} WITA</p>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center my-8">
            <h2 class="text-xl font-bold uppercase underline decoration-blue-800 underline-offset-4 tracking-wider text-gray-900">
                REKAPITULASI KEHADIRAN PESERTA MAGANG
            </h2>
            <p class="text-sm text-gray-600 mt-2 font-medium">
                Periode Laporan: Semua Data Kehadiran Terverifikasi & Tercatat
            </p>
        </div>

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-4 gap-4 mb-8 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <div class="text-center border-r border-gray-200 last:border-0">
                <p class="text-xs font-bold text-gray-500 uppercase">Total Entri</p>
                <p class="text-xl font-extrabold text-gray-800">{{ $attendances->count() }}</p>
            </div>
            <div class="text-center border-r border-gray-200 last:border-0">
                <p class="text-xs font-bold text-emerald-600 uppercase">Hadir / Verified</p>
                <p class="text-xl font-extrabold text-emerald-700">{{ $totalHadir }}</p>
            </div>
            <div class="text-center border-r border-gray-200 last:border-0">
                <p class="text-xs font-bold text-blue-600 uppercase">Izin / Sakit</p>
                <p class="text-xl font-extrabold text-blue-700">{{ $totalIzin }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs font-bold text-red-600 uppercase">Alpa / Tanpa Keterangan</p>
                <p class="text-xl font-extrabold text-red-700">{{ $totalAlpa }}</p>
            </div>
        </div>

        <!-- Data Table -->
        <table class="w-full border-collapse border border-gray-300 text-xs mb-10">
            <thead>
                <tr class="bg-gray-100 text-gray-700 uppercase font-bold text-left">
                    <th class="border border-gray-300 px-3 py-2 text-center w-12">No</th>
                    <th class="border border-gray-300 px-3 py-2">ID & Nama Peserta</th>
                    <th class="border border-gray-300 px-3 py-2">Divisi & Mentor</th>
                    <th class="border border-gray-300 px-3 py-2 text-center">Tanggal</th>
                    <th class="border border-gray-300 px-3 py-2 text-center">Masuk</th>
                    <th class="border border-gray-300 px-3 py-2 text-center">Pulang</th>
                    <th class="border border-gray-300 px-3 py-2 text-center">Status</th>
                    <th class="border border-gray-300 px-3 py-2">Keterangan / Lokasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $index => $item)
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-3 py-2 text-center font-mono">{{ $index + 1 }}</td>
                    <td class="border border-gray-300 px-3 py-2 font-medium">
                        <div class="font-bold text-gray-900">{{ $item->user->name ?? 'User Terhapus' }}</div>
                        <div class="text-[11px] text-gray-500 font-mono">{{ $item->user->intern_id ?? '-' }}</div>
                    </td>
                    <td class="border border-gray-300 px-3 py-2">
                        <div class="font-semibold text-gray-800">{{ $item->user->division ?? '-' }}</div>
                        <div class="text-[11px] text-gray-500">{{ $item->user->mentor->name ?? '-' }}</div>
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-center font-mono">
                        {{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-center font-mono">
                        {{ $item->check_in ?? '-' }}
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-center font-mono">
                        {{ $item->check_out ?? '-' }}
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-center">
                        @if($item->status == 'verified')
                            <span class="font-bold text-emerald-700 uppercase">Hadir</span>
                        @elseif($item->status == 'izin')
                            <span class="font-bold text-blue-700 uppercase">Izin/Sakit</span>
                        @elseif($item->status == 'alpa')
                            <span class="font-bold text-red-700 uppercase">Alpa</span>
                        @else
                            <span class="font-semibold text-gray-600 uppercase">{{ $item->status }}</span>
                        @endif
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-gray-600">
                        {{ $item->notes ?? $item->location ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="border border-gray-300 px-3 py-6 text-center text-gray-400 italic">
                        Tidak ada data absensi yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Signature Box -->
        <div class="grid grid-cols-2 mt-12 pt-6 page-break-inside-avoid">
            <div>
                <p class="text-xs text-gray-500">Catatan:</p>
                <p class="text-xs text-gray-600 italic">Dokumen ini dicetak dari sistem otomatis Portal Magang PT Pelabuhan Indonesia (Persero) dan sah secara administrasi digital.</p>
            </div>
            <div class="text-center ml-auto w-64">
                <p class="text-xs font-semibold text-gray-700 mb-1">Banjarmasin, {{ \Carbon\Carbon::now('Asia/Makassar')->format('d F Y') }}</p>
                <p class="text-xs font-bold text-gray-900 uppercase">Mengetahui / Pembimbing Magang,</p>
                <div class="h-24 flex items-center justify-center">
                    <!-- Space for physical stamp/signature -->
                    <span class="text-[10px] text-gray-300 uppercase tracking-widest border border-dashed border-gray-300 px-3 py-1">Cap / Tanda Tangan</span>
                </div>
                <p class="text-xs font-bold text-gray-900 underline">{{ Auth::user()->name ?? 'Pejabat Berwenang' }}</p>
                <p class="text-[11px] text-gray-500 font-mono">NIPP / ID: {{ Auth::user()->nipp ?? Auth::user()->id ?? '-' }}</p>
            </div>
        </div>
    </div>
</body>
</html>
