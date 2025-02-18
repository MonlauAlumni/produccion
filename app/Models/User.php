<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

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
}