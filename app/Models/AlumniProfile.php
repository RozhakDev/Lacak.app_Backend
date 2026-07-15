<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumniProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'major_id',
        'graduation_year',
        'phone_number',
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
}
