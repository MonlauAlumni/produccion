<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'privacy',
        'slug',
        'group_logo',
        'group_banner',
        'creator_id',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function members()
    {
        return $this->hasMany(GroupUser::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class)->orderBy('created_at', 'desc');
    }

    public function events()
    {
        return $this->hasMany(GroupEvent::class);
    }

    public function files()
    {
        return $this->hasMany(GroupFile::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
    
}