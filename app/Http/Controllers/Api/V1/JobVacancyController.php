<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\JobVacancyService;
use App\Http\Resources\JobVacancyResource;
use App\Http\Requests\JobVacancy\ApplyJobRequest;
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

            return $this->paginatedResponse('Daftar lowongan berhasil dimuat.', $response);
        } catch (Exception $e) {
            return $this->errorResponse('Gagal memuat daftar lowongan pekerjaan.', [$e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $job = $this->jobService->getJobDetail((int) $id);
            return $this->successResponse('Detail lowongan berhasil dimuat.', new JobVacancyResource($job));
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), [], 404);
        } catch (Exception $e) {
            return $this->errorResponse('Terjadi kesalahan sistem.', [$e->getMessage()], 500);
        }
    }

    public function apply(ApplyJobRequest $request, $id): JsonResponse
    {

        try {
            $application = $this->jobService->applyToJob(
                (int) $id,
                auth()->id(),
                $request->file('cv'),
                $request->input('cover_letter')
            );

            return $this->successResponse('Berhasil melamar pekerjaan.', [
                'id' => $application->id,
                'status' => $application->status,
            ], 201);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), [], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }

    public function myApplications(Request $request): JsonResponse
    {
        $applications = $this->jobService->getMyApplications(auth()->id());

        $formatted = $applications->through(function ($app) {
            return [
                'id' => $app->id,
                'job_vacancy' => new JobVacancyResource($app->jobVacancy),
                'status' => $app->status,
                'cv_url' => asset('storage/' . $app->cv_url),
                'cover_letter' => $app->cover_letter,
                'applied_at' => $app->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->paginatedResponse('Daftar lamaran berhasil dimuat.', $formatted);
    }
}
