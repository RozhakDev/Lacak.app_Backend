<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobVacancyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'company_name' => $this->company_name,
            'images' => collect($this->images ?? [])->map(fn($path) => asset('storage/' . $path))->toArray(),
            'description' => $this->description,
            'requirements' => $this->requirements,
            'is_active' => $this->is_active,
            'expires_at' => $this->expires_at ? $this->expires_at->format('Y-m-d') : null,
            'posted_at' => $this->created_at->format('Y-m-d'),
            'posted_by' => $this->whenLoaded('creator', function () {
                return $this->creator->name;
            }),
            'is_applied' => $this->when(isset($this->is_applied), (bool) $this->is_applied),
        ];
    }
}
