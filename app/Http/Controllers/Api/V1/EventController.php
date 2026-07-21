<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $search = $request->query('search');
            $events = $this->eventService->getActiveEvents($search);

            $response = EventResource::collection($events);

            return $this->paginatedResponse('Daftar kegiatan berhasil dimuat.', $response);
        } catch (Exception $e) {
            return $this->errorResponse('Gagal memuat daftar kegiatan.', [$e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $event = $this->eventService->getEventDetail((int) $id);
            return $this->successResponse('Detail kegiatan berhasil dimuat.', new EventResource($event));
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), [], 404);
        } catch (Exception $e) {
            return $this->errorResponse('Terjadi kesalahan sistem.', [$e->getMessage()], 500);
        }
    }

    public function register(Request $request, $id): JsonResponse
    {
        try {
            $participant = $this->eventService->registerEvent((int) $id, auth()->id());

            return $this->successResponse('Berhasil mendaftar kegiatan.', [
                'event_id' => $participant->event_id,
                'status' => $participant->status,
            ], 201);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), [], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }

    public function myEvents(Request $request): JsonResponse
    {
        $participants = $this->eventService->getMyEvents(auth()->id());

        $formatted = $participants->through(function ($participant) {
            return [
                'id' => $participant->id,
                'event' => new EventResource($participant->event),
                'status' => $participant->status,
                'registered_at' => $participant->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->paginatedResponse('Daftar kegiatan yang diikuti berhasil dimuat.', $formatted);
    }
}
