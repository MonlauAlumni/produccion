<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 
        'title', 
        'description', 
        'requirements',
        'experience',
        'responsibilities',
        'benefits',
        'min_experience',
        'min_studies',
        'vacancies',
        'minimum_salary', 
        'maximum_salary', 
        'location', 
        'work_mode', 
        'category', 
        'expires_at',
        'deadline', 
        'status',
        'file'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'deadline' => 'datetime',
        'responsibilities' => 'array',
        'benefits' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function applicants()
    {
        return $this->belongsToMany(User::class, 'job_offer_applications');
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_job_offers');
    }
}