<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_offer_id',
        'user_id',
        'cover_letter',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relación con JobOffer (una aplicación pertenece a una oferta de trabajo)
    public function jobOffer()
    {
        return $this->belongsTo(JobOffer::class);
    }

    // Relación con Student (una aplicación pertenece a un alumno)
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
