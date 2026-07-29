<?php

namespace App\Policies;

use App\Models\JobVacancy;
use App\Models\User;

class JobVacancyPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, JobVacancy $jobVacancy): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin BKK');
    }

    public function update(User $user, JobVacancy $jobVacancy): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        $creator = User::find($jobVacancy->created_by);
        if ($creator && $creator->hasRole('Super Admin')) {
            return false;
        }

        return $user->hasRole('Admin BKK') && 
               $jobVacancy->school_id !== null && 
               $jobVacancy->school_id === $user->school_id;
    }

    public function delete(User $user, JobVacancy $jobVacancy): bool
    {
        return $this->update($user, $jobVacancy);
    }

    public function restore(User $user, JobVacancy $jobVacancy): bool
    {
        return $this->update($user, $jobVacancy);
    }

    public function forceDelete(User $user, JobVacancy $jobVacancy): bool
    {
        return false;
    }
}
