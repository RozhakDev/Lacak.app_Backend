<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TracerWork extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tracer_submission_id',
        'location_scale',
        'location_country',
        'field_of_work',
        'salary_range',
        'company_name',
        'position',
        'start_date',
        'is_linear',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'is_linear' => 'boolean',
        ];
    }

    public function tracerSubmission()
    {
        return $this->belongsTo(tracerSubmission::class);
    }
}
