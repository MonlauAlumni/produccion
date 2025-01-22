<?php

namespace App\Models;

<<<<<<< HEAD
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
=======
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Company extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'company_phone',
        'fiscal_id',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
>>>>>>> 586437ca753fd1ebff9daa4af9bcb38cf21ae63b
