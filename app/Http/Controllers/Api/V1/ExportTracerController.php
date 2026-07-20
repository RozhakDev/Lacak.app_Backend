<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TracerSubmission;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Shuchkin\SimpleXLSXGen;

class ExportTracerController extends Controller
{
    public function export(Request $request)
    {
        $fileName = 'lacakapp-tracer-study-' . date('Ymd-His') . '.xlsx';
        
        $submissions = TracerSubmission::with(['alumniProfile.user', 'alumniProfile.major', 'work', 'study', 'entrepreneur'])->get();

        $data = [];

        $data[] = [
            '<center><style bgcolor="#0f172a" color="#ffffff"><b>ID</b></style></center>',
            '<center><style bgcolor="#0f172a" color="#ffffff"><b>Nama Alumni</b></style></center>',
            '<center><style bgcolor="#0f172a" color="#ffffff"><b>NISN</b></style></center>',
            '<center><style bgcolor="#0f172a" color="#ffffff"><b>Jurusan</b></style></center>',
            '<center><style bgcolor="#0f172a" color="#ffffff"><b>Tahun Lulus</b></style></center>',
            '<center><style bgcolor="#0f172a" color="#ffffff"><b>Status</b></style></center>',
            '<center><style bgcolor="#0f172a" color="#ffffff"><b>Tanggal Lapor</b></style></center>',
            '<center><style bgcolor="#0f172a" color="#ffffff"><b>Detail Pekerjaan / Kampus / Usaha</b></style></center>'
        ];

        foreach ($submissions as $row) {
            $detail = '';
            if ($row->status === 'bekerja' && $row->work) {
                $detail = "Posisi: {$row->work->position} di {$row->work->company_name}";
            } elseif ($row->status === 'kuliah' && $row->study) {
                $detail = "Kampus: {$row->study->university_name}";
            } elseif ($row->status === 'wirausaha' && $row->entrepreneur) {
                $detail = "Usaha: {$row->entrepreneur->business_type}";
            }

            $data[] = [
                $row->id,
                $row->alumniProfile->user->name ?? '-',
                $row->alumniProfile->user->nisn ?? '-',
                $row->alumniProfile->major->name ?? '-',
                $row->alumniProfile->graduation_year ?? '-',
                ucfirst($row->status),
                $row->submitted_at ? $row->submitted_at->format('Y-m-d') : '-',
                $detail
            ];
        }

        $xlsx = SimpleXLSXGen::fromArray($data);

        return response((string) $xlsx, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
