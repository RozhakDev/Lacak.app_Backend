<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterMajor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
    ];

    public function alumniProfiles()
    {
        return $this->hasMany(AlumniProfile::class, 'major_id');
    }
}
