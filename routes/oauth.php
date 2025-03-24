<?php

use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;

// Rotas para autenticação social (Google)
/* Rotas movidas para o arquivo web.php
Route::middleware('guest')->group(function () {
    // Rotas específicas para o Google
    Route::get('/login/google', [SocialiteController::class, 'redirectToProvider'])
        ->name('auth.google')
        ->defaults('provider', 'google');
        
    Route::get('/login/google/callback', [SocialiteController::class, 'handleProviderCallback'])
        ->name('auth.google.callback')
        ->defaults('provider', 'google');
});
*/
