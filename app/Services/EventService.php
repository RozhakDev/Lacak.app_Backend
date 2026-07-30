<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\AlumniProfile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class EventService
{
    public function getActiveEvents(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        $query = Event::with('creator')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now()->toDateString());
            });

        if (auth()->check()) {
            $query->withExists(['participants as is_registered' => function ($q) {
                $q->where('user_id', auth()->id());
            }]);
        }

        if (!empty($search)) {
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->latest('start_date')->paginate($perPage);
    }

    public function getEventDetail(int $id): Event
    {
        $query = Event::with('creator')->where('is_active', true);

        if (auth()->check()) {
            $query->withExists(['participants as is_registered' => function ($q) {
                $q->where('user_id', auth()->id());
            }]);
        }

        $event = $query->find($id);

        if (!$event) {
            throw new ModelNotFoundException('Event/Kegiatan tidak ditemukan atau sudah tidak aktif.');
        }

        return $event;
    }

    public function registerEvent(int $eventId, int $userId): EventParticipant
    {
        $hasProfile = AlumniProfile::where('user_id', $userId)->exists();
        if (!$hasProfile) {
            throw new Exception('Silakan lengkapi profil alumni Anda terlebih dahulu sebelum mendaftar kegiatan.');
        }

        $event = $this->getEventDetail($eventId);

        $existing = EventParticipant::where('event_id', $eventId)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            throw new Exception('Anda sudah terdaftar di kegiatan ini.');
        }

        return EventParticipant::create([
            'event_id' => $eventId,
            'user_id' => $userId,
            'status' => 'registered'
        ]);
    }

    public function getMyEvents(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return EventParticipant::with('event.creator')
            ->whereHas('event', function ($query) {
                $query->where('is_active', true);
            })
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }
}
