<?php
namespace App\Observers;

use App\Models\Profile;

class ProfileObserver
{
    public function created(Profile $profile)
{

   
    // Generar el slug a partir del nombre y apellidos del usuario
    // y asignarlo al campo 'slang' del perfil
        $profile->slang = strtolower(
            str_replace(' ', '-', trim(
                "{$profile->user->name}-{$profile->user->last_name_1}" .
                ($profile->user->last_name_2 ? "-{$profile->user->last_name_2}" : "")
            ))
        );
        $profile->save();
    
}


}
