<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialLoginController;

Route::get('/', function () {
    return Inertia::render('Home'); 
});


Route::get('/register', [RegisterController::class, 'create'])->name('register');

Route::post('/register', [RegisterController::class, 'store']);

Route::get('github/redirect', [SocialLoginController::class, 'github_redirect'])->name('github.redirect');
Route::get('github/callback', [SocialLoginController::class, 'github_callback'])->name('github.callback');
