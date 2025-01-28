<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Profile;
use App\Models\Education;   
use App\Models\WorkExperience;
use App\Models\Skill;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

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

    // Relación con Education (uno a muchos)
    public function educations()
    {
        return $this->hasMany(Education::class);
    }

    // Relación con WorkExperience (uno a muchos)
    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class);
    }

  
}
