<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRelatedSchoolScope;

class TracerEntrepreneur extends Model
{
    use HasFactory, HasRelatedSchoolScope;

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

    public function getSchoolRelationPath()
    {
        return 'tracerSubmission.alumniProfile.user';
    }
}
