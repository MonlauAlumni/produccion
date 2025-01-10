<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    // Define el nombre de la tabla si no sigue la convención de Laravel (que sería 'users' por defecto)
    protected $table = 'users';

    // Indica qué campos son asignables en masa (Mass Assignment)
    protected $fillable = [
        'name',
        'last_name_1',
        'last_name_2',
        'email',
        'password',
        'role',
        'training_area',
        'profile_picture'
    ];

    // Campos que deberían ser ocultados en las respuestas JSON (por ejemplo, la contraseña)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Campos que deberían ser mutados a un tipo específico
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Función para manejar la relación con las sesiones (si es necesario)
   

    // Puedes agregar más relaciones o métodos específicos que necesites
}
