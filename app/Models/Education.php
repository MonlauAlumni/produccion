<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'educations';
    protected $fillable = [
        'user_id',
        'degree',
        'institution',
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
