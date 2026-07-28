<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'email', 'phone', 'address', 'logo', 'is_active'
    ];

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
