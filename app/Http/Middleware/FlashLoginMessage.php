<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlashLoginMessage
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check() && session()->has('login_success') && !session()->has('login_message_shown')) {
            session()->flash('login_message', 'Bem-vindo, ' . Auth::user()->name . '! Login efetuado com sucesso!');
            session()->put('login_message_shown', true);
        }

        return $response;
    }
}

