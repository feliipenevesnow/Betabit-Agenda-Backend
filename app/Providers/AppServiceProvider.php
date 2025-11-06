<?php

namespace App\Providers;

// Importações para Fortify:
use App\Actions\Fortify\CreateNewUser; // Ação que cria o usuário
use App\Actions\Fortify\UpdateUserPassword; // Ação que atualiza a senha
use App\Actions\Fortify\UpdateUserProfileInformation; // Ação que atualiza o perfil

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

// Contratos do Fortify:
use Laravel\Fortify\Contracts\CreatesNewUsers; // Contrato para criação de usuário (O QUE FALTAVA)
use Laravel\Fortify\Contracts\UpdatesUserPasswords; 
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 🛑 MAPEAMENTO CRUCIAL QUE RESOLVE O ERRO 500:
        // Vincula a Interface (Contrato) Fortify à sua implementação (Action).
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);
        
        // Mapeamentos existentes (corretos)
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