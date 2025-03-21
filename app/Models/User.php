<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Profile;
use App\Models\Company;
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
        'profile_picture',
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

    public function savedJobOffers()
    {
        return $this->belongsToMany(JobOffer::class, 'saved_job_offers');
    }

    public function appliedJobOffers()
    {
        return $this->belongsToMany(JobOffer::class, 'job_offer_applications');
    }

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    public function mutual_connetios(User $user)
    {
        return $this->connections()->whereHas('connections', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

   
  
 }
