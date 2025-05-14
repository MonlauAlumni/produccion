<?php 

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Company;
use App\Observers\CompanyObserver;
use App\Observers\UserObserver;

class EventServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
        User::observe(UserObserver::class);
        Company::observe(CompanyObserver::class);
    }
}