<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;


Route::get('/', function () {
    return Inertia::render('Home'); 
})->name('home');   

Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/admin/dashboard', function () {
        return Inertia::render('Admin');
    })->name('admin.dashboard');
});

Route::get('/complete-profile', [ProfileController::class, 'show'])->name('complete-profile');
Route::post('/complete-profile', [ProfileController::class, 'store']);


Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);

Route::get('github/redirect', [SocialLoginController::class, 'github_redirect'])->name('github.redirect');
Route::get('github/callback', [SocialLoginController::class, 'github_callback'])->name('github.callback');

Route::get('google/redirect', [SocialLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('google/callback', [SocialLoginController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('/redirect', [SocialLoginController::class, 'redirectToMicrosoft'])->name('microsoft.redirect');
Route::get('/callback', [SocialLoginController::class, 'handleMicrosoftCallback'])->name('microsoft.callback');