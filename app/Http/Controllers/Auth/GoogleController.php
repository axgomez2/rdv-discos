<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SystemSettingsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    protected $systemSettings;
    
    public function __construct(SystemSettingsService $systemSettings)
    {
        $this->systemSettings = $systemSettings;
    }
    
    /**
     * Redireciona o usuário para a página de autenticação do Google.
     */
    public function redirectToGoogle()
    {
        try {
            // Verifica se o login com Google está habilitado
            $enabled = (bool)$this->systemSettings->get('oauth', 'google_enabled', false);
            if (!$enabled) {
                Log::warning('Tentativa de login com Google quando está desabilitado');
                return redirect()->route('login')->with('error', 'O login com Google está temporariamente indisponível.');
            }
            
            // Configura o Socialite com as configurações do banco de dados
            $clientId = $this->systemSettings->get('oauth', 'google_client_id', '');
            $clientSecret = $this->systemSettings->get('oauth', 'google_client_secret', '');
            $redirectUrl = $this->systemSettings->get('oauth', 'google_redirect', route('auth.google.callback'));
            
            if (empty($clientId) || empty($clientSecret)) {
                Log::error('Configurações de OAuth do Google incompletas');
                return redirect()->route('login')->with('error', 'Configuração incompleta de OAuth. Entre em contato com o administrador.');
            }
            
            // Definir configurações de sessão para o estado CSRF
            Session::put('state', $state = Str::random(40));
            
            // Configurar o driver Google do Socialite com as configurações do banco
            config([
                'services.google' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect' => $redirectUrl,
                ]
            ]);
            
            Log::info('Iniciando redirecionamento para o Google');
            return Socialite::driver('google')
                ->with(['state' => $state])
                ->redirect();
        } catch (Exception $e) {
            Log::error('Erro no redirecionamento para o Google: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Ocorreu um erro ao tentar conectar com o Google.');
        }
    }

    /**
     * Obtém as informações do usuário do Google e realiza o login/registro.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // Verificar estado CSRF para prevenir ataques
            $state = $request->input('state');
            $savedState = Session::pull('state');
            
            if ($state !== $savedState) {
                Log::warning('CSRF state mismatch em callback do Google');
                return redirect()->route('login')->with('error', 'Falha na verificação de segurança. Tente novamente.');
            }
            
            // Configurações dinâmicas do Google OAuth
            $clientId = $this->systemSettings->get('oauth', 'google_client_id', '');
            $clientSecret = $this->systemSettings->get('oauth', 'google_client_secret', '');
            $redirectUrl = $this->systemSettings->get('oauth', 'google_redirect', route('auth.google.callback'));
            
            config([
                'services.google' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect' => $redirectUrl,
                ]
            ]);
            
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
