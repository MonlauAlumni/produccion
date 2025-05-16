<?php

use App\Http\Controllers\Landing\LandingController;
use App\Http\Controllers\Notifications\NotificationController;
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
use App\Http\Controllers\Social\PostController;
use App\Http\Controllers\Social\EventController;
use App\Http\Controllers\Messaging\MessageController;
use App\Http\Middleware\IsAdministrator;
use App\Http\Middleware\IsCompany;
use App\Http\Middleware\IsStudent;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Students\StudentController;

Route::fallback(function () {
    return Inertia::render('404_page');
});

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/admin/{page}', [AdminController::class, 'show'])->name('admin.page');
    Route::get('/admin/user/{id}', [AdminController::class, 'singleUser'])->name('admin.user');
    Route::put('/admin/user/{id}', [AdminController::class, 'updateUser'])->name('admin.user.update');
    Route::delete('/admin/user/{id}', [AdminController::class, 'deleteUser'])->name('admin.user.delete');

    Route::get('/admin/company/{id}', [AdminController::class, 'singleCompany'])->name('admin.company');
    Route::put('/admin/company/{id}', [AdminController::class, 'updateCompany'])->name('admin.company.update');
    Route::delete('/admin/company/{id}', [AdminController::class, 'deleteCompany'])->name('admin.company.delete');
});



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store']);
    Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy']);
    Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show']);
});


