<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    protected $table = 'experiences';
    protected $fillable = [
        'user_id',
        'company_name',
        'position',
        'start_date',
        'end_date',
        'description',
    ];

    // Relación con User (inversa, uno a muchos)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
