<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'event_date',
        'organizer_id',
        'group_id',
        'is_private',
        'slug',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'is_private' => 'boolean',
    ];

    /**
     * Get the organizer of the event.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Get the group associated with the event.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the photos for the event.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(EventPhoto::class);
    }

    /**
     * Get the attendees for the event.
     */
    public function attendees(): HasMany
    {
        return $this->hasMany(EventAttendee::class);
    }

    /**
     * Check if a user is attending the event.
     */
    public function isAttendingBy(User $user): bool
    {
        return $this->attendees()->where('user_id', $user->id)->exists();
    }
}

