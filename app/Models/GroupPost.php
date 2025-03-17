<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id',
        'user_id',
        'content',
        'image',
        'likes_count',
        'comments_count',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(GroupPostComment::class);
    }
}
