<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    // Aquí indicamos la tabla correcta
    protected $table = 'job_applications';

    protected $fillable = [
        'status',
        'job_offer_id',
        'user_id',
        'cover_letter',
        'created_at',
        'updated_at',
    ];
}