Route::middleware('auth')->group(function () {

    Route::group(['middleware' => ['role:alumne|admin']], function () {

        Route::post('/perfil/{slang}', [ProfileController::class, 'update'])->name('perfil.update');
        Route::post('/perfil/{slang}/update-banner', [ProfileController::class, 'updateBanner'])->name('perfil.updateBanner');
        Route::post('/perfil/{slang}/update-profile-picture', [ProfileController::class, 'updateProfilePicture'])->name('perfil.updateProfilePicture');

        Route::post('/perfil/{slang}/educacion', [ExperienceController::class, 'storeEducation'])->name('perfil.educacion.store');
        Route::put('/perfil/{slang}/educacion/{id}', [ExperienceController::class, 'updateEducation'])->name('perfil.educacion.update');
        Route::delete('/perfil/{slang}/educacion/{id}', [ExperienceController::class, 'deleteEducation'])->name('perfil.educacion.delete');

        Route::post('/perfil/{slang}/experiencia', [ExperienceController::class, 'storeWork'])->name('perfil.experience.store');
        Route::put('/perfil/{slang}/experiencia/{id}', [ExperienceController::class, 'updateWork'])->name('perfil.experience.update');
        Route::delete('/perfil/{slang}/experiencia/{id}', [ExperienceController::class, 'deleteWork'])->name('perfil.experience.delete');

        Route::post('/ofertas/{id}/aplicar', [JobApplicationController::class, 'store'])->name('job-application.store');
        Route::post('/ofertas/{id}/guardar', [JobOfferController::class, 'toggleSave'])->name('job-offers.toggleSave');

        Route::get('/mis-ofertas', [JobOfferController::class, 'myOffers'])->name('my-offers.index');

        Route::get('/empresas', [CompanyController::class, 'index'])->name('companies.index');

        Route::post('/perfil/{slang}/skills', [ProfileController::class, 'updateSkills'])->name('perfil.skills.update');
        Route::delete('/perfil/{slang}/skills/{skillId}', [ProfileController::class, 'removeSkill'])->name('perfil.skills.remove');

    });
    
    Route::group(['middleware' => ['role:empresa|admin']], function () {

        Route::post('/empresa/{slang}/update-logo', [CompanyController::class, 'updateLogo'])->name('empresa.updateLogo');
        Route::post('/empresa/{slang}/update', [CompanyController::class, 'update'])->name('empresa.update');
        Route::post('/empresa/{slang}/update-banner', [CompanyController::class, 'updateBanner'])->name('empresa.updateBanner');

        Route::get('/ofertas/crear', [JobOfferController::class, 'create'])->name('ofertas.crear');
        Route::post('/ofertas/crear', [JobOfferController::class, 'store'])->name('ofertas.store');

        Route::get('/gestion-candidatos', [JobApplicationController::class, 'index'])->name('job-applications.index');
        Route::post('/gestion-candidatos/{applicationId}/change-status', [JobApplicationController::class, 'changeStatus'])->name('job-applications.changeStatus');

        Route::delete('/gestion-candidatos/{applicationId}', [JobApplicationController::class, 'destroy'])->name('job-applications.destroy');

        Route::get('/alumnos', [StudentController::class, 'index'])->name('student.show');
    });

    Route::get('/home', [JobOfferController::class, 'index'])->name('home');

    Route::get('/perfil/{slang}', [ProfileController::class, 'profile'])->name('perfil.show');
    Route::get('/perfil/{slang}/download-cv', [ProfileController::class, 'downloadCV'])->name('perfil.downloadCV');

    Route::get('/empresa/{slang}', [CompanyController::class, 'show'])->name('empresa.show');

    Route::get('/ofertas', [JobOfferController::class, 'index'])->name('ofertas.index');
    Route::get('/ofertas/{id}', [JobOfferController::class, 'show'])->name('job-offers.show');

    Route::get('/notificaciones', [NotificationController::class, 'show'])->name('notificaciones.show');

    Route::put('/notificaciones/{id}/mark-as-read', [NotificationController::class, 'update'])->name('notificaciones.update');

    Route::put('/notificaciones/mark-all-as-read', [NotificationController::class, 'updateAll'])->name('notificaciones.updateAll');


    Route::delete('/notificaciones/{id}', [NotificationController::class, 'destroy'])->name('notificaciones.destroy');

    Route::delete('/notificaciones', [NotificationController::class, 'destroyAll'])->name('notificaciones.destroyAll');

    Route::post('/notificaciones', [NotificationController::class, 'sendNotification'])->name('notificaciones.send');

    Route::get('/connect', [SocialController::class, 'show'])->name('connect.show');
    Route::get('/connect/search', [SocialController::class, 'showSearch'])->name('connect.search');

    Route::get('/grupos', [App\Http\Controllers\Social\GroupController::class, 'index'])->name('groups.index');
    Route::get('/grupos/nuevo', [GroupController::class, 'showCreateGroup'])->name('create-group.show');
    Route::get('/grupos/{slug}', [App\Http\Controllers\Social\GroupController::class, 'show'])->name('groups.show');

    Route::post('/grupos/nuevo', [App\Http\Controllers\Social\GroupController::class, 'createGroup'])->name('groups.store');
    Route::post('/grupos/{slug}/update-banner', [App\Http\Controllers\Social\GroupController::class, 'updateBanner'])->name('groups.update-banner');
    Route::post('/grupos/{slug}/update-logo', [App\Http\Controllers\Social\GroupController::class, 'updateLogo'])->name('groups.update-logo');
    Route::post('/grupos/{slug}/join', [App\Http\Controllers\Social\GroupController::class, 'joinGroup'])->name('groups.join');
    Route::post('/grupos/{slug}/invite', [App\Http\Controllers\Social\GroupController::class, 'inviteMember'])->name('groups.invite');
    Route::post('/grupos/{groupId}/posts/{postId}/comment', [PostController::class, 'addCommentInGroup'])->name('group.postComment');

    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/comment', [PostController::class, 'addComment'])->name('posts.comment');
    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike'])->name('posts.like');
    Route::post('/posts/group/{groupId}', [PostController::class, 'storeInGroup'])->name('group.storePost');
    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike'])->name('posts.like');

    Route::get('/eventos', [EventController::class, 'index'])->name('events.index');
    Route::get('/eventos/nuevo', [EventController::class, 'create'])->name('events.create');
    Route::post('/eventos/nuevo', [EventController::class, 'store'])->name('events.store');
    Route::get('/eventos/{slug}', [EventController::class, 'show'])->name('events.show');
    Route::get('/eventos/{slug}/editar', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/eventos/{slug}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/eventos/{slug}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::post('/eventos/{slug}/attend', [EventController::class, 'attend'])->name('events.attend');
    Route::post('/eventos/{slug}/cancel-attendance', [EventController::class, 'cancelAttendance'])->name('events.cancel-attendance');
    Route::post('/eventos/{slug}/remove-photo', [EventController::class, 'removePhoto'])->name('events.remove-photo');


    Route::get('/calendario', [CalendarController::class, 'index'])->name('calendar.index');

    Route::get('/configuracion', [SettingsController::class, 'show'])->name('settings.show');
    Route::get('/configuracion/cambiar-contraseña', [SettingsController::class, 'changePassword'])->name('change.password');
    Route::put('/settings/update-appearance', [SettingsController::class, 'updateAppearance'])->name('update.appearance');
    Route::put('/settings/change-password', [SettingsController::class, 'updatePassword'])->name('update.password');
    Route::put('/settings/change-language', [SettingsController::class, 'changeLanguage'])->name('change.language');
    Route::put('/user/update', [SettingsController::class, 'updateUser'])->name('user.update');


    //Messaging

    Route::get('/mensajes', [MessageController::class, 'index'])->name('messages.index');

    // Obtener mensajes de una conversación
    Route::get('/api/conversations/{conversation}/messages', [MessageController::class, 'getMessages']);

    // Enviar mensaje en una conversación existente
    Route::post('/api/conversations/{conversation}/messages', [MessageController::class, 'sendMessage']);

    // Crear nueva conversación (solo empresas)
    Route::post('/api/conversations', [MessageController::class, 'createConversation']);

    // Obtener todas las conversaciones
    Route::get('/api/conversations', [MessageController::class, 'getConversations']);

    // Marcar conversación como leída
    Route::post('/api/conversations/{conversation}/read', [MessageController::class, 'markAsRead']);

    // Buscar alumni para nueva conversación

    Route::get('/api/alumni/search', [MessageController::class, 'searchAlumni']);
    // Obtener trabajos de la empresa para nueva conversación
    Route::get('/api/company/jobs', [MessageController::class, 'getCompanyJobs']);

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

Route::get('logout', function () {
    Auth::logout();
    return redirect('/');
});