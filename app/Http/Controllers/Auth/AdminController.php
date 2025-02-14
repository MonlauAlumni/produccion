<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\User;
class AdminController extends Controller
{
    public function show(){
        return Inertia::render('Auth/Admin', [
            'user' => auth()->user()
        ]);
    }
}
