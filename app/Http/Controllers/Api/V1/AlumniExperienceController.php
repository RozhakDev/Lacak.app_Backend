<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AlumniExperience;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AlumniExperienceController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $profile = $request->user()->alumniProfile;

        if (!$profile) {
            return $this->errorResponse('Silakan lengkapi profil utama Anda terlebih dahulu.', [], 400);
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_current' => ['boolean'],
        ]);

        $experience = $profile->experiences()->create($validated);

        return $this->successResponse('Pengalaman berhasil ditambahkan.', $experience, 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $profile = $request->user()->alumniProfile;

        if (!$profile) {
            return $this->errorResponse('Silakan lengkapi profil utama Anda terlebih dahulu.', [], 400);
        }

        $experience = $profile->experiences()->find($id);

        if (!$experience) {
            return $this->errorResponse('Pengalaman tidak ditemukan.', [], 404);
        }

        $validated = $request->validate([
            'company_name' => ['sometimes', 'required', 'string', 'max:255'],
            'position' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_current' => ['boolean'],
        ]);

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
