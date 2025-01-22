<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Company extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'company_phone',
        'fiscal_id',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}