<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\User;
class AdminController extends Controller
{
    public function show(){
        return Inertia::render('Admin/Layouts/AdminLayout', [
            'user' => auth()->user()
        ]);
    }
}
