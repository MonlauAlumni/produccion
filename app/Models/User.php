<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 
        'first_name',
        'last_name_1',
        'last_name_2',
        'email',
        'password',
        'role',
        'training_area',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
