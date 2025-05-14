<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profiles';
    protected $fillable = [
        'user_id',
        'description',
        'profile_picture',
        'cv_path',
        'phone',
        'availability',
        'degree',
        'job_title',
        'graduation_year',
        'location',
        'linkedin',
        'github',
        'website',
        'slang',
        'banner_url',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSlangAttribute()
    {
        if (isset($this->attributes['slang']) && $this->attributes['slang'] != '') {
            return $this->attributes['slang'];
        }

        return strtolower(
            str_replace(' ', '-', trim("{$this->name}-{$this->last_name_1}" . ($this->last_name_2 ? "-{$this->last_name_2}" : "")))
        );
    }

    protected static function booted()
    {
        static::creating(function ($profile) {
            $profile->slang = $profile->getSlangAttribute();
            $user = $profile->user()->first();
            $profile->profile_picture = $user ? $user->profile_picture : null;
        });
    }

   
}
