<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\JobVacancyService;
use App\Http\Resources\JobVacancyResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class JobVacancyController extends Controller
{
    protected $jobService;

    public function __construct(JobVacancyService $jobService)
    {
        $this->jobService = $jobService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $search = $request->query('search');
            $jobs = $this->jobService->getActiveJobs($search);

            $response = JobVacancyResource::collection($jobs);

            return $this->paginatedResponse('Daftar lowongan pekerjaan berhasil diambil', $response);
        } catch (Exception $e) {
            return $this->errorResponse('Terjadi kesalahan saat memuat lowongan.', [$e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $job = $this->jobService->getJobDetail((int) $id);
            return $this->successResponse('Detail lowongan pekerjaan', new JobVacancyResource($job));
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), [], 404);
        } catch (Exception $e) {
            return $this->errorResponse('Terjadi kesalahan.', [$e->getMessage()], 500);
        }
    }
}
