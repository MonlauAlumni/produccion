<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterCompanyController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Profile\ExperienceController;
use App\Http\Middleware\IsAdministrator;
use App\Http\Middleware\IsCompany;
use App\Http\Middleware\IsStudent;
use Illuminate\Support\Facades\Auth;    


Route::get('/home', function () {

    return Inertia::render('Home');
})->name('home');
Route::get('/', function () {
    return Inertia::render('LandingPage');
});

Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/admin/{page}', [AdminController::class, 'show'])->name('admin.page');
    Route::get('/admin/user/{id}', [AdminController::class, 'singleUser'])->name('admin.user');
});
   

Route::middleware('auth')->group(function() {
    Route::get('/perfil/{slang}', [ProfileController::class, 'profile'])->name('perfil.show');
    Route::put('/perfil/{slang}', [ProfileController::class, 'update'])->name('perfil.update');
    Route::post('/perfil/{slug}/experiencia', [ExperienceController::class, 'storeWork'])->name('perfil.experience.store');
    Route::post('/perfil/{slang}/educacion', [ExperienceController::class, 'storeEducation'])->name('educacion.store');


   
    // Route::middleware(isAdministrator::class)->prefix('admin')->group(function() {
    //     Route::get('/dashboard', function () {
    //        dd('admin');
    //     })->name('admin.dashboard');
    // });
    // Route::middleware(isCompany::class)->prefix('company')->group(function() {
    //     Route::get('/dashboard', function () {
    //         dd('company');
    //     })->name('company.dashboard');
    // });
    // Route::middleware(isStudent::class)->prefix('user')->group(function() {
    //     Route::get('/dashboard', function () {
    //         dd('user');
    //     })->name('user.dashboard');
    // });

});


Route::middleware(['guest'])->group(function () {
    Route::get('/complete-profile', [ProfileController::class, 'show'])->name('complete-profile');
    Route::post('/complete-profile', [ProfileController::class, 'store']);

    Route::get('/register-company', [RegisterCompanyController::class, 'create'])->name('register-company');
    Route::post('/register-company', [RegisterCompanyController::class, 'store']);
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
});

Route::get('logout' , function() {
    Auth::logout(); 
    return redirect('/login'); 
});