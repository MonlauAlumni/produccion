<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\User;
class AdminController extends Controller
{
    public function show($page)
    {
        switch ($page) {
            case 'dashboard':
                return Inertia::render('Admin/AdminHome', [
                    'user' => auth()->user()
                ]);

            case 'users':
                return Inertia::render('Admin/AdminUsers', [
                    'users' => User::all()
                ]);
        }
    }

}
