<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use App\Observers\ProfileObserver;
use App\Models\Profile;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TwoFactorChallengeViewResponse::class, function () {
            return new class implements TwoFactorChallengeViewResponse {
                public function toResponse($request)
                {
                    return Inertia::render('Auth/TwoFactorChallenge')->toResponse($request);
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    Inertia::share([
        'user' => function () {
            $user = auth()->user();
    
            if (!$user) {
                return null;
            }
    
            // Solo pasar los campos necesarios
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_picture' => optional($user->profile)->profile_picture,
                'company' => optional($user->company)->only(['company_name', 'id', 'profile_picture']), // Asegúrate de que 'profile_picture' está incluido
            ];
        },
    ]);
    
    

    Profile::observe(ProfileObserver::class);
}

}

