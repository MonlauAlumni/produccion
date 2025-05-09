<?php

namespace App\Http\Controllers\Landing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LandingController extends Controller
{
    public function index()
    {
        $userCount = \App\Models\User::count();
        $companyCount = \App\Models\Company::count();
        $currentStudentCount = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('role_id', 2);
        })->count();

        return Inertia::render('LandingPage', [
            'userCount' => $userCount,
            'companyCount' => $companyCount,
            'currentStudentCount' => $currentStudentCount,
        ]);
    }
}
