<?php

namespace App\Providers;

use App\Services\SystemSettingsService;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Event;

class OAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Este método é chamado antes do boot e antes que as rotas sejam carregadas
        // Não fazemos nada aqui que dependa de rotas
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registramos um evento para quando a aplicação estiver totalmente inicializada
        // e apenas quando uma solicitação web estiver em andamento
        if (!$this->app->runningInConsole()) {
            Event::listen('Illuminate\Foundation\Http\Events\RequestHandled', function () {
                $this->configureSocialiteForNextRequest();
            });
        }

        // Quando o Socialite for usado, configuramos ele com os dados do banco
        $this->app->resolving('Laravel\Socialite\SocialiteManager', function ($socialite) {
            $this->configureGoogleDriver($socialite);
        });
    }
    
    /**
     * Configura o Socialite para a próxima requisição
     */
    protected function configureSocialiteForNextRequest(): void
    {
        try {
            // Limpar cache do Socialite a cada requisição para usar configurações atualizadas
            Socialite::forgetDrivers();
        } catch (\Exception $e) {
            // Silenciosamente ignorar qualquer erro
        }
    }
    
    /**
     * Configura o driver do Google com as configurações do banco de dados
     */
    protected function configureGoogleDriver($socialite): void
    {
        try {
            // Tentamos obter as configurações do banco de dados apenas no contexto de uma requisição web
            if (!$this->app->runningInConsole() && request() !== null) {
                /** @var SystemSettingsService $systemSettings */
                $systemSettings = $this->app->make(SystemSettingsService::class);
                
                // Obtemos as configurações
                $googleClientId = $systemSettings->get('oauth', 'google_client_id', '');
                $googleClientSecret = $systemSettings->get('oauth', 'google_client_secret', '');
                $googleRedirect = $systemSettings->get('oauth', 'google_redirect', '');
                
                // Apenas configuramos se houver dados válidos
                if (!empty($googleClientId) && !empty($googleClientSecret)) {
                    config(['services.google.client_id' => $googleClientId]);
                    config(['services.google.client_secret' => $googleClientSecret]);
                    
                    if (!empty($googleRedirect)) {
                        config(['services.google.redirect' => $googleRedirect]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Silenciosamente ignorar erros
            if ($this->app->hasDebugModeEnabled()) {
                logger()->error('Erro ao configurar driver Google do Socialite: ' . $e->getMessage());
            }
        }
    }
}
