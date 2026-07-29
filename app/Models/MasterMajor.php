<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSchoolScope;
use Illuminate\Support\Facades\Cache;

class MasterMajor extends Model
{
    use HasFactory, HasSchoolScope;

    protected $fillable = [
        'school_id',
        'name',
        'code',
    ];

    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget("master_majors_{$model->school_id}");
        });

        static::deleted(function ($model) {
            Cache::forget("master_majors_{$model->school_id}");
        });
    }

    public function alumniProfiles()
    {
        return $this->hasMany(AlumniProfile::class, 'major_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
