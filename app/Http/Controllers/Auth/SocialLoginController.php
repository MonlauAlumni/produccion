<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * Redirigir al usuario a GitHub para autorizar la aplicación.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function github_redirect()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Manejar la respuesta de GitHub después de que el usuario autoriza la aplicación.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function github_callback()
{
    $githubUser = Socialite::driver('github')->user();
  
   
    $user = User::where('github_id', $githubUser->getId())->first();

    // Si el usuario no existe, creamos uno nuevo
    if (!$user) {
       
      
        $user = User::create([
            'name' => $githubUser->getName() ?? 'No name',  // Asignar nombre si está disponible, sino 'No name'
            'last_name_1' => $githubUser->getNickname() ?? 'Apellido1',  // Usar nickname o un valor predeterminado
            'email' => $githubUser->getEmail(),
            'github_id' => $githubUser->getId(),
            'password' => bcrypt(Str::random(16)),
            'role' => 'student',
            'training_area' => 'Informatica',
        ]);
    }

    // Iniciar sesión automáticamente con el usuario recién creado o existente
    Auth::login($user);

    // Redirigir al usuario a la página principal u otra que desees
    return redirect()->route('home')->with('success', 'Registro exitoso!');
}

}
