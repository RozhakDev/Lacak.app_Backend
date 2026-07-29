<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;

class JobApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, JobApplication $jobApplication): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, JobApplication $jobApplication): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can('update', $jobApplication->jobVacancy);
    }

    public function delete(User $user, JobApplication $jobApplication): bool
    {
        return $this->update($user, $jobApplication);
    }

    public function restore(User $user, JobApplication $jobApplication): bool
    {
        return $this->update($user, $jobApplication);
    }

    public function forceDelete(User $user, JobApplication $jobApplication): bool
    {
        return false;
    }
}
