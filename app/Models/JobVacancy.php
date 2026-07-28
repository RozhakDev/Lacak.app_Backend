<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasSchoolScope;

class JobVacancy extends Model
{
    use HasFactory, SoftDeletes, HasSchoolScope;

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
        return $this->belongsTo(User::class, 'created_by');
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
