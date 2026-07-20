<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TracerSubmission;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportTracerController extends Controller
{
    public function export(Request $request)
    {
        $fileName = 'tracer_study_export_' . date('Y_m_d_His') . '.csv';
        
        $submissions = TracerSubmission::with(['alumniProfile.user', 'alumniProfile.major', 'work', 'study', 'entrepreneur'])->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = [
            'ID',
            'Nama Alumni',
            'NISN',
            'Jurusan',
            'Tahun Lulus',
            'Status',
            'Tanggal Lapor',
            'Detail Pekerjaan / Kampus / Usaha'
        ];

        $callback = function() use($submissions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($submissions as $row) {
                $detail = '';
                if ($row->status === 'bekerja' && $row->work) {
                    $detail = "Posisi: {$row->work->position} di {$row->work->company_name}";
                } elseif ($row->status === 'kuliah' && $row->study) {
                    $detail = "Kampus: {$row->study->university_name}";
                } elseif ($row->status === 'wirausaha' && $row->entrepreneur) {
                    $detail = "Usaha: {$row->entrepreneur->business_type}";
                }

                fputcsv($file, [
                    $row->id,
                    $row->alumniProfile->user->name ?? '-',
                    $row->alumniProfile->user->nisn ?? '-',
                    $row->alumniProfile->major->name ?? '-',
                    $row->alumniProfile->graduation_year ?? '-',
                    ucfirst($row->status),
                    $row->submitted_at ? $row->submitted_at->format('Y-m-d') : '-',
                    $detail
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
