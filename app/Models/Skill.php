<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Profile;
use App\Models\JobOffer;
use App\Models\Company;

class Skill extends Model
{
    use HasFactory;

    // Definir los campos que pueden ser llenados
    protected $fillable = [
        'name',  // El nombre de la habilidad
    ];

    // Relación con los perfiles
    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'profile_skills');
    }

    // Relación con las ofertas de trabajo
    public function jobOffers()
    {
        return $this->belongsToMany(JobOffer::class, 'job_offer_skills');
    }

    // Relación con las empresas
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_skills');
    }
}
