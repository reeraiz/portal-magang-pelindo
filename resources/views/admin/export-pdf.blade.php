<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Data Intern</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            color: black;
            padding: 20px;
        }

        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .print-table th, .print-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        .print-table th {
            background-color: #f3f4f6 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            @page {
                size: landscape;
                margin: 1cm;
            }
        }
    </style>
</head>
<body>

    <!-- Controls -->
    <div class="fixed top-4 right-4 no-print flex gap-3">
        <button onclick="window.close()" class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded hover:bg-gray-300">Tutup</button>
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white font-bold rounded hover:bg-blue-700 shadow flex items-center gap-2">
            Print / Save PDF
        </button>
    </div>

    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold uppercase mb-2">Daftar Intern (Anak Magang)</h2>
        <p class="text-sm">Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
        <p class="text-sm">Oleh: {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</p>
        <p class="text-sm font-bold mt-2">Total Intern: {{ count($interns) }}</p>
    </div>

    <table class="print-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 20%;">Nama Lengkap</th>
                <th style="width: 20%;">Asal Kampus</th>
                <th style="width: 20%;">Departemen & Divisi</th>
                <th style="width: 15%;">Mentor</th>
                <th style="width: 20%;">Periode Magang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($interns as $index => $intern)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $intern->name }}</td>
                    <td>{{ $intern->university->name ?? '-' }}</td>
                    <td>
                        {{ $intern->division ?? '-' }}
                        @if($intern->department)
                            <br>{{ $intern->department }}
                        @endif
                    </td>
                    <td>{{ $intern->mentor ? $intern->mentor->name : '-' }}</td>
                    <td>
                        @if($intern->internship_start_date && $intern->internship_end_date)
                            {{ \Carbon\Carbon::parse($intern->internship_start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($intern->internship_end_date)->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
