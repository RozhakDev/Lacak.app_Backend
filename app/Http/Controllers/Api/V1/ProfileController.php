<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\TracerService;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $tracerService;

    public function __construct(TracerService $tracerService)
    {
        $this->tracerService = $tracerService;
    }

    public function show(Request $request): JsonResponse
    {
        $profile = $request->user()->alumniProfile;
        
        if (!$profile) {
            return $this->errorResponse('Profil belum dilengkapi.', [], 404);
        }

        return $this->successResponse('Data profil alumni', new ProfileResource($profile));
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $profile = $this->tracerService->updateProfile($request->user(), $request->validated());
        
        return $this->successResponse('Profil berhasil diperbarui.', new ProfileResource($profile));
    }
}
