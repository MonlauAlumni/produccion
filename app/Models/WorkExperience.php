<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    protected $table = 'experiences';
    protected $fillable = [
        'user_id',
        'job_title',
        'company_name',
        'start_date',
        'end_date',
        'responsibilities',
    ];

    // Relación con User (inversa, uno a muchos)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
