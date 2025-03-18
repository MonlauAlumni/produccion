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
use App\Http\Controllers\Social\SocialController;
use App\Http\Controllers\Social\GroupController;
use App\Http\Middleware\IsAdministrator;
use App\Http\Middleware\IsCompany;
use App\Http\Middleware\IsStudent;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
use Illuminate\Support\Facades\Auth;    


Route::fallback(function () {
    return Inertia::render('404_page'); 
});

Route::get('/', function () {
    return Inertia::render('LandingPage');
});

Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/admin/{page}', [AdminController::class, 'show'])->name('admin.page');
    Route::get('/admin/user/{id}', [AdminController::class, 'singleUser'])->name('admin.user');
    Route::put('/admin/user/{id}', [AdminController::class, 'updateUser'])->name('admin.user.update');
    Route::delete('/admin/user/{id}', [AdminController::class, 'deleteUser'])->name('admin.user.delete');

    Route::get('/admin/company/{id}', [AdminController::class, 'singleCompany'])->name('admin.company');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store']);
    Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy']);
    Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show']);
});
   

Route::middleware('auth')->group(function() {
    Route::get('/home', [JobOfferController::class, 'index'])->name('home');

    Route::get('/perfil/{slang}', [ProfileController::class, 'profile'])->name('perfil.show');
    Route::post('/perfil/{slang}', [ProfileController::class, 'update'])->name('perfil.update');

    Route::post('/perfil/{slang}/experiencia', [ExperienceController::class, 'storeWork'])->name('perfil.experience.store');
    Route::put('/perfil/{slang}/experiencia/{id}', [ExperienceController::class, 'updateWork'])->name('perfil.experience.update');
    Route::delete('/perfil/{slang}/experiencia/{id}', [ExperienceController::class, 'deleteWork'])->name('perfil.experience.delete');

    Route::post('/perfil/{slang}/educacion', [ExperienceController::class, 'storeEducation'])->name('perfil.educacion.store');
    Route::put('/perfil/{slang}/educacion/{id}', [ExperienceController::class, 'updateEducation'])->name('perfil.educacion.update');
    Route::delete('/perfil/{slang}/educacion/{id}', [ExperienceController::class, 'deleteEducation'])->name('perfil.educacion.delete');
    
    Route::post('/perfil/{slang}/update-banner', [ProfileController::class, 'updateBanner'])->name('perfil.updateBanner');
    Route::post('/perfil/{slang}/update-profile-picture', [ProfileController::class, 'updateProfilePicture'])->name('perfil.updateProfilePicture');
    Route::get('/perfil/{slang}/download-cv', [ProfileController::class, 'downloadCV'])->name('perfil.downloadCV');
    
    Route::get('/empresa/{slang}', [CompanyController::class, 'show'])->name('empresa.show');
    Route::post('/empresa/{slang}/update-logo', [CompanyController::class, 'updateLogo'])->name('empresa.updateLogo');
    Route::post('/empresa/{slang}/update', [CompanyController::class, 'update'])->name('empresa.update');
    Route::post('/empresa/{slang}/update-banner', [CompanyController::class, 'updateBanner'])->name('empresa.updateBanner');


    Route::get('/ofertas/crear', [JobOfferController::class, 'create'])->name('ofertas.crear'); 
    Route::post('/ofertas/crear', [JobOfferController::class, 'store'])->name('ofertas.store'); 
    Route::get('/ofertas', [JobOfferController::class, 'index'])->name('ofertas.index');
    Route::get('/ofertas/{id}', [JobOfferController::class, 'show'])->name('job-offers.show');

    Route::post('/ofertas/{id}/aplicar', [JobApplicationController::class, 'store'])->name('job-application.store');

    Route::get('/mis-ofertas', [JobOfferController::class, 'myOffers'])->name('my-offers.index');

    Route::get('/connect', [SocialController::class, 'show'])->name('connect.show'); 
    Route::get('/grupos', [App\Http\Controllers\Social\GroupController::class, 'index'])->name('groups.index');
    Route::get('/grupos/nuevo', [GroupController::class, 'showCreateGroup'])->name('create-group.show');
    Route::get('/grupos/{slug}', [App\Http\Controllers\Social\GroupController::class, 'show'])->name('groups.show');

    Route::post('/grupos/nuevo', [App\Http\Controllers\Social\GroupController::class, 'createGroup'])->name('groups.store');
    Route::post('/grupos/{slug}/update-banner', [App\Http\Controllers\Social\GroupController::class, 'updateBanner'])->name('groups.update-banner');
    Route::post('/grupos/{slug}/update-logo', [App\Http\Controllers\Social\GroupController::class, 'updateLogo'])->name('groups.update-logo');
    Route::post('/grupos/{slug}/join', [App\Http\Controllers\Social\GroupController::class, 'joinGroup'])->name('groups.join');
    Route::post('/grupos/{slug}/invite', [App\Http\Controllers\Social\GroupController::class, 'inviteMember'])->name('groups.invite');
    Route::post('/grupos/{groupId}/posts', [GroupController::class, 'storePost'])->name('group.storePost');
    Route::post('/grupos/{groupId}/posts/{postId}/comment', [GroupController::class, 'postComment'])->name('group.postComment');



    Route::get('/configuracion', [SettingsController::class, 'show'])->name('settings.show');
    Route::get('/configuracion/cambiar-contraseña', [SettingsController::class, 'changePassword'])->name('change.password');
    Route::put('/settings/update-appearance', [SettingsController::class, 'updateAppearance'])->name('update.appearance');
    Route::put('/settings/change-password', [SettingsController::class, 'updatePassword'])->name('update.password');
    Route::put('/settings/change-language', [SettingsController::class, 'changeLanguage'])->name('change.language');
    Route::put('/user/update', [SettingsController::class, 'updateUser'])->name('user.update');

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
    Route::get('/login-monlau', [LoginController::class, 'show'])->name('login.monlau');
    Route::post('/login-monlau', [LoginController::class, 'store']);

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