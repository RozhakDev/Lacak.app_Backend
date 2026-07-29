<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRelatedSchoolScope;

class TracerWork extends Model
{
    use HasFactory, HasRelatedSchoolScope;

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
        return $this->belongsTo(TracerSubmission::class);
    }

    public function getSchoolRelationPath()
    {
        return 'tracerSubmission.alumniProfile.user';
    }
}
