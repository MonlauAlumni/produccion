<?php
 
namespace App\Http\Controllers\Auth;
 
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
 
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
 
class RegisterController extends Controller
{
    
    public function create()
    {
        return Inertia::render('Auth/Register');
    }
 
   
    public function store(Request $request)
    {

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'training_area' => 'required|in:Informatica,Marketing,Automocion',
        ]);
     
        $user = User::create([
            'name' => $request->first_name,
            'last_name_1' => $request->last_name_1,
            'last_name_2' => $request->last_name_2,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'training_area' => $request->training_area,
        ]);

        $user->settings()->create();
        $user->profile()->create();

     

        Auth::login($user);
        $user->assignRole('alumne');
     
        return redirect('/home')->with('success', 'Perfil completado con éxito. ¡Por favor, inicia sesión!');
    }
}