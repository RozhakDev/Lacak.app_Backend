<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AlumniExperienceService;
use App\Http\Requests\AlumniExperience\StoreAlumniExperienceRequest;
use App\Http\Requests\AlumniExperience\UpdateAlumniExperienceRequest;
use App\Http\Resources\AlumniExperienceResource;
use App\Support\AppLogger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class AlumniExperienceController extends Controller
{
    protected $experienceService;

    public function __construct(AlumniExperienceService $experienceService)
    {
        $this->experienceService = $experienceService;
    }

    public function store(StoreAlumniExperienceRequest $request): JsonResponse
    {
        try {
            $experience = $this->experienceService->storeExperience($request->user(), $request->validated());

            AppLogger::api()->info('Pengalaman alumni berhasil ditambahkan.', AppLogger::context([
                'experience_id' => $experience->id,
            ]));

            return $this->successResponse('Pengalaman berhasil ditambahkan.', new AlumniExperienceResource($experience), 201);
        } catch (Exception $e) {
            AppLogger::api()->error('Gagal menambahkan pengalaman alumni.', AppLogger::context([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]));
            return $this->errorResponse($e->getMessage(), [], $e->getCode() ?: 400);
        }
    }

    public function update(UpdateAlumniExperienceRequest $request, $id): JsonResponse
    {
        try {
            $experience = $this->experienceService->updateExperience($request->user(), (int) $id, $request->validated());

            AppLogger::api()->info('Pengalaman alumni berhasil diperbarui.', AppLogger::context([
                'experience_id' => $id,
            ]));

            return $this->successResponse('Pengalaman berhasil diperbarui.', new AlumniExperienceResource($experience));
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Pengalaman tidak ditemukan.', [], 404);
        } catch (Exception $e) {
            AppLogger::api()->error('Gagal memperbarui pengalaman alumni.', AppLogger::context([
                'experience_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]));
            return $this->errorResponse($e->getMessage(), [], $e->getCode() ?: 400);
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $this->experienceService->deleteExperience($request->user(), (int) $id);

            AppLogger::api()->info('Pengalaman alumni berhasil dihapus.', AppLogger::context([
                'experience_id' => $id,
            ]));

            return $this->successResponse('Pengalaman berhasil dihapus.');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Pengalaman tidak ditemukan.', [], 404);
        } catch (Exception $e) {
            AppLogger::api()->error('Gagal menghapus pengalaman alumni.', AppLogger::context([
                'experience_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]));
            return $this->errorResponse($e->getMessage(), [], $e->getCode() ?: 400);
        }
    }
}
