<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TracerStudy extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tracer_submission_id',
        'university_name',
        'enrollment_date',
        'is_linear',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
            'is_linear' => 'boolean',
        ];
    }

    public function tracerSubmission()
    {
        return $this->belongsTo(TracerSubmission::class);
    }
}
