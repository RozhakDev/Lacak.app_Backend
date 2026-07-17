<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MasterMajor;
use App\Http\Resources\MasterMajorResource;
use Illuminate\Http\JsonResponse;

class MasterController extends Controller
{
    public function getMajors(): JsonResponse
    {
        $majors = MasterMajor::orderBy('name', 'asc')->get();

        return $this->successResponse(
            'Data Master Jurusan berhasil diambil', 
            MasterMajorResource::collection($majors)
        );
    }
}
