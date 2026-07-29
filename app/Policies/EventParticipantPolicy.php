<?php

namespace App\Policies;

use App\Models\EventParticipant;
use App\Models\User;

class EventParticipantPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, EventParticipant $eventParticipant): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, EventParticipant $eventParticipant): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can('update', $eventParticipant->event);
    }

    public function delete(User $user, EventParticipant $eventParticipant): bool
    {
        return $this->update($user, $eventParticipant);
    }

    public function restore(User $user, EventParticipant $eventParticipant): bool
    {
        return $this->update($user, $eventParticipant);
    }

    public function forceDelete(User $user, EventParticipant $eventParticipant): bool
    {
        return false;
    }
}
