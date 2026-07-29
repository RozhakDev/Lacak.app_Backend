<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Event $event): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin BKK');
    }

    public function update(User $user, Event $event): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        $creator = User::find($event->created_by);
        if ($creator && $creator->hasRole('Super Admin')) {
            return false;
        }

        return $user->hasRole('Admin BKK') && 
               $event->school_id !== null && 
               $event->school_id === $user->school_id;
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }

    public function restore(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }

    public function forceDelete(User $user, Event $event): bool
    {
        return false;
    }
}
