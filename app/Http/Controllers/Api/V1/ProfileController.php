<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\TracerService;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Support\AppLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class ProfileController extends Controller
{
    protected $tracerService;

    public function __construct(TracerService $tracerService)
    {
        $this->tracerService = $tracerService;
    }

    public function show(Request $request): JsonResponse
    {
        $profile = $request->user()->alumniProfile()->with(['experiences', 'user', 'major'])->first();

        if (!$profile) {
            AppLogger::api()->info('Profil alumni tidak ditemukan.', AppLogger::context());
            return $this->errorResponse('Profil tidak ditemukan atau belum lengkap.', [], 404);
        }

        return $this->successResponse('Data profil berhasil diambil.', new ProfileResource($profile));
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $isNew = !$request->user()->alumniProfile()->exists();
            $profile = $this->tracerService->updateProfile($request->user(), $request->validated());
            $profile->load(['experiences', 'user', 'major']);

            $statusCode = $isNew ? 201 : 200;
            $message = $isNew ? 'Profil berhasil dibuat.' : 'Profil berhasil diperbarui.';

            AppLogger::api()->info($message, AppLogger::context([
                'profile_id' => $profile->id,
                'is_new' => $isNew,
            ]));

            return $this->successResponse($message, new ProfileResource($profile), $statusCode);
        } catch (Exception $e) {
            AppLogger::api()->error('Gagal memperbarui profil alumni.', AppLogger::context([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]));
            return $this->errorResponse('Gagal memperbarui profil. Silakan coba lagi.', [], 500);
        }
    }
}
