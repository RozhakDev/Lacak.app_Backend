<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\TracerExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Shuchkin\SimpleXLSXGen;

class ExportTracerController extends Controller
{
    protected $exportService;

    public function __construct(TracerExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function export(Request $request)
    {
        $fileName = 'lacakapp-tracer-study-' . date('Ymd-His') . '.xlsx';
        
        $data = $this->exportService->getExportData();

        $xlsx = SimpleXLSXGen::fromArray($data);

        return response((string) $xlsx, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
