<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterCompanyController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Profile\ExperienceController;
use App\Http\Controllers\JobOffers\JobOfferController;
use App\Http\Controllers\JobOffers\JobApplicationController;
use App\Http\Middleware\IsAdministrator;
use App\Http\Middleware\IsCompany;
use App\Http\Middleware\IsStudent;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
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
    Route::put('/admin/user/{id}', [AdminController::class, 'updateUser'])->name('admin.user.update');
    Route::delete('/admin/user/{id}', [AdminController::class, 'deleteUser'])->name('admin.user.delete');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store']);
    Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy']);
    Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show']);

});
   

Route::middleware('auth')->group(function() {
    Route::get('/perfil/{slang}', [ProfileController::class, 'profile'])->name('perfil.show');
    Route::put('/perfil/{slang}', [ProfileController::class, 'update'])->name('perfil.update');
    Route::post('/perfil/{slug}/experiencia', [ExperienceController::class, 'storeWork'])->name('perfil.experience.store');
    Route::post('/perfil/{slang}/educacion', [ExperienceController::class, 'storeEducation'])->name('educacion.store');

    Route::get('/empresa/{slang}', [CompanyController::class, 'show'])->name('empresa.show');
    Route::post('/empresa/{slang}/update-logo', [CompanyController::class, 'updateLogo'])->name('empresa.updateLogo');
    
    Route::get('/job-offers/{id}', [JobOfferController::class, 'show'])->name('job-offers.show');

    Route::middleware(isCompany::class)->group(function() {
        Route::get('/job-offers/create', [JobOfferController::class, 'create'])->name('job-offers.create'); 
        Route::post('/job-offers', [JobOfferController::class, 'store'])->name('job-offers.store'); 

    });

    Route::get('/settings', [SettingsController::class, 'show'])->name('settings');
  

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