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

    public function mutual_connections()
    {
        $myConnectionIds = auth()->user()->connections()
            ->where('status', 'accepted')
            ->pluck('connection_id')
            ->merge(
                auth()->user()->connectedUsers()
                    ->where('status', 'accepted')
                    ->pluck('user_id')
            )
            ->unique();

        return $this->connections()
            ->where('status', 'accepted')
            ->whereIn('connection_id', $myConnectionIds);
    }

    public function connections()
    {
        return $this->belongsToMany(User::class, 'connections', 'user_id', 'connection_id')
            ->withPivot('status');
    }

    public function connectedUsers()
    {
        return $this->hasMany(Connection::class, 'connection_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }



}
