<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumniExperience extends Model
{
    protected $fillable = [
        'alumni_profile_id',
        'company_name',
        'position',
        'description',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function alumniProfile()
    {
        return $this->belongsTo(AlumniProfile::class);
    }
}
