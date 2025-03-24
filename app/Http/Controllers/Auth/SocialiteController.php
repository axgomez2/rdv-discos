<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SystemSettingsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    protected $systemSettings;

    public function __construct(SystemSettingsService $systemSettings)
    {
        $this->systemSettings = $systemSettings;
    }

    /**
     * Redireciona o usuário para a página de autenticação do provedor.
     *
     * @param string $provider
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        try {
            // Log para depuração
            Log::info("Iniciando autenticação com provedor: $provider");
            
            // Verificar se o provedor é suportado
            if (!in_array($provider, ['google', 'facebook'])) {
                Log::error("Provedor não suportado: $provider");
                return redirect('/login')->with('error', "Provedor de autenticação '$provider' não é suportado.");
            }

            // Se for Google, verifica as configurações do banco de dados
            if ($provider === 'google') {
                // Verifica se está habilitado
                $enabled = (bool)$this->systemSettings->get('oauth', 'google_enabled', false);
                if (!$enabled) {
                    Log::error('Google OAuth authentication is disabled in settings');
                    return redirect('/login')->with('error', 'Autenticação com Google está desabilitada no momento.');
                }

                // Obter as credenciais do banco de dados
                $clientId = $this->systemSettings->get('oauth', 'google_client_id', '');
                $clientSecret = $this->systemSettings->get('oauth', 'google_client_secret', '');
                
                // Usa a URL de callback consistente com as rotas definidas
                $redirectUrl = url('/auth/google/callback');
                
                if (empty($clientId) || empty($clientSecret)) {
                    Log::error('Google OAuth credentials are not configured in database');
                    return redirect('/login')->with('error', 'Configuração do Google OAuth incompleta. Entre em contato com o administrador.');
                }

                // Limpar o cache do Socialite
                Socialite::forgetDrivers();

                // Configurar o Socialite com as credenciais do banco
                config([
                    'services.google.client_id' => $clientId,
                    'services.google.client_secret' => $clientSecret,
                    'services.google.redirect' => $redirectUrl
                ]);

                Log::info("Google OAuth configurado com credenciais do banco de dados");
                Log::info("Redirect URI configurado: " . $redirectUrl);
                
                // Log adicional para depuração
                Log::info("Client ID: " . substr($clientId, 0, 5) . "..." . substr($clientId, -5));
                Log::info("Client Secret: " . (empty($clientSecret) ? "Não configurado" : "Configurado"));
            }
            
            // Tenta criar a URL de redirecionamento do Socialite
            try {
                $redirectResponse = Socialite::driver($provider)->redirect();
                Log::info("Redirecionamento para $provider criado com sucesso");
                return $redirectResponse;
            } catch (\Exception $e) {
                Log::error("Erro ao criar redirecionamento para o provedor $provider: " . $e->getMessage());
                throw $e; // Repropagar para o catch externo
            }
        } catch (Exception $e) {
            Log::error("Erro ao redirecionar para o provedor $provider: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect('/login')->with('error', "Não foi possível conectar ao provedor $provider: " . $e->getMessage());
        }
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
            // Log para depuração
            Log::info("Processando callback de $provider");

            // Se for Google, configura com as credenciais do banco de dados
            if ($provider === 'google') {
                $clientId = $this->systemSettings->get('oauth', 'google_client_id', '');
                $clientSecret = $this->systemSettings->get('oauth', 'google_client_secret', '');
                
                // Usa a URL de callback consistente com as rotas definidas
                $redirectUrl = url('/auth/google/callback');

                // Limpar o cache do Socialite
                Socialite::forgetDrivers();

                config([
                    'services.google.client_id' => $clientId,
                    'services.google.client_secret' => $clientSecret,
                    'services.google.redirect' => $redirectUrl
                ]);

                Log::info("Google OAuth credenciais carregadas para o callback");
            }
            
            $socialUser = Socialite::driver($provider)->user();
            
            // Log para depuração
            Log::info("Usuário obtido do provedor $provider: " . $socialUser->getEmail());

            // Verifica se o usuário já existe no banco de dados
            $user = User::where('email', $socialUser->getEmail())->first();

            // Se não existir, cria um novo usuário
            if (!$user) {
                Log::info("Criando novo usuário para: " . $socialUser->getEmail());
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(16)), // Senha aleatória
                    'email_verified_at' => now(), // Usuário já verificado pelo provedor
                ]);
            } else {
                Log::info("Usuário encontrado no banco de dados: " . $user->email);
            }

            // Autentica o usuário
            Auth::login($user, true);
            
            Log::info("Autenticação bem-sucedida para: " . $user->email);
            return redirect()->intended('/dashboard');

        } catch (Exception $e) {
            Log::error("Erro durante autenticação com $provider: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect('/login')->with('error', 'Ocorreu um erro durante a autenticação social: ' . $e->getMessage());
        }
    }
}
