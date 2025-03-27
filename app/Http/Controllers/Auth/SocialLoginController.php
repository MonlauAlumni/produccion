<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class SocialLoginController extends Controller
{
    public function redirectToMicrosoft()
    {
        return Socialite::driver('microsoft')->redirect();
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleMicrosoftCallback(Request $request)
    { // Agregar Request como parámetro
        $microsoftUser = Socialite::driver('microsoft')->user();
        // dd($microsoftUser);
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

        foreach ($groups['value'] as $group) {
            if (isset($group['displayName'])) {
                $groupNames[] = $group['displayName'];
            }
        }

        $user = User::where('microsoft_id', $microsoftUser->getId())->first();


        // Buscar si el usuario ya exist

        if (!$user) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'image/jpeg'
            ])->get('https://graph.microsoft.com/v1.0/me/photo/$value');

            if ($response->successful()) {
                $profileImage = 'data:image/jpeg;base64,' . base64_encode($response->body());
                $base64Str = substr($profileImage, strpos($profileImage, ",") + 1);

                $imageData = base64_decode($base64Str);
                $filename = '/profile_picture/' . uniqid() . '.jpg';
                Storage::disk('public')->put($filename, $imageData);
            }

            Session::put('microsoft_user', [
                'email' => $microsoftUser->getEmail(),
                'microsoft_id' => $microsoftUser->getId(),
                'password' => bcrypt(Str::random(16)),
                'role' => 'student',
                'profile_photo_path' => isset($filename) ? '/storage'.$filename : null,
            ]);
        
            return redirect('/complete-profile');
        } else {
            if (!$user->settings()->exists()) {
                $user->settings()->create();
            }
            // Si el usuario existe, comprobamos si tiene 2FA habilitado
            if ($user->two_factor_secret) {
                $request->session()->put('login.id', $user->id);
                $request->session()->save();
                return redirect()->route('two-factor.login');
            }


            // Si no tiene 2FA, iniciamos sesión
            Auth::login($user);
            return redirect('/home');
        }
    }

    public function handleGoogleCallback(Request $request) 
    {
        
        try {
            $googleUser = Socialite::driver('google')->user();

            if (!$googleUser->getEmail()) {
                throw new \Exception('No se pudo obtener el correo electrónico del usuario.');
            }

            // Buscar si el usuario ya existe
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Si el usuario no existe, lo guardamos temporalmente
                Session::put('google_user', [
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(Str::random(16)),
                ]);
                return redirect('/complete-profile');
            } else {
                if (!$user->settings()->exists()) {
                    $user->settings()->create();
                }
                // Si el usuario existe, comprobamos si tiene 2FA habilitado
                if ($user->two_factor_secret) {
                    $request->session()->put('login.id', $user->id);
                    $request->session()->save();
                    return redirect()->route('two-factor.login');
                }

                // Si no tiene 2FA, iniciamos sesión
                Auth::login($user);
                return redirect('/home');
            }
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Error al iniciar sesión con Google: ' . $e->getMessage());
        }
    }

    public function github_redirect()
    {
        return Socialite::driver('github')->redirect();
    }

    public function github_callback(Request $request) // Agregar Request como parámetro
    {
        $githubUser = Socialite::driver('github')->stateless()->user();

        // Buscar si el usuario ya existe
        $user = User::where('github_id', $githubUser->getId())->first();

        if (!$user) {
            // Si el usuario no existe, lo guardamos temporalmente
            Session::put('github_user', [
                'email' => $githubUser->getEmail(),
                'github_id' => $githubUser->getId(),
                'password' => bcrypt(Str::random(16)),
            ]);
            return redirect('/complete-profile');
        } else {
            if (!$user->settings()->exists()) {
                $user->settings()->create();
            }
            // Si el usuario existe, comprobamos si tiene 2FA habilitado
            if ($user->two_factor_secret) {
                $request->session()->put('login.id', $user->id);
                $request->session()->save();
                return redirect()->route('two-factor.login');
            }

            // Si no tiene 2FA, iniciamos sesión
            Auth::login($user);
            return redirect('/home');
        }
    }
}

