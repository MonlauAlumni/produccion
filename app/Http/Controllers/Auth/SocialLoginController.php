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
    public function redirectToMicrosoft(){
        return Socialite::driver('microsoft')->redirect();

    }
      public function redirectToGoogle()
      {
          return Socialite::driver('google')->redirect();
      }

      public function handleMicrosoftCallback(){
        $microsoftUser = Socialite::driver('microsoft')->user();
        $accessToken = $microsoftUser->token;

        $client = new \GuzzleHttp\Client();
        $response = $client->get('https://graph.microsoft.com/v1.0/me/memberOf', [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Accept' => 'application/json'
            ],
        ]);
        $groups = json_decode($response->getBody(), true);
        $groupNames = [];

        foreach ($groups['value'] as $group){
            if(isset($group['displayName'])) {
                $groupNames[] = $group['displayName'];
            }

        }
        //dd($groupNames);
        //dd($microsoftUser);
        $user = User::where('microsoft_id', $microsoftUser->getId())->first();
        if (!$user) {
            Session::put('microsoft_user', [
                'email' => $microsoftUser->getEmail(),
                'microsoft_id' => $microsoftUser->getId(),
                'password' => bcrypt(Str::random(16)),
                'role' => 'student',
            ]);
        }

        else {
            Auth::login($user);
            return redirect('/home');
        }
        
       
        return redirect('/complete-profile');
      }
     
     
      public function handleGoogleCallback()
      {
          try {
              $googleUser = Socialite::driver('google')->user();
      
              if (!$googleUser->getEmail()) {
                  throw new \Exception('No se pudo obtener el correo electrónico del usuario.');
              }
      
              
              $user = User::where('email', $googleUser->getEmail())->first();
              
              if (!$user) {
                  
                  Session::put('google_user', [
                      'email' => $googleUser->getEmail(),
                      'google_id' => $googleUser->getId(),
                      'password' => bcrypt(Str::random(16)),
                      
                  ]);
              }
      
             
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
    
    $githubUser = Socialite::driver('github')->stateless()->user();
    
  
   
    $user = User::where('github_id', $githubUser->getId())->first();

  
    if (!$user) {
       
        Session::put('github_user', [
            'email' => $githubUser->getEmail(),
            'github_id' => $githubUser->getId(),
            'password' => bcrypt(Str::random(16)),
          
        ]);
    }

    else {
        Auth::login($user);
        return redirect('/home');
    }

    return redirect('/complete-profile');
}

}
