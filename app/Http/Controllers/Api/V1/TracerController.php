<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\TracerService;
use App\Http\Requests\Tracer\StoreTracerSubmissionRequest;
use App\Http\Resources\TracerSubmissionResource;
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
            
            return $this->successResponse('Data tracer study berhasil disimpan.', new TracerSubmissionResource($submission), 201);
        } catch (Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->errorResponse($e->getMessage(), [], $code);
        }
    }
}
