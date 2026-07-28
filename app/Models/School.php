<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'email', 'phone', 'address', 'logo', 'is_active'
    ];

    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget('master_schools_active');
        });

        static::deleted(function ($model) {
            Cache::forget('master_schools_active');
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function jobVacancies()
    {
        return $this->hasMany(JobVacancy::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function majors()
    {
        return $this->hasMany(MasterMajor::class);
    }
}
