<?php
 
namespace App\Http\Controllers\Auth;
 
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
 
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
 
class RegisterController extends Controller
{
 
 
 
    public function create()
    {
        return Inertia::render('Register');
    }
 
    // Manejar el envío del formulario de registro
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,teacher,tutor,student,company,guest',
            'training_area' => 'required|in:Informatica,Marketing,Automocion',
        ]);
       
        $user = new User([
            'name' => $request->first_name,
            'last_name_1' => $request->last_name_1,
            'last_name_2' => $request->last_name_2,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'training_area' => $request->training_area,
        ]);
 
 
        $user->save();
 
 
        return redirect()->route('/')->with('success', 'User registered successfully!');
    }
}