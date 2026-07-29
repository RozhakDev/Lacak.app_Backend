<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AlumniExperienceService;
use App\Http\Requests\AlumniExperience\StoreAlumniExperienceRequest;
use App\Http\Requests\AlumniExperience\UpdateAlumniExperienceRequest;
use App\Http\Resources\AlumniExperienceResource;
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
            return $this->successResponse('Pengalaman berhasil ditambahkan.', new AlumniExperienceResource($experience), 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), [], $e->getCode() ?: 400);
        }
    }

    public function update(UpdateAlumniExperienceRequest $request, $id): JsonResponse
    {
        try {
            $experience = $this->experienceService->updateExperience($request->user(), (int) $id, $request->validated());
            return $this->successResponse('Pengalaman berhasil diperbarui.', new AlumniExperienceResource($experience));
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Pengalaman tidak ditemukan.', [], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), [], $e->getCode() ?: 400);
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $this->experienceService->deleteExperience($request->user(), (int) $id);
            return $this->successResponse('Pengalaman berhasil dihapus.');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Pengalaman tidak ditemukan.', [], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), [], $e->getCode() ?: 400);
        }
    }
}
