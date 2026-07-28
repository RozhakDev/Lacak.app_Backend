<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasSchoolScope;

class MasterMajor extends Model
{
    use HasFactory, SoftDeletes, HasSchoolScope;

    protected $fillable = [
        'school_id',
        'name',
        'code',
    ];

    public function alumniProfiles()
    {
        return $this->hasMany(AlumniProfile::class, 'major_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
