<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'photo_path',
        'order',
    ];

    /**
     * Get the event that owns the photo.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}

