<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'group_id',
        'content',
        'likes_count',
        'comments_count',
    ];

    protected $appends = ['is_liked'];

    /**
     * Get the user who created the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the group this post belongs to (if any).
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the comments for this post.
     */
    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    /**
     * Get the images for this post.
     */
    public function images()
    {
        return $this->hasMany(PostImage::class)->orderBy('order');
    }

    /**
     * Get the likes for this post.
     */
    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    /**
     * Check if a user has liked this post.
     */
    public function isLikedBy(User $user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the first image of the post (if any).
     */
    public function getFirstImageAttribute()
    {
        return $this->images()->orderBy('order')->first();
    }

    public function getIsLikedAttribute()
    {
        if (!auth()->check()) return false;
        return $this->isLikedBy(auth()->user());
    }

    /**
     * Scope a query to only include group posts.
     */
    public function scopeInGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    /**
     * Scope a query to only include non-group posts.
     */
    public function scopeNotInGroup($query)
    {
        return $query->whereNull('group_id');
    }
}