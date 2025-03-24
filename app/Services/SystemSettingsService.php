<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SystemSettingsService
{
    // Cache TTL em segundos (1 hora)
    protected const CACHE_TTL = 3600;
    
    /**
     * Obter uma configuração do sistema
     * 
     * @param string $group Grupo da configuração
     * @param string $key Chave da configuração
     * @param mixed $default Valor padrão caso a configuração não exista
     * @return mixed
     */
    public function get(string $group, string $key, $default = null)
    {
        $cacheKey = "system_settings:{$group}:{$key}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($group, $key, $default) {
            return SystemSetting::getByKey($group, $key, $default);
        });
    }
    
    /**
     * Salvar uma configuração do sistema
     * 
     * @param string $group Grupo da configuração
     * @param string $key Chave da configuração
     * @param mixed $value Valor da configuração
     * @param string|null $description Descrição opcional
     * @param bool $isEncrypted Indica se o valor deve ser criptografado
     * @return \App\Models\SystemSetting
     */
    public function set(string $group, string $key, $value, ?string $description = null, bool $isEncrypted = false)
    {
        $setting = SystemSetting::updateOrCreateSetting($group, $key, $value, $description, $isEncrypted);
        
        // Limpar o cache
        $this->clearCache($group, $key);
        
        return $setting;
    }
    
    /**
     * Obter todas as configurações de um grupo
     * 
     * @param string $group
     * @return array
     */
    public function getGroup(string $group)
    {
        $cacheKey = "system_settings:{$group}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($group) {
            return SystemSetting::getGroupSettings($group);
        });
    }
    
    /**
     * Verificar se uma configuração existe
     * 
     * @param string $group
     * @param string $key
     * @return bool
     */
    public function has(string $group, string $key): bool
    {
        return SystemSetting::where('group', $group)
            ->where('key', $key)
            ->exists();
    }
    
    /**
     * Limpar o cache de uma configuração específica
     * 
     * @param string $group
     * @param string $key
     */
    public function clearCache(string $group, string $key = null)
    {
        if ($key) {
            Cache::forget("system_settings:{$group}:{$key}");
        }
        
        Cache::forget("system_settings:{$group}");
    }
    
    /**
     * Limpar todo o cache de configurações
     */
    public function clearAllCache()
    {
        // Poderia usar pattern para limpar todos os caches relacionados
        // mas para maior compatibilidade, vamos buscar todos e limpar um a um
        $settings = SystemSetting::all();
        
        foreach ($settings as $setting) {
            $this->clearCache($setting->group, $setting->key);
        }
        
        // Limpar caches de grupos
        $groups = SystemSetting::select('group')->distinct()->pluck('group');
        foreach ($groups as $group) {
            Cache::forget("system_settings:{$group}");
        }
    }
    
    /**
     * Configurações específicas para Google OAuth
     */
    public function saveGoogleOauth(array $data)
    {
        $this->set('oauth', 'google_client_id', $data['client_id'], 'Google OAuth Client ID', false);
        $this->set('oauth', 'google_client_secret', $data['client_secret'], 'Google OAuth Client Secret', true);
        $this->set('oauth', 'google_redirect', $data['redirect'], 'Google OAuth Redirect URL', false);
        $this->set('oauth', 'google_enabled', $data['enabled'] ?? false, 'Google OAuth Enabled', false);
    }
    
    /**
     * Configurações específicas para PagSeguro
     */
    public function savePagSeguro(array $data)
    {
        $this->set('payment', 'pagseguro_email', $data['email'], 'PagSeguro Account Email', false);
        $this->set('payment', 'pagseguro_token', $data['token'], 'PagSeguro API Token', true);
        $this->set('payment', 'pagseguro_sandbox', $data['sandbox'] ?? false, 'PagSeguro Sandbox Mode', false);
        $this->set('payment', 'pagseguro_enabled', $data['enabled'] ?? false, 'PagSeguro Enabled', false);
    }
    
    /**
     * Configurações específicas para MercadoPago
     */
    public function saveMercadoPago(array $data)
    {
        $this->set('payment', 'mercadopago_public_key', $data['public_key'], 'MercadoPago Public Key', false);
        $this->set('payment', 'mercadopago_access_token', $data['access_token'], 'MercadoPago Access Token', true);
        $this->set('payment', 'mercadopago_sandbox', $data['sandbox'] ?? false, 'MercadoPago Sandbox Mode', false);
        $this->set('payment', 'mercadopago_enabled', $data['enabled'] ?? false, 'MercadoPago Enabled', false);
    }
    
    /**
     * Configurações específicas para MercadoEnvio (frete)
     */
    public function saveMercadoEnvio(array $data)
    {
        $this->set('shipping', 'mercadoenvio_api_key', $data['api_key'], 'MercadoEnvio API Key', false);
        $this->set('shipping', 'mercadoenvio_secret_key', $data['secret_key'], 'MercadoEnvio Secret Key', true);
        $this->set('shipping', 'mercadoenvio_seller_id', $data['seller_id'], 'MercadoEnvio Seller ID', false);
        $this->set('shipping', 'mercadoenvio_sandbox', $data['sandbox'] ?? false, 'MercadoEnvio Sandbox Mode', false);
        $this->set('shipping', 'mercadoenvio_enabled', $data['enabled'] ?? false, 'MercadoEnvio Enabled', false);
    }
    
    /**
     * Configurações específicas para Melhor Envio
     */
    public function saveMelhorEnvio(array $data)
    {
        $this->set('shipping', 'melhorenvio_client_id', $data['client_id'], 'Melhor Envio Client ID', false);
        $this->set('shipping', 'melhorenvio_client_secret', $data['client_secret'], 'Melhor Envio Client Secret', true);
        $this->set('shipping', 'melhorenvio_sandbox', $data['sandbox'] ?? 'false', 'Melhor Envio Sandbox Mode', false);
        $this->set('shipping', 'melhorenvio_enabled', $data['enabled'] ?? 'false', 'Melhor Envio Enabled', false);
        
        // Salvar token e informações de autenticação, se fornecidas
        if (isset($data['token'])) {
            $this->set('shipping', 'melhorenvio_token', $data['token'], 'Melhor Envio Access Token', true);
        }
        
        if (isset($data['refresh_token'])) {
            $this->set('shipping', 'melhorenvio_refresh_token', $data['refresh_token'], 'Melhor Envio Refresh Token', true);
        }
        
        if (isset($data['token_expires_at'])) {
            $this->set('shipping', 'melhorenvio_token_expires_at', $data['token_expires_at'], 'Melhor Envio Token Expiration', false);
        }
        
        // Opções adicionais
        $this->set('shipping', 'melhorenvio_auto_approve', $data['auto_approve'] ?? 'false', 'Melhor Envio Auto Approve Orders', false);
        
        // Serviços disponíveis - Correios
        $this->set('shipping', 'melhorenvio_service_pac', $data['service_pac'] ?? 'false', 'Melhor Envio Service PAC', false);
        $this->set('shipping', 'melhorenvio_service_sedex', $data['service_sedex'] ?? 'false', 'Melhor Envio Service SEDEX', false);
        $this->set('shipping', 'melhorenvio_service_mini', $data['service_mini'] ?? 'false', 'Melhor Envio Service Mini', false);
        
        // Serviços disponíveis - Transportadoras
        $this->set('shipping', 'melhorenvio_service_jadlog', $data['service_jadlog'] ?? 'false', 'Melhor Envio Service Jadlog', false);
        $this->set('shipping', 'melhorenvio_service_azul', $data['service_azul'] ?? 'false', 'Melhor Envio Service Azul', false);
        $this->set('shipping', 'melhorenvio_service_latam', $data['service_latam'] ?? 'false', 'Melhor Envio Service LATAM', false);
        
        return true;
    }
    
    /**
     * Configurações específicas para Correios
     */
    public function saveCorreios(array $data)
    {
        $this->set('shipping', 'correios_usuario', $data['usuario'], 'Correios Usuário', false);
        $this->set('shipping', 'correios_senha', $data['senha'], 'Correios Senha', true);
        $this->set('shipping', 'correios_empresa_codigo', $data['empresa_codigo'] ?? '', 'Correios Código da Empresa (Contrato)', false);
        $this->set('shipping', 'correios_enabled', $data['enabled'] ?? false, 'Correios Enabled', false);
    }
}
