<?php
namespace App\Observers;

use App\Models\Profile;

class ProfileObserver
{
    public function created(Profile $profile)
{


    if ($profile->user->hasRole('alumne')) {
        $profile->slang = strtolower(
            str_replace(' ', '-', trim(
                "{$profile->user->name}-{$profile->user->last_name_1}" .
                ($profile->user->last_name_2 ? "-{$profile->user->last_name_2}" : "")
            ))
        );
    }
}


}
