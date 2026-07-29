<?php

namespace App\Policies;

use App\Models\MasterMajor;
use App\Models\User;

class MasterMajorPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MasterMajor $masterMajor): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin BKK');
    }

    public function update(User $user, MasterMajor $masterMajor): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->hasRole('Admin BKK') && 
               $masterMajor->school_id !== null && 
               $masterMajor->school_id === $user->school_id;
    }

    public function delete(User $user, MasterMajor $masterMajor): bool
    {
        return $this->update($user, $masterMajor);
    }

    public function restore(User $user, MasterMajor $masterMajor): bool
    {
        return $this->update($user, $masterMajor);
    }

    public function forceDelete(User $user, MasterMajor $masterMajor): bool
    {
        return false;
    }
}
