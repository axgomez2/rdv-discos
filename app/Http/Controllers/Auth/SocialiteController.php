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
            Log::info("Rota usada: " . request()->route()->getName());
            
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
                
                // Obter a URL de redirecionamento configurada no banco de dados
                $configuredRedirect = $this->systemSettings->get('oauth', 'google_redirect', '');
                
                // Se tiver um redirect configurado no banco, usar esse em vez do calculado
                if (!empty($configuredRedirect)) {
                    Log::info("Substituindo URL de redirecionamento pela configurada no banco: $configuredRedirect");
                    $redirectUrl = $configuredRedirect;
                } else {
                    // Determina URL de callback baseado na rota que foi usada
                    $routeName = request()->route()->getName() ?? '';
                    if ($routeName === 'login.with.google') {
                        // Usar a URL de callback alternativa
                        $redirectUrl = url('/google-callback');
                        Log::info("Usando URL de callback alternativa: $redirectUrl");
                    } else {
                        // Usa a URL de callback padrão
                        $redirectUrl = url('/auth/google/callback');
                        Log::info("Usando URL de callback padrão: $redirectUrl");
                    }
                }
                
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
            
            // Tenta criar a URL de redirecionamento do Socialite com escopos explícitos para Google
            try {
                if ($provider === 'google') {
                    // Adicionar escopos explícitos para garantir o acesso correto
                    $redirectResponse = Socialite::driver($provider)
                        ->scopes(['openid', 'profile', 'email'])
                        ->redirect();
                } else {
                    $redirectResponse = Socialite::driver($provider)->redirect();
                }
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
     * Manipula o callback do provider após a autenticação
     * 
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($provider)
    {
        try {
            Log::info("Recebendo callback do provedor: $provider");
            Log::info("Rota de callback usada: " . request()->route()->getName());
            Log::info("URL completa: " . request()->fullUrl());
            
            // Se for Google, tenta obter as configurações do DB para garantir consistência
            if ($provider === 'google') {
                // Obter as credenciais do banco de dados
                $clientId = $this->systemSettings->get('oauth', 'google_client_id', '');
                $clientSecret = $this->systemSettings->get('oauth', 'google_client_secret', '');
                
                // Obter a URL de redirecionamento configurada no banco de dados
                $configuredRedirect = $this->systemSettings->get('oauth', 'google_redirect', '');
                
                // Se tiver um redirect configurado no banco, usar esse
                if (!empty($configuredRedirect)) {
                    $redirectUrl = $configuredRedirect;
                    Log::info("Usando URL de redirecionamento configurada no banco: $redirectUrl");
                } else {
                    // Determina URL de callback baseado na rota que foi usada
                    $routeName = request()->route()->getName() ?? '';
                    if (in_array($routeName, ['google.callback', 'web.auth.google.callback', 'direct.auth.google.callback'])) {
                        // URL de callback alternativa
                        $redirectUrl = url('/google-callback');
                    } else {
                        // URL de callback padrão
                        $redirectUrl = url('/auth/google/callback');
                    }
                }
                
                // Configurar o Socialite com as credenciais do banco
                config([
                    'services.google.client_id' => $clientId,
                    'services.google.client_secret' => $clientSecret,
                    'services.google.redirect' => $redirectUrl
                ]);
                
                Log::info("Configuração do callback Google OAuth: redirect=$redirectUrl");
            }
            
            try {
                // Tenta obter o usuário
                if ($provider === 'google') {
                    $socialUser = Socialite::driver($provider)
                        ->scopes(['openid', 'profile', 'email'])
                        ->stateless()  // Tenta sem estado para evitar problemas de sessão
                        ->user();
                } else {
                    $socialUser = Socialite::driver($provider)->user();
                }
                
                Log::info("Usuário obtido com sucesso do provedor $provider: " . $socialUser->getEmail());
            } catch (\Exception $e) {
                Log::error("Erro ao obter usuário do provedor $provider: " . $e->getMessage());
                throw $e;
            }
            
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
            return redirect()->intended('/');

        } catch (Exception $e) {
            Log::error("Erro durante autenticação com $provider: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect('/login')->with('error', 'Ocorreu um erro durante a autenticação social: ' . $e->getMessage());
        }
    }
}
