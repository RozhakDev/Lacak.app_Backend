<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TracerEntrepreneur extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tracer_submission_id',
        'ownership_type',
        'employee_count',
        'monthly_omset_range',
        'business_type',
    ];

    protected function casts(): array
    {
        return [
            'employee_count' => 'integer',
        ];
    }

    public function tracerSubmission()
    {
        return $this->belongsTo(TracerSubmission::class);
    }
}
