<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;


class Company extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'slang',
        'company_phone',
        'fiscal_id',
        'description',
        'profile_picture',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
