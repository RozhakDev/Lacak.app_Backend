<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSchoolScope;
use \App\Models\Scopes\SchoolScope;

class JobVacancy extends Model
{
    use HasFactory, HasSchoolScope;

    protected $fillable = [
        'school_id',
        'created_by',
        'title',
        'company_name',
        'images',
        'description',
        'requirements',
        'is_active',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'expires_at' => 'date',
            'images' => 'array',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withoutGlobalScope(SchoolScope::class);
    }

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
