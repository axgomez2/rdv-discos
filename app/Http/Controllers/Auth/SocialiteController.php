<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redireciona o usuário para a página de autenticação do provedor.
     *
     * @param string $provider
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtém as informações do usuário do provedor.
     *
     * @param string $provider
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            // Verifica se o usuário já existe no banco de dados
            $user = User::where('email', $socialUser->getEmail())->first();

            // Se não existir, cria um novo usuário
            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(16)), // Senha aleatória
                    'email_verified_at' => now(), // Usuário já verificado pelo provedor
                ]);
            }

            // Autentica o usuário
            Auth::login($user, true);

            return redirect()->intended('/dashboard');

        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Ocorreu um erro durante a autenticação social: ' . $e->getMessage());
        }
    }
}
