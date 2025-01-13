<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    
    public function show()
    {
        $githubUser = Session::get('github_user');

        if (!$githubUser) {
            return redirect('/login');
        }

        return inertia('CompleteProfile', [
            'email' => $githubUser['email'],
        ]);
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

        if (!$githubUser) {
            return redirect('/login');
        }

        // Crear el usuario en la base de datos
        $user = User::create([
            'email' => $githubUser['email'],
            'github_id' => $githubUser['github_id'],
            'password' => $githubUser['password'],
            'role' => $githubUser['role'],
            'name' => $request->input('name'),
            'last_name_1' => $request->input('last_name_1'),
            'last_name_2' => $request->input('last_name_2'),
            'training_area' => $request->input('training_area'),
        ]);

   
        Session::forget('github_user');

        return redirect('/')->with('success', 'Profile completed successfully! Please log in.');
    }
    
}
