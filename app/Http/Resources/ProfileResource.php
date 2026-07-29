<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'nisn' => $this->user->nisn,
            ],
            'major' => [
                'id' => $this->major->id ?? null,
                'code' => $this->major->code ?? null,
                'name' => $this->major->name ?? null,
            ],
            'graduation_year' => $this->graduation_year,
            'phone_number' => $this->phone_number,
            'avatar_url' => $this->avatar_url ? url('storage/' . $this->avatar_url) : null,
            'about_me' => $this->about_me,
            'skills' => $this->skills ?? [],
            'linkedin_url' => $this->linkedin_url,
            'portfolio_url' => $this->portfolio_url,
            'resume_url' => $this->resume_url ? url('storage/' . $this->resume_url) : null,
            'experiences' => $this->whenLoaded('experiences', function () {
                return AlumniExperienceResource::collection($this->experiences);
            }),
        ];
    }
}
