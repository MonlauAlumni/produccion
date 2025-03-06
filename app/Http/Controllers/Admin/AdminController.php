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
                    ->when($request->filled('role'), function ($query) use ($request) {
                        $query->whereHas('roles', function ($q) use ($request) {
                            $q->where('role_id', $request->role);
                        });
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
                return Inertia::render('Admin/AdminCompanies', [
                    'companies' => Company::all()
                ]);

                
            default:
                abort(404);
        }
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
        ]);

        $user->update($validated);

        return response()->json(['message' => 'User updated successfully']);
    }
}
