<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class RegisterCompanyController extends Controller
{

    public function create()
    {
        return Inertia::render('Auth/RegisterCompany');
    }

    public function store(Request $request)
    {
      //  dd($request->all());
        
        $validatedData = $request->validate([
            // Datos del Alumno
            'name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',

            // Datos de la Empresa
            'company_name' => 'required|string|max:255',
           'company_phone' => 'required|regex:/^[0-9]{7,15}$/',
            'fiscal_id' => 'required|string|max:255|unique:companies,fiscal_id',
            'description' => 'nullable|string',
        ]);

        // //Registro del Usuario (Usuario)
        try {
        $user = User::create([
            'name' => $request->name,
            'last_name_1' => $request->last_name_1,
            'last_name_2' => $request->last_name_2,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'training_area' => 'Informatica',
        ]);
        $user->assignRole('empresa'); 
        
        $user->settings()->create();
        // Registro de la Empresa
        $company = Company::create([
            'user_id' => $user->id,
            'company_name' => $request->company_name,
            'company_phone' => $request->company_phone,
            'fiscal_id' => $request->fiscal_id,
            'description' => $request->description,
        ]);


        Auth::login($user);


        return redirect('/home')->with('success', 'Perfil completado con éxito. ¡Bienvenido!');

    } catch (\Exception $e) {
        dd($e);
        return back()->with('error', 'Ocurrió un error, intenta nuevamente.');
    }

  

    }
}
