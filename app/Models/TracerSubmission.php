<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TracerSubmission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'alumni_profile_id',
        'status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    public function alumniProfile()
    {
        return $this->belongsTo(AlumniProfile::class);
    }

    public function work()
    {
        return $this->hasOne(TracerWork::class);
    }

    public function study()
    {
        return $this->hasOne(TracerStudy::class);
    }

    public function entrepreneur()
    {
        return $this->hasOne(TracerEntrepreneur::class);
    }
}
