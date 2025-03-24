<?php

use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;

// Rotas para autenticação social (Google)
Route::middleware('guest')->group(function () {
    // Rotas específicas para o Google
    Route::get('/auth/google', [SocialiteController::class, 'redirectToProvider'])
        ->name('auth.google')
        ->defaults('provider', 'google');
        
    Route::get('/auth/google/callback', [SocialiteController::class, 'handleProviderCallback'])
        ->name('auth.google.callback')
        ->defaults('provider', 'google');
});
