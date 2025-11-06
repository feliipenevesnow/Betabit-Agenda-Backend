<?php

namespace App\Providers;


use App\Actions\Fortify\CreateNewUser; 
use App\Actions\Fortify\UpdateUserPassword; 
use App\Actions\Fortify\UpdateUserProfileInformation; 

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;


use Laravel\Fortify\Contracts\CreatesNewUsers; 
use Laravel\Fortify\Contracts\UpdatesUserPasswords; 
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
        
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);
        
        
        $this->app->singleton(UpdatesUserProfileInformation::class, UpdateUserProfileInformation::class);
        $this->app->singleton(UpdatesUserPasswords::class, UpdateUserPassword::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}