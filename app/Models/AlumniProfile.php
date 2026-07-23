<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumniProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'major_id',
        'graduation_year',
        'phone_number',
        'avatar_url',
        'about_me',
        'skills',
        'linkedin_url',
        'portfolio_url',
        'resume_url',
    ];

    protected $casts = [
        'graduation_year' => 'integer',
        'skills' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function major()
    {
        return $this->belongsTo(MasterMajor::class, 'major_id');
    }

    public function tracerSubmissions()
    {
        return $this->hasMany(TracerSubmission::class);
    }

    public function experiences()
    {
        return $this->hasMany(AlumniExperience::class);
    }
}
