<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AlumniExperience;
use App\Http\Requests\AlumniExperience\StoreAlumniExperienceRequest;
use App\Http\Requests\AlumniExperience\UpdateAlumniExperienceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AlumniExperienceController extends Controller
{
    public function store(StoreAlumniExperienceRequest $request): JsonResponse
    {
        $profile = $request->user()->alumniProfile;

        if (!$profile) {
            return $this->errorResponse('Silakan lengkapi profil utama Anda terlebih dahulu.', [], 400);
        }

        $validated = $request->validated();

        $experience = $profile->experiences()->create($validated);

        return $this->successResponse('Pengalaman berhasil ditambahkan.', $experience, 201);
    }

    public function update(UpdateAlumniExperienceRequest $request, $id): JsonResponse
    {
        $profile = $request->user()->alumniProfile;

        if (!$profile) {
            return $this->errorResponse('Silakan lengkapi profil utama Anda terlebih dahulu.', [], 400);
        }

        $experience = $profile->experiences()->find($id);

        if (!$experience) {
            return $this->errorResponse('Pengalaman tidak ditemukan.', [], 404);
        }

        $validated = $request->validated();

        $experience->update($validated);

        return $this->successResponse('Pengalaman berhasil diperbarui.', $experience);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $profile = $request->user()->alumniProfile;

        if (!$profile) {
            return $this->errorResponse('Silakan lengkapi profil utama Anda terlebih dahulu.', [], 400);
        }

        $experience = $profile->experiences()->find($id);

        if (!$experience) {
            return $this->errorResponse('Pengalaman tidak ditemukan.', [], 404);
        }

        $experience->delete();

        return $this->successResponse('Pengalaman berhasil dihapus.');
    }
}
