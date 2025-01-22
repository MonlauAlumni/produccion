<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class CompanyRegisterController extends Controller
{
    public function store(Request $request)
    {
        // Validación de datos
        $request->validate([
         
            'name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',

            // Datos de la Empresa
            'companyName' => 'required|string|max:255',
            'companyPhone' => 'required|string|max:15',
            'fiscalId' => 'required|string|max:255|unique:companies,fiscal_id',
            'description' => 'nullable|string',
        ]);

        // Registro del Alumno (Usuario)
        $user = User::create([
            'name' => $request->name,
            'last_name_1' => $request->last_name_1,
            'last_name_2' => $request->last_name_2,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'training_area' => 'Informática',
        ]);

        // Registro de la Empresa
        $company = Company::create([
            'user_id' => $user->id, 
            'name' => $request->companyName,
            'phone' => $request->companyPhone,
            'fiscal_id' => $request->fiscalId,
            'description' => $request->description,
        ]);

       
        return response()->json([
            'message' => 'Registro exitoso',
            'user' => $user,
            'company' => $company
        ]);
    }
}
