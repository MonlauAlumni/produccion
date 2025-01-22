<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class Company extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    protected $table = 'companies';
    protected $fillable = [
        'name', 
        'website',
        'description',
        'phone',
        'fiscal_id',
    ];

   
}
