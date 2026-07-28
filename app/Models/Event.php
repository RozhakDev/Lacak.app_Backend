<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasSchoolScope;

class Event extends Model
{
    use HasFactory, SoftDeletes, HasSchoolScope;

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->created_by)) {
                $model->created_by = auth()->id() ?? 1;
            }
            if (empty($model->slug)) {
                $model->slug = \Illuminate\Support\Str::slug($model->title) . '-' . uniqid();
            }
        });
    }

    protected $fillable = [
        'school_id',
        'created_by',
        'title',
        'slug',
        'description',
        'event_type',
        'location_type',
        'location_details',
        'start_date',
        'end_date',
        'banner_url',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
