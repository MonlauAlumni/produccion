<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Session;

class SocialLoginController extends Controller
{
    
      public function redirectToGoogle()
      {
          return Socialite::driver('google')->redirect();
      }
  
      // Respuesta de Google
      public function handleGoogleCallback()
      {
          try {
              // Obtener los datos del usuario de Google
              $googleUser = Socialite::driver('google')->user();
      
              if (!$googleUser->getEmail()) {
                  throw new \Exception('No se pudo obtener el correo electrónico del usuario.');
              }
      
              // Buscar si el usuario ya existe
              $user = User::where('email', $googleUser->getEmail())->first();
             
              if (!$user) {
                  // Si el usuario no existe, guardamos los datos en la sesión
                  Session::put('google_user', [
                      'email' => $googleUser->getEmail(),
                      'google_id' => $googleUser->getId(),
                      'password' => bcrypt(Str::random(16)),
                      'role' => 'student',
                  ]);
              }
      
              // Redirigir al formulario de completar perfil
              return redirect('/complete-profile');
          } catch (\Exception $e) {
              return redirect('/login')->with('error', 'Error al iniciar sesión con Google: ' . $e->getMessage());
          }
      }
    
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

  
    if (!$user) {
       
        Session::put('github_user', [
            'email' => $githubUser->getEmail(),
            'github_id' => $githubUser->getId(),
            'password' => bcrypt(Str::random(16)),
            'role' => 'student',
        ]);
    }
    return redirect('/complete-profile');
}

}
