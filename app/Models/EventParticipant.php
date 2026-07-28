<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRelatedSchoolScope;

class EventParticipant extends Model
{
    use HasFactory, HasRelatedSchoolScope;

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
