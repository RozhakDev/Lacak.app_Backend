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

    public function apply(Request $request, $id): JsonResponse
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|string|max:1000',
        ]);

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
        $applications = \App\Models\JobApplication::with('jobVacancy')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

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
