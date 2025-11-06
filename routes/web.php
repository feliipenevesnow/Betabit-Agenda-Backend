<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use Laravel\Fortify\Http\Controllers\ProfileInformationController;
use Laravel\Fortify\Http\Controllers\PasswordController;

Route::get('/', function () {
    return response()->noContent(200);
});

Route::middleware('web')->group(function () {
    
    Route::get('/debug-user', function () {
        return Auth::user() ?? ['message' => 'Nenhum usuário autenticado'];
    });

    Route::middleware('auth')->group(function () {
        
        Route::put('/user/profile-information', [ProfileInformationController::class, 'update'])
            ->name('user-profile-information.update');
            
        Route::put('/user/password', [PasswordController::class, 'update'])
            ->name('user-password.update');
    });
});