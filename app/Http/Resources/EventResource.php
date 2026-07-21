<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'event_type' => $this->event_type,
            'location_type' => $this->location_type,
            'location_details' => $this->location_details,
            'start_date' => $this->start_date ? $this->start_date->format('Y-m-d H:i:s') : null,
            'end_date' => $this->end_date ? $this->end_date->format('Y-m-d H:i:s') : null,
            'banner_url' => $this->banner_url ? asset('storage/' . $this->banner_url) : null,
            'is_active' => $this->is_active,
            'posted_by' => $this->whenLoaded('creator', function () {
                return $this->creator->name;
            }),
            'is_registered' => $this->when(isset($this->is_registered), (bool) $this->is_registered),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
