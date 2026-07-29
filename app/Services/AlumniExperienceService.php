<?php

namespace App\Services;

use App\Models\User;
use App\Models\AlumniExperience;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class AlumniExperienceService
{
    public function storeExperience(User $user, array $data): AlumniExperience
    {
        $profile = $user->alumniProfile;

        if (!$profile) {
            throw new Exception('Silakan lengkapi profil utama Anda terlebih dahulu.', 400);
        }

        return $profile->experiences()->create($data);
    }

    public function updateExperience(User $user, int $id, array $data): AlumniExperience
    {
        $profile = $user->alumniProfile;

        if (!$profile) {
            throw new Exception('Silakan lengkapi profil utama Anda terlebih dahulu.', 400);
        }

        $experience = $profile->experiences()->find($id);

        if (!$experience) {
            throw new ModelNotFoundException('Pengalaman tidak ditemukan.');
        }

        $experience->update($data);

        return $experience;
    }

    public function deleteExperience(User $user, int $id): void
    {
        $profile = $user->alumniProfile;

        if (!$profile) {
            throw new Exception('Silakan lengkapi profil utama Anda terlebih dahulu.', 400);
        }

        $experience = $profile->experiences()->find($id);

        if (!$experience) {
            throw new ModelNotFoundException('Pengalaman tidak ditemukan.');
        }

        $experience->delete();
    }
}
