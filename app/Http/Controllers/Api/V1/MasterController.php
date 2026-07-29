<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\MasterDataService;
use Illuminate\Http\JsonResponse;

class MasterController extends Controller
{
    protected $masterDataService;

    public function __construct(MasterDataService $masterDataService)
    {
        $this->masterDataService = $masterDataService;
    }

    public function getSchools(): JsonResponse
    {
        return $this->successResponse(
            'Data sekolah berhasil dimuat.',
            $this->masterDataService->getActiveSchools()
        );
    }

    public function getMajors(): JsonResponse
    {
        $tenantId = auth()->check() ? auth()->user()->school_id : null;
        
        return $this->successResponse(
            'Daftar jurusan berhasil dimuat.', 
            $this->masterDataService->getMajors($tenantId)
        );
    }

    public function getTracerOptions(): JsonResponse
    {
        return $this->successResponse(
            'Opsi form tracer berhasil dimuat.', 
            $this->masterDataService->getTracerOptions()
        );
    }
}
