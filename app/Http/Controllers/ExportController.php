<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    /**
     * Terapkan scope sesuai role. 
     * Mentor hanya melihat intern yang di-assign padanya.
     */
    private function getInternsQuery(Request $request)
    {
        $user = Auth::user();
        $query = User::where('role', 'intern')->with(['university', 'mentor']);

        if ($user->role === 'pembimbing') {
            $query->where('mentor_id', $user->id);
        }

        if ($request->has('ids') && !empty($request->ids)) {
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        }

        return $query;
    }

    /**
     * Export interns to CSV (Excel readable).
     */
    public function exportExcel(Request $request)
    {
        $interns = $this->getInternsQuery($request)->get();

        $fileName = 'data_interns_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Nama', 'Asal Kampus', 'Departemen & Divisi', 'Mentor', 'Periode Magang'];

        $callback = function() use($interns, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 correctly in Excel
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));

            // Add Total Data row
            fputcsv($file, ['Total Intern:', count($interns)], ';');
            fputcsv($file, [], ';'); // Empty row for spacing

            fputcsv($file, $columns, ';'); // Use semicolon for Indonesian excel compatibility

            $no = 1;
            foreach ($interns as $intern) {
                $divDept = '';
                if ($intern->division) {
                    $divDept .= $intern->division;
                }
                if ($intern->department) {
                    $divDept .= ($divDept ? ' - ' : '') . $intern->department;
                }

                $mentorName = $intern->mentor ? $intern->mentor->name : '-';
                
                $periode = '-';
                if ($intern->internship_start_date && $intern->internship_end_date) {
                    $periode = \Carbon\Carbon::parse($intern->internship_start_date)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($intern->internship_end_date)->format('d/m/Y');
                }

                fputcsv($file, [
                    $no++,
                    $intern->name,
                    $intern->university->name ?? '-',
                    $divDept ?: '-',
                    $mentorName,
                    $periode
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Print View for PDF export.
     */
    public function exportPdf(Request $request)
    {
        $interns = $this->getInternsQuery($request)->get();
        return view('admin.export-pdf', compact('interns'));
    }
}
