<?php
namespace App\Observers;

use App\Models\Profile;

class ProfileObserver
{
    public function creating(Profile $profile)
    {
      
        $user = $profile->user;

        // Genera el slang
        $profile->slang = strtolower(
            str_replace(' ', '-', trim("{$user->name}-{$user->last_name_1}" . ($user->last_name_2 ? "-{$user->last_name_2}" : "")))
        );
    }
}
