<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    /**
     * Redireciona o usuário para a página de autenticação do Google.
     */
    public function redirectToGoogle()
    {
        try {
            Log::info('Iniciando redirecionamento para o Google');
            return Socialite::driver('google')->redirect();
        } catch (Exception $e) {
            Log::error('Erro no redirecionamento para o Google: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Ocorreu um erro ao tentar conectar com o Google.');
        }
    }

    /**
     * Obtém as informações do usuário do Google e realiza o login/registro.
     */
    public function handleGoogleCallback()
    {
        try {
            Log::info('Recebendo callback do Google');

            $googleUser = Socialite::driver('google')->user();
            Log::info('Dados do usuário Google recebidos', ['email' => $googleUser->email]);

            // Verificar se um usuário com este e-mail já existe
            $existingUser = User::where('email', $googleUser->email)->first();
            
            if ($existingUser) {
                // Se o usuário existe mas não tem google_id, atualize
                if (empty($existingUser->google_id)) {
                    $existingUser->update([
                        'google_id' => $googleUser->id
                    ]);
                }
                
                $user = $existingUser;
                Log::info('Usuário existente atualizado', ['user_id' => $user->id]);
            } else {
                // Criar novo usuário
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt(uniqid(mt_rand(), true)),
                    'email_verified_at' => now(), // Usuários do Google já vêm com email verificado
                ]);
                
                Log::info('Novo usuário criado', ['user_id' => $user->id]);
            }

            Auth::login($user);

            Log::info('Usuário logado com sucesso', ['user_id' => $user->id]);

            return redirect()->intended('/');

        } catch (Exception $e) {
            Log::error('Erro no callback do Google: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Ocorreu um erro ao tentar fazer login com o Google.');
        }
    }
}
