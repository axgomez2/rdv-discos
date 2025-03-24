<?php

namespace App\Providers;

use App\Services\SystemSettingsService;
use Illuminate\Support\ServiceProvider;

class OAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            $systemSettings = app(SystemSettingsService::class);
            
            // Configurar Google OAuth a partir do banco de dados
            $googleClientId = $systemSettings->get('oauth', 'google_client_id', '');
            $googleClientSecret = $systemSettings->get('oauth', 'google_client_secret', '');
            $googleRedirect = $systemSettings->get('oauth', 'google_redirect', route('auth.google.callback'));
            
            // Sobrescrever as configurações de serviço com os valores do banco de dados
            if (!empty($googleClientId) && !empty($googleClientSecret)) {
                config([
                    'services.google.client_id' => $googleClientId,
                    'services.google.client_secret' => $googleClientSecret,
                    'services.google.redirect' => $googleRedirect,
                ]);
            }
            
            // Fazer o mesmo para outros providers de OAuth se necessário
            
        } catch (\Exception $e) {
            // Silenciosamente ignorar erros durante o boot
            // Isso pode acontecer se o banco de dados ainda não estiver configurado
            // durante a execução de comandos como migrate:fresh
            logger()->error('Erro ao carregar configurações OAuth: ' . $e->getMessage());
        }
    }
}
