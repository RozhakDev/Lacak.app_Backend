<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\TracerExportService;
use App\Support\AppLogger;
use Illuminate\Http\Request;
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
        if (!$request->user() || !$request->user()->hasAnyRole(['Super Admin', 'Admin BKK'])) {
            AppLogger::export()->warning('Akses export tidak sah ditolak.', AppLogger::context());
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        try {
            $fileName = 'lacakapp-tracer-study-' . date('Ymd-His') . '.xlsx';
            $data = $this->exportService->getExportData();
            $xlsx = SimpleXLSXGen::fromArray($data);

            AppLogger::export()->info('Export Tracer Study berhasil diunduh.', AppLogger::context([
                'filename' => $fileName,
                'row_count' => count($data) - 1,
                'school_id' => $request->user()->school_id,
                'role' => $request->user()->getRoleNames()->first(),
            ]));

            return response((string) $xlsx, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        } catch (\Exception $e) {
            AppLogger::export()->error('Export Tracer Study gagal.', AppLogger::context([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]));
            return response()->json(['message' => 'Gagal mengekspor data. Silakan coba lagi.'], 500);
        }
    }
}
