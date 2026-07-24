<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curriculum Vitae - {{ $intern->name }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman:wght@400;700&display=swap');
        
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .a4-page {
            width: 210mm;
            min-height: 297mm;
            background: white;
            margin: 20px auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 15mm;
            position: relative;
            /* Border as in the screenshot */
            border: 2px solid black;
        }

        .section-title {
            font-size: 1.125rem; /* text-lg */
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 3px double black;
            display: block;
            margin-bottom: 1rem;
        }

        .section-wrapper {
            margin-bottom: 1.5rem;
        }


        .print-border-top, .print-border-bottom, .print-border-left, .print-border-right {
            display: none;
        }

        @media print {
            body {
                background: white;
            }
            .a4-page {
                margin: 0;
                box-shadow: none;
                width: 100%;
                min-height: auto;
                padding: 15mm;
                border: none; /* Handled by fixed borders in print */
            }
            .print-border-top, .print-border-bottom, .print-border-left, .print-border-right {
                display: block;
                position: fixed;
                background: black;
                z-index: 1000;
            }
            .print-border-top { top: 0; left: 0; right: 0; height: 2px; }
            .print-border-bottom { bottom: 0; left: 0; right: 0; height: 2px; }
            .print-border-left { top: 0; bottom: 0; left: 0; width: 2px; }
            .print-border-right { top: 0; bottom: 0; right: 0; width: 2px; }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 5mm; /* Small margin so the border shows well */
            }
        }
    </style>
</head>
<body>
    
    <!-- Print Borders -->
    <div class="print-border-top"></div>
    <div class="print-border-bottom"></div>
    <div class="print-border-left"></div>
    <div class="print-border-right"></div>

    <!-- Controls -->
    <div class="fixed top-4 right-4 no-print flex gap-3">
        <button onclick="window.close()" class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded hover:bg-gray-300">Tutup</button>
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white font-bold rounded hover:bg-blue-700 shadow flex items-center gap-2">
            Print / Save PDF
        </button>
    </div>

    <!-- CV Layout -->
    <div class="a4-page text-black">
        
        <!-- Header -->
        <h1 class="text-3xl font-bold uppercase mb-6 tracking-wide leading-none pt-1">CURRICULUM VITAE</h1>

        <!-- Data Pribadi Section -->
        <div class="section-wrapper">
            <div class="flex justify-between items-start gap-4">
                <!-- Left Column: Title and Table -->
                <div class="flex-1">
                    <div class="section-title">DATA PRIBADI</div>
                    
                    <table class="w-full text-[15px] table-fixed mt-2">
                        <tbody>
                            <tr>
                                <td class="py-1 align-top w-48">Nama</td>
                                <td class="py-1 px-2 align-top w-4">:</td>
                                <td class="py-1 text-black align-top">{{ $intern->name }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 align-top w-48">Tempat, Tanggal Lahir</td>
                                <td class="py-1 px-2 align-top w-4">:</td>
                                <td class="py-1 text-black align-top">
                                    {{ $intern->birth_place ?? '-' }}@if($intern->birth_place && $intern->birth_date), @endif
                                    {{ $intern->birth_date ? \Carbon\Carbon::parse($intern->birth_date)->translatedFormat('d F Y') : ($intern->birth_place ? '' : '-') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 align-top w-48">Jenis Kelamin</td>
                                <td class="py-1 px-2 align-top w-4">:</td>
                                <td class="py-1 text-black align-top">{{ $intern->gender->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 align-top w-48">Agama</td>
                                <td class="py-1 px-2 align-top w-4">:</td>
                                <td class="py-1 text-black align-top">{{ $intern->religion ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 align-top w-48">Kewarganegaraan</td>
                                <td class="py-1 px-2 align-top w-4">:</td>
                                <td class="py-1 text-black align-top">{{ $intern->citizenship ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 align-top w-48">Alamat</td>
                                <td class="py-1 px-2 align-top w-4">:</td>
                                <td class="py-1 text-black align-top whitespace-pre-line">{{ $intern->address ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 align-top w-48">Handphone</td>
                                <td class="py-1 px-2 align-top w-4">:</td>
                                <td class="py-1 text-black align-top">{{ $intern->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 align-top w-48">E_Mail</td>
                                <td class="py-1 px-2 align-top w-4">:</td>
                                <td class="py-1 text-black align-top">{{ $intern->email }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div> <!-- Close Left Column -->

                <!-- Photo on the right -->
                <div class="w-40 h-56 shrink-0 border border-gray-400 bg-red-600 flex items-center justify-center overflow-hidden">
                    @if($intern->avatar)
                        <img src="{{ asset('storage/' . $intern->avatar) }}" alt="Foto {{ $intern->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-red-600 flex items-center justify-center text-white text-xs text-center p-2">
                            Pas Foto<br>3x4
                        </div>
                    @endif
                </div>
            </div> <!-- Close Flex Container -->
        </div> <!-- Close Section Wrapper -->

        <!-- Data Pendidikan Section -->
        <div class="section-wrapper">
            <div class="section-title">DATA PENDIDIKAN</div>
            <table class="w-full text-[15px] table-fixed mt-2">
                <tbody>
                    <tr>
                        <td class="py-1 align-top w-48">Tingkat Pendidikan</td>
                        <td class="py-1 px-2 align-top w-4">:</td>
                        <td class="py-1 text-black align-top">{{ $intern->educationLevel->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 align-top w-48">Perguruan Tinggi</td>
                        <td class="py-1 px-2 align-top w-4">:</td>
                        <td class="py-1 text-black align-top uppercase">
                            {{ $intern->university->name ?? '-' }}
                            @if($intern->education_start_year && $intern->education_end_year)
                                ({{ $intern->education_start_year }} - {{ $intern->education_end_year }})
                            @endif
                        </td>
                    </tr>
                    @if($intern->faculty)
                    <tr>
                        <td class="py-1 align-top w-48">Fakultas</td>
                        <td class="py-1 px-2 align-top w-4">:</td>
                        <td class="py-1 text-black align-top">{{ $intern->faculty }}</td>
                    </tr>
                    @endif
                    @if($intern->major)
                    <tr>
                        <td class="py-1 align-top w-48">Jurusan</td>
                        <td class="py-1 px-2 align-top w-4">:</td>
                        <td class="py-1 text-black align-top">{{ $intern->major }}</td>
                    </tr>
                    @endif
                    @if($intern->study_program)
                    <tr>
                        <td class="py-1 align-top w-48">Program Studi</td>
                        <td class="py-1 px-2 align-top w-4">:</td>
                        <td class="py-1 text-black align-top">{{ $intern->study_program }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Informasi Penempatan Section -->
        <div class="section-wrapper">
            <div class="section-title">INFORMASI PENEMPATAN</div>
            <table class="w-full text-[15px] table-fixed mt-2">
                <tbody>
                    <tr>
                        <td class="py-1 align-top w-48">Divisi / Departemen</td>
                        <td class="py-1 px-2 align-top w-4">:</td>
                        <td class="py-1 text-black align-top">
                            {{ $intern->division ?? '-' }}
                            @if($intern->department)
                                <br>{{ $intern->department }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 align-top w-48">Jenis Magang</td>
                        <td class="py-1 px-2 align-top w-4">:</td>
                        <td class="py-1 text-black align-top">{{ $intern->internshipType->name ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
