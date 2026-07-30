<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\TracerService;
use App\Http\Requests\Tracer\StoreTracerSubmissionRequest;
use App\Http\Resources\TracerSubmissionResource;
use App\Support\AppLogger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class TracerController extends Controller
{
    protected $tracerService;

    public function __construct(TracerService $tracerService)
    {
        $this->tracerService = $tracerService;
    }

    public function store(StoreTracerSubmissionRequest $request): JsonResponse
    {
        try {
            $submission = $this->tracerService->submitTracer($request->user(), $request->validated());

            AppLogger::api()->info('Tracer study berhasil disimpan.', AppLogger::context([
                'submission_id' => $submission->id,
                'status' => $submission->status,
            ]));

            return $this->successResponse('Data Tracer Study berhasil disimpan.', new TracerSubmissionResource($submission), 201);
        } catch (Exception $e) {
            $code = $e->getCode();
            $code = (is_numeric($code) && $code >= 100 && $code <= 599) ? (int)$code : 500;

            AppLogger::api()->error('Gagal menyimpan tracer study.', AppLogger::context([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]));

            return $this->errorResponse($e->getMessage(), [], $code);
        }
    }

    public function show(Request $request): JsonResponse
    {
        try {
            $submission = $this->tracerService->getSubmission($request->user());

            return $this->successResponse('Data Tracer Study berhasil diambil.', new TracerSubmissionResource($submission));
        } catch (Exception $e) {
            $code = $e->getCode();
            $code = (is_numeric($code) && $code >= 100 && $code <= 599) ? (int)$code : 500;

            AppLogger::api()->warning('Tracer study tidak ditemukan.', AppLogger::context([
                'reason' => $e->getMessage(),
            ]));

            return $this->errorResponse($e->getMessage(), [], $code);
        }
    }
}
