<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    // Mostrar el formulario de completar perfil
    public function show()
    {
        // Obtener los usuarios de Google y GitHub desde la sesión
        $googleUser = Session::get('google_user');
        $githubUser = Session::get('github_user');
        $microsoftUser = Session::get('microsoft_user');
       
        // Determinar qué fuente de datos usar
        $user = $googleUser ?? $githubUser ?? $microsoftUser;

    
        // Si se encuentra un usuario (ya sea de Google o GitHub)
        if ($user) {
            return inertia('CompleteProfile', [
                'email' => $user['email'],
            ]);
        }
    
        // Si no se encontró el usuario, redirigir al inicio
        return redirect('/');
    }
    

    // Guardar los datos del perfil
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'training_area' => 'required|in:Informatica,Marketing,Automocion',
        ]);

        // Obtener los datos de la sesión de Google o GitHub
        $githubUser = Session::get('github_user');
        $googleUser = Session::get('google_user');
        $microsoftUser = Session::get('microsoft_user');

        if ($githubUser) {
            $user = User::create([
                'email' => $githubUser['email'],
                'github_id' => $githubUser['github_id'],
                'google_id' => null,
                'password' => $githubUser['password'],
                'role' => $githubUser['role'],
                'name' => $request->input('name'),
                'last_name_1' => $request->input('last_name_1'),
                'last_name_2' => $request->input('last_name_2'),
                'training_area' => $request->input('training_area'),
            ]);
        } elseif ($googleUser) {
            $user = User::create([
                'email' => $googleUser['email'],
                'google_id' => $googleUser['google_id'],
                'github_id' => null,
                'password' => $googleUser['password'],
                'role' => $googleUser['role'],
                'name' => $request->input('name'),
                'last_name_1' => $request->input('last_name_1'),
                'last_name_2' => $request->input('last_name_2'),
                'training_area' => $request->input('training_area'),
            ]);
        }
        elseif($microsoftUser) {
            $user = User::create([
                'email' => $microsoftUser['email'],
                'microsoft_id' => $microsoftUser['microsoft_id'],
                'password' => $microsoftUser['password'],
                'role' => $microsoftUser['role'],
                'name' => $request->input('name'),
                'last_name_1' => $request->input('last_name_1'),
                'last_name_2' => $request->input('last_name_2'),
                'training_area' => $request->input('training_area'),
            ]);

        }

        // Limpiar los datos de la sesión
        Session::forget('github_user');
        Session::forget('google_user');
        Session::forget('microsoft_user');

        return redirect('/')->with('success', 'Perfil completado con éxito. ¡Por favor, inicia sesión!');
    }
}
