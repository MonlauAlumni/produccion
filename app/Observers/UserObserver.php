<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Profile;

class UserObserver
{
    public function created(User $user)
    {
        if($user->hasRole('alumne')){
        Profile::create([
            'user_id' => $user->id,
        ]);
    }
    }
}

