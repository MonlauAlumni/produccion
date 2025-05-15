<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Company;
class AdminController extends Controller
{
    public function show(Request $request, $page)
    {
        switch ($page) {
            case 'dashboard':
                return Inertia::render('Admin/AdminHome', [
                    'user' => auth()->user()
                ]);

            case 'users':
                $query = User::select(['id', 'name', 'last_name_1', 'last_name_2','email', 'training_area'])
                    ->when($request->filled('id'), function ($query) use ($request) {
                        $query->where('id', $request->id);
                    })
                    ->when($request->filled('name'), function ($query) use ($request) {
                        $name = strtolower($request->name);
                        $query->whereRaw('LOWER(CONCAT(name, " ", last_name_1, " ", last_name_2)) like ?', ["%{$name}%"]);
                    })
                    ->when($request->filled('email'), function ($query) use ($request) {
                        $query->where('email', 'like', '%' . $request->email . '%');
                    })
                    ->when($request->filled('area'), function ($query) use ($request) {
                        $query->where('training_area', 'like', '%' . $request->area . '%');
                    })

                    
                    ->with('roles') 
                    ->orderBy('id', 'desc')
                    ->paginate($request->pagination ?? 10);

                $users = $query;

                $totalUsers = $users->total();  

                return Inertia::render('Admin/AdminUsers', [
                    'users' => $users->items(),
                    'pagination' => $users,
                    'totalUsers' => $totalUsers,
                    'filters' => $request->all(),
                ]);


                case 'companies':
                    $query = Company::select([
                            'id', 
                            'company_name', 
                            'company_phone', 
                            'fiscal_id', 
                            'user_id',
                            'address',        // Añadido
                            'zip_code',       // Añadido
                            'population'      // Añadido
                        ])
                        ->with('user:id,email,name,last_name_1,last_name_2')
                        ->when($request->filled('id'), function ($query) use ($request) {
                            $query->where('id', $request->id);
                        })
                        ->when($request->filled('company_name'), function ($query) use ($request) {
                            $company_name = strtolower($request->company_name);
                            $query->whereRaw('LOWER(company_name) like ?', ["%{$company_name}%"]);
                        })
                        ->when($request->filled('company_phone'), function ($query) use ($request) {
                            $query->where('company_phone', 'like', '%' . $request->company_phone . '%');
                        })
                        ->when($request->filled('fiscal_id'), function ($query) use ($request) {
                            $query->where('fiscal_id', 'like', '%' . $request->fiscal_id . '%');
                        })
                        ->when($request->filled('address'), function ($query) use ($request) {
                            $query->where('address', 'like', '%' . $request->address . '%');
                        })
                        ->when($request->filled('zip_code'), function ($query) use ($request) {
                            $query->where('zip_code', 'like', '%' . $request->zip_code . '%');
                        })
                        ->when($request->filled('population'), function ($query) use ($request) {
                            $query->where('population', 'like', '%' . $request->population . '%');
                        })
                        ->orderBy('id', 'desc')
                        ->paginate($request->pagination ?? 10);
                
                    $companies = $query;
                
                    return Inertia::render('Admin/AdminCompanies', [
                        'companies' => $companies->items(),
                        'pagination' => $companies,
                        'totalCompanies' => $companies->total(),
                        'filters' => $request->all(),                
                    ]);
                

                
                
            default:
                abort(404);
        }
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'Usuario eliminado exitosamente.');
    }

    public function singleUser($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return Inertia::render('Admin/AdminSingleUser', [
            'user' => $user
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'email' => "required|email|unique:users,email,{$id}",
            'training_area' => 'required|string|max:255',
        ], 
        );

        $user->update($validated);
        return redirect()->back()->with('success', 'Usuario actualizado exitosamente.');
    }

    
    public function singleCompany($id)
    {
        $company = Company::findOrFail($id);
        return Inertia::render('Admin/AdminSingleCompany', [
            'company' => $company
        ]);
    }

    public function updateCompany(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_phone' => 'required|string|max:255',
            'fiscal_id' => 'required|string|max:255',
            'address' => 'required|string|max:255', // Añadido
            'zip_code' => 'required|string|max:10', // Añadido
            'population' => 'required|string|max:255', // Añadido

        ], 
        );

        $company->update($validated);
        return redirect()->back()->with('success', 'Empresa actualizada exitosamente.');
    }
}
