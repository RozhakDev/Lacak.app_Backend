<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TracerSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at,

            'detail_bekerja' => $this->when($this->status === 'bekerja', $this->work),
            'detail_kuliah' => $this->when($this->status === 'kuliah', $this->study),
            'detail_wirausaha' => $this->when($this->status === 'wirausaha', $this->entrepreneur),
        ];
    }
}
