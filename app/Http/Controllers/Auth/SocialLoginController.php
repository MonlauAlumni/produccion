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
