<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\JobOffer;
use App\Models\Profile;


class Skill extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'category'];

    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'profile_skill', 'skill_id', 'profile_id');
    }
    

    public function jobOffers(): BelongsToMany
    {
        return $this->belongsToMany(JobOffer::class);
    }
}
