<?php

use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;

// Rotas para autenticação social com Google - sem middleware
// Estas rotas são mais diretas e têm maior prioridade para evitar problemas de 404

// Rota de redirecionamento para o Google
Route::get('/auth/google', [SocialiteController::class, 'redirectToProvider'])
    ->name('direct.auth.google')
    ->defaults('provider', 'google');
    
// Rota de callback do Google
Route::get('/auth/google/callback', [SocialiteController::class, 'handleProviderCallback'])
    ->name('direct.auth.google.callback')
    ->defaults('provider', 'google');
