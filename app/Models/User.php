<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Profile;
use App\Models\Educations;   
use App\Models\WorkExperience;
use App\Models\Skill;
use Laravel\Fortify\TwoFactorAuthenticatable;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable;

    protected $fillable = [
        'name', 
        'last_name_1',
        'last_name_2',
        'email',
        'password',
        'training_area',
        'google_id',
        'github_id',
        'microsoft_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function educations()
    {
        return $this->hasMany(Education::class);
    }

    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class);
    }

    public function getSlangAttribute()
    {
        return strtolower(
            str_replace(' ', '-', trim("{$this->name}-{$this->last_name_1}" . ($this->last_name_2 ? "-{$this->last_name_2}" : "")))
        );
    }
  
 }
