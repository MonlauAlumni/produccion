<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
   
    public function show()
    {
   
        $googleUser = Session::get('google_user');
        $githubUser = Session::get('github_user');
        $microsoftUser = Session::get('microsoft_user');
       
      
        $user = $googleUser ?? $githubUser ?? $microsoftUser;

    
      
        if ($user) {
            return inertia('Auth/CompleteProfile', [
                'email' => $user['email'],
            ]);
        }
        
    
        
    
    }
    

   
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'training_area' => 'required|in:Informatica,Marketing,Automocion',
        ]);

      
        $githubUser = Session::get('github_user');
        $googleUser = Session::get('google_user');
        $microsoftUser = Session::get('microsoft_user');

        if ($githubUser) {
            $user = User::create([
                'email' => $githubUser['email'],
                'github_id' => $githubUser['github_id'],
                'google_id' => null,
                'password' => $githubUser['password'],
           
                'name' => $request->input('name'),
                'last_name_1' => $request->input('last_name_1'),
                'last_name_2' => $request->input('last_name_2'),
                'training_area' => $request->input('training_area'),
            ]);
            $user = User::where('email', $githubUser['email'])->first();

        } elseif ($googleUser) {
            $user = User::create([
                'email' => $googleUser['email'],
                'google_id' => $googleUser['google_id'],
                'password' => $googleUser['password'],
                'name' => $request->input('name'),
                'last_name_1' => $request->input('last_name_1'),
                'last_name_2' => $request->input('last_name_2'),
                'training_area' => $request->input('training_area'),
            ]);
            $user = User::where('email', $googleUser['email'])->first();

        }
        elseif($microsoftUser) {
            $user = User::create([
                'email' => $microsoftUser['email'],
                'microsoft_id' => $microsoftUser['microsoft_id'],
                'password' => $microsoftUser['password'],
                'name' => $request->input('name'),
                'last_name_1' => $request->input('last_name_1'),
                'last_name_2' => $request->input('last_name_2'),
                'training_area' => $request->input('training_area'),
            ]);

            $user = User::where('email', $microsoftUser['email'])->first();

        }
        Auth::login($user);
        $user->assignRole('alumne');
        $user->settings()->create();
        $request->session()->regenerate();
        
        // Limpiar los datos de la sesión
        Session::forget('github_user');
        Session::forget('google_user');
        Session::forget('microsoft_user');


        return redirect('/home')->with('success', 'Perfil completado con éxito. ¡Por favor, inicia sesión!');
    }
}
