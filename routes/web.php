<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return Inertia::render('Home'); 
});

Route::get('/complete-profile', [ProfileController::class, 'show'])->name('complete-profile');
Route::post('/complete-profile', [ProfileController::class, 'store']);


Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

<<<<<<< HEAD
Route::get('github/redirect', [SocialLoginController::class, 'github_redirect'])->name('github.redirect');
Route::get('github/callback', [SocialLoginController::class, 'github_callback'])->name('github.callback');
=======
>>>>>>> Ponga
