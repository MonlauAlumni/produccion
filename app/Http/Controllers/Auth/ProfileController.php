<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Inertia\Inertia;
use App\Models\User;

use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{

    public function profile($slang)
{
    $user = User::all()->first(function ($user) use ($slang) {
        return $user->slang === $slang;
    });

    if (!$user) {
        abort(404);
    }
    $authUser = Auth::user();
   
    $isSameUser = $authUser && $authUser->id === $user->id;

    return Inertia::render('Auth/Profile', [
        'user' => $user,
        'profile' => $user->profile,
        'workExperiences' => $user->workExperiences,
        'educations' => $user->educations,
        'isSameUser' => $isSameUser,
      
        
    ]);
}

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
        } elseif ($microsoftUser) {
            $user = User::create([
                'email' => $microsoftUser['email'],
                'microsoft_id' => $microsoftUser['microsoft_id'],
                'password' => $microsoftUser['password'],
                'name' => $request->input('name'),
                'last_name_1' => $request->input('last_name_1'),
                'last_name_2' => $request->input('last_name_2'),
                'training_area' => $request->input('training_area'),
            ]);
        }
        $user->assignRole('alumne');

        // Limpiar los datos de la sesión
        Session::forget('github_user');
        Session::forget('google_user');
        Session::forget('microsoft_user');

        return redirect('/')->with('success', 'Perfil completado con éxito. ¡Por favor, inicia sesión!');
    }



    public function update(Request $request)
    {
  
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
    
       
        $user = auth()->user(); 
     
  
        // Actualizar el perfil
        $user->update([
            'name' => $request->input('name'),
            'last_name_1' => $request->input('last_name_1'),
            'last_name_2' => $request->input('last_name_2'),
        ]);
        
        $user->profile->update([
            'description' => $request->input('description'),
        ]);
        
      
        
    }
}
