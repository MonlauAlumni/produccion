<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Application;
use App\Models\Company;


class AdminController extends Controller
{
    public function show(Request $request, $page)
    {
        switch ($page) {
            case 'dashboard':
                $totalUsers = User::count();
                $totalCompanies = Company::count();
                $totalApplications = Application::where('status', 'open')->count();
                $newUsersThisMonth = User::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count();
                $usersByArea = User::selectRaw('training_area, COUNT(*) as total')
                    ->groupBy('training_area')
                    ->pluck('total', 'training_area');
                $companiesBySector = Company::selectRaw('sector, COUNT(*) as total')
                    ->groupBy('sector')
                    ->pluck('total', 'sector');
                return Inertia::render('Admin/AdminHome', [
                    'totalUsers' => $totalUsers,
                    'totalCompanies' => $totalCompanies,
                    'totalApplications' => $totalApplications,
                    'newUsersThisMonth' => $newUsersThisMonth,
                    'usersByArea' => $usersByArea,
                    'companiesBySector' => $companiesBySector,
                ]);
                break;

            case 'home':
                return Inertia::render('Admin/AdminHome', [
                    'usersByArea' => User::selectRaw('training_area, COUNT(*) as total')
                        ->groupBy('training_area')
                        ->pluck('total', 'training_area'),
                ]);
                break;

            case 'users':
                $usersByArea = User::selectRaw('training_area, COUNT(*) as total')
                    ->groupBy('training_area')
                    ->pluck('total', 'training_area');
                $query = User::select(['id', 'name', 'last_name_1', 'last_name_2', 'email', 'training_area', 'status'])
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
                    ->whereHas('roles', function ($q) {
                        $q->whereIn('model_has_roles.role_id', [2, 3]);
                    })

                    ->orderBy('id', 'desc')
                    ->paginate($request->pagination ?? 10);

                $users = $query;

                return Inertia::render('Admin/AdminUsers', [
                    'users' => $users->items(),
                    'pagination' => $users,
                    'totalUsers' => $users->total(),
                    'filters' => $request->all(),
                    'usersByArea' => $usersByArea,
                ]);
                break;

            case 'companies':
                $totalOpenApplications = Application::where('status', 'open')->count();

                $query = Company::select([
                    'id',
                    'company_name',
                    'company_phone',
                    'fiscal_id',
                    'user_id',
                    'address',
                    'zip_code',
                    'population'
                ])
                    ->with('user:id,email,name,last_name_1,last_name_2')
                    ->when($request->filled('id'), fn($query) => $query->where('id', $request->id))
                    ->when($request->filled('company_name'), function ($query) use ($request) {
                        $company_name = strtolower($request->company_name);
                        $query->whereRaw('LOWER(company_name) like ?', ["%{$company_name}%"]);
                    })
                    ->when($request->filled('company_phone'), fn($query) => $query->where('company_phone', 'like', '%' . $request->company_phone . '%'))
                    ->when($request->filled('fiscal_id'), fn($query) => $query->where('fiscal_id', 'like', '%' . $request->fiscal_id . '%'))
                    ->when($request->filled('address'), fn($query) => $query->where('address', 'like', '%' . $request->address . '%'))
                    ->when($request->filled('zip_code'), fn($query) => $query->where('zip_code', 'like', '%' . $request->zip_code . '%'))
                    ->when($request->filled('population'), fn($query) => $query->where('population', 'like', '%' . $request->population . '%'))
                    ->orderBy('id', 'desc')
                    ->paginate($request->pagination ?? 10);

                $companies = $query;

                $registeredThisMonth = Company::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count();

                $usersThisMonth = User::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count();

                $usersByArea = User::selectRaw('training_area, COUNT(*) as total')
                    ->groupBy('training_area')
                    ->pluck('total', 'training_area');

                return Inertia::render('Admin/AdminCompanies', [
                    'companies' => $companies->items(),
                    'pagination' => $companies,
                    'totalCompanies' => $companies->total(),
                    'registeredThisMonth' => $registeredThisMonth,
                    'usersThisMonth' => $usersThisMonth,
                    'usersByArea' => $usersByArea,
                    'totalOpenApplications' => $totalOpenApplications,
                    'filters' => $request->all(),
                ]);
                break;
            case 'notifications':
                $users = User::with('roles')->get();
                return Inertia::render('Admin/AdminNotifications', [
                    'users' => $users,
                ]);
                break;

            case "notifications":
                return Inertia::render('Admin/AdminNotifications', [
                ]);

                break;

            default:
                abort(404);
        }
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
            'content' => 'nullable|string',
        ]);

        foreach ($request->user_ids as $userId) {
            $notification = new \App\Models\Notification();
            $notification->user_id = $userId;
            $notification->type = $request->type;
            $notification->message = $request->content;
            $notification->is_read = false;
            $notification->save();
        }

        return redirect()->back()->with('success', 'Notificaciones enviadas exitosamente.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return Inertia::location('/admin/users/users');
    }

    public function deleteCompany($id)
    {
        $company = Company::findOrFail($id);
        $company->user()->delete();
        $company->delete();
        return redirect()->back()->with('success', 'Empresa eliminada exitosamente.');
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

        // All fields are optional for partial update
        $rules = [
            'name' => 'sometimes|string|max:255',
            'last_name_1' => 'sometimes|string|max:255',
            'last_name_2' => 'sometimes|nullable|string|max:255',
            'email' => "sometimes|email|unique:users,email,{$id}",
            'training_area' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:online,offline,blocked',
        ];
        $validated = $request->validate($rules);

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
            'address' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'population' => 'required|string|max:255',
        ]);

        $company->update($validated);
        return redirect()->back()->with('success', 'Empresa actualizada exitosamente.');
    }
}
