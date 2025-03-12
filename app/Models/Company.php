<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\JobOffer;


class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'slang',
        'company_phone',
        'fiscal_id',
        'description',
        'profile_picture',
        'sector',
        'size',
        'location',
        'linkedin',
        'twitter',
        'facebook',
        'instagram',
        'website',
        'founded_year',
        'email',
        'banner_url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobOffers()
    {
        return $this->hasMany(JobOffer::class);
    }


 protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            $baseSlug = Str::slug($company->company_name);
            $slug = $baseSlug;
            $count = 1;

            while (Company::where('slang', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }

            $company->slang = $slug;
        });
    }
}
