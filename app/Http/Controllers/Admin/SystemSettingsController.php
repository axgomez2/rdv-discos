<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SystemSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SystemSettingsController extends Controller
{
    protected $settingsService;
    
    public function __construct(SystemSettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }
    
    /**
     * Mostra a página principal de configurações da loja
     */
    public function index()
    {
        // Carrega configurações de todos os grupos
        $settings = [
            ...$this->settingsService->getGroup('auth'),
            ...$this->settingsService->getGroup('payment'),
            ...$this->settingsService->getGroup('shipping')
        ];
        
        return view('admin.settings.store-index', compact('settings'));
    }
    
    /**
     * Mostrar todas as configurações
     */
    public function settingsIndex()
    {
        // Redirecionar para a nova rota de configurações da loja
        return redirect()->route('admin.store-settings.index');
    }
    
    /**
     * Mostrar formulário para configurações do Google OAuth
     */
    public function editGoogleOauth()
    {
        $settings = $this->settingsService->getGroup('oauth');
        
        return view('admin.settings.google-oauth', compact('settings'));
    }
    
    /**
     * Atualizar configurações do Google OAuth
     */
    public function updateGoogleOauth(Request $request)
    {
        $request->validate([
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'redirect' => 'required|string|url',
            'enabled' => 'boolean'
        ]);
        
        try {
            $this->settingsService->saveGoogleOauth($request->all());
            
            return redirect()->route('admin.store-settings.index')
                ->with('success', 'Configurações do Google OAuth atualizadas com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações do Google OAuth: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao salvar configurações: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Mostrar formulário para configurações do PagSeguro
     */
    public function editPagSeguro()
    {
        $settings = array_filter($this->settingsService->getGroup('payment'), function($key) {
            return strpos($key, 'pagseguro_') === 0;
        }, ARRAY_FILTER_USE_KEY);
        
        return view('admin.settings.pagseguro', compact('settings'));
    }
    
    /**
     * Atualizar configurações do PagSeguro
     */
    public function updatePagSeguro(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'sandbox' => 'boolean',
            'enabled' => 'boolean'
        ]);
        
        try {
            $this->settingsService->savePagSeguro($request->all());
            
            return redirect()->route('admin.store-settings.index')
                ->with('success', 'Configurações do PagSeguro atualizadas com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações do PagSeguro: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao salvar configurações: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Mostrar formulário para configurações do MercadoPago
     */
    public function editMercadoPago()
    {
        $settings = array_filter($this->settingsService->getGroup('payment'), function($key) {
            return strpos($key, 'mercadopago_') === 0;
        }, ARRAY_FILTER_USE_KEY);
        
        return view('admin.settings.mercadopago', compact('settings'));
    }
    
    /**
     * Atualizar configurações do MercadoPago
     */
    public function updateMercadoPago(Request $request)
    {
        $request->validate([
            'public_key' => 'required|string',
            'access_token' => 'required|string',
            'sandbox' => 'boolean',
            'enabled' => 'boolean'
        ]);
        
        try {
            $this->settingsService->saveMercadoPago($request->all());
            
            return redirect()->route('admin.store-settings.index')
                ->with('success', 'Configurações do MercadoPago atualizadas com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações do MercadoPago: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao salvar configurações: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Mostrar formulário para configurações do Melhor Envio
     */
    public function editMelhorEnvio()
    {
        $settings = array_filter($this->settingsService->getGroup('shipping'), function($key) {
            return strpos($key, 'melhorenvio_') === 0;
        }, ARRAY_FILTER_USE_KEY);
        
        return view('admin.settings.melhorenvio', compact('settings'));
    }
    
    /**
     * Atualizar configurações do Melhor Envio
     */
    public function updateMelhorEnvio(Request $request)
    {
        try {
            $validated = $request->validate([
                'client_id' => 'required|string',
                'client_secret' => 'required|string',
                'sandbox' => 'nullable',
                'enabled' => 'nullable',
                'auto_approve' => 'nullable',
                'insurance' => 'nullable',
                'receipt' => 'nullable',
                'own_hand' => 'nullable',
                'timeout' => 'nullable|integer|min:10|max:60',
                'additional_days' => 'nullable|integer|min:0|max:30',
                'service_pac' => 'nullable',
                'service_sedex' => 'nullable',
                'service_mini' => 'nullable',
                'service_jadlog' => 'nullable',
                'service_azul' => 'nullable',
                'service_latam' => 'nullable',
                // Dados do remetente
                'from_name' => 'required|string|max:255',
                'from_document' => 'required|string|max:20',
                'from_phone' => 'required|string|max:20',
                'from_email' => 'required|email|max:255',
                'from_postal_code' => 'required|string|max:10',
                'from_state' => 'required|string|max:2',
                'from_city' => 'required|string|max:100',
                'from_address' => 'required|string|max:255',
                'from_number' => 'required|string|max:20',
                'from_complement' => 'nullable|string|max:255',
                'from_district' => 'required|string|max:100',
            ]);

            $data = [
                'client_id' => $validated['client_id'],
                'client_secret' => $validated['client_secret'],
                'sandbox' => isset($validated['sandbox']) ? 'true' : 'false',
                'enabled' => isset($validated['enabled']) ? 'true' : 'false',
                'auto_approve' => isset($validated['auto_approve']) ? 'true' : 'false',
                'insurance' => isset($validated['insurance']) ? 'true' : 'false',
                'receipt' => isset($validated['receipt']) ? 'true' : 'false',
                'own_hand' => isset($validated['own_hand']) ? 'true' : 'false',
                'timeout' => $validated['timeout'] ?? '30',
                'additional_days' => $validated['additional_days'] ?? '0',
                'service_pac' => isset($validated['service_pac']) ? 'true' : 'false',
                'service_sedex' => isset($validated['service_sedex']) ? 'true' : 'false',
                'service_mini' => isset($validated['service_mini']) ? 'true' : 'false',
                'service_jadlog' => isset($validated['service_jadlog']) ? 'true' : 'false',
                'service_azul' => isset($validated['service_azul']) ? 'true' : 'false',
                'service_latam' => isset($validated['service_latam']) ? 'true' : 'false',
                // Dados do remetente
                'from_name' => $validated['from_name'],
                'from_document' => $validated['from_document'],
                'from_phone' => $validated['from_phone'],
                'from_email' => $validated['from_email'],
                'from_postal_code' => $validated['from_postal_code'],
                'from_state' => $validated['from_state'],
                'from_city' => $validated['from_city'],
                'from_address' => $validated['from_address'],
                'from_number' => $validated['from_number'],
                'from_complement' => $validated['from_complement'] ?? '',
                'from_district' => $validated['from_district'],
            ];

            // Preservar o token se já existir
            $settings = array_filter($this->settingsService->getGroup('shipping'), function($key) {
                return strpos($key, 'melhorenvio_') === 0;
            }, ARRAY_FILTER_USE_KEY);
            
            if (!empty($settings['melhorenvio_token'])) {
                $data['token'] = $settings['melhorenvio_token'];
            }
            
            if (!empty($settings['melhorenvio_refresh_token'])) {
                $data['refresh_token'] = $settings['melhorenvio_refresh_token'];
            }
            
            if (!empty($settings['melhorenvio_token_expires_at'])) {
                $data['token_expires_at'] = $settings['melhorenvio_token_expires_at'];
            }

            $this->settingsService->saveMelhorEnvio($data);

            return redirect()->route('admin.settings.melhorenvio')->with('success', 'Configurações do Melhor Envio atualizadas com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar configurações do Melhor Envio: ' . $e->getMessage());
            return redirect()->route('admin.settings.melhorenvio')->with('error', 'Erro ao atualizar configurações: ' . $e->getMessage());
        }
    }
    
    /**
     * Processa o callback de autenticação do Melhor Envio
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function melhorEnvioCallback(Request $request)
    {
        try {
            // Verificar se o código de autorização foi recebido
            if (!$request->has('code')) {
                return redirect()->route('admin.settings.melhorenvio')
                    ->with('error', 'Autorização falhou. Código de autorização não recebido.');
            }

            $code = $request->code;
            
            // Recuperar as configurações salvas
            $settings = array_filter($this->settingsService->getGroup('shipping'), function($key) {
                return strpos($key, 'melhorenvio_') === 0;
            }, ARRAY_FILTER_USE_KEY);
            
            // Verificar se as credenciais existem
            if (empty($settings['melhorenvio_client_id']) || empty($settings['melhorenvio_client_secret'])) {
                return redirect()->route('admin.settings.melhorenvio')
                    ->with('error', 'Client ID e Client Secret devem ser configurados antes da autenticação.');
            }
            
            $clientId = $settings['melhorenvio_client_id'];
            $clientSecret = $settings['melhorenvio_client_secret'];
            $redirectUri = url('/admin/settings/melhorenvio/callback');
            
            // Determinar a URL da API com base no modo sandbox
            $apiUrl = isset($settings['melhorenvio_sandbox']) && $settings['melhorenvio_sandbox'] == 'true'
                ? 'https://sandbox.melhorenvio.com.br'
                : 'https://melhorenvio.com.br';
            
            // Realizar a requisição para obter o token
            $client = new \GuzzleHttp\Client();
            $response = $client->post($apiUrl . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri' => $redirectUri,
                    'code' => $code,
                ],
                'http_errors' => false,
            ]);
            
            $result = json_decode($response->getBody()->getContents(), true);
            
            // Verificar se o token foi obtido com sucesso
            if (isset($result['access_token'])) {
                // Salvar o token nas configurações
                $data = $settings;
                $data['token'] = $result['access_token'];
                $data['refresh_token'] = $result['refresh_token'] ?? null;
                $data['token_expires_at'] = now()->addSeconds($result['expires_in'] ?? 3600)->format('Y-m-d H:i:s');
                
                $this->settingsService->saveMelhorEnvio($data);
                
                return redirect()->route('admin.settings.melhorenvio')
                    ->with('success', 'Autenticação realizada com sucesso! Token gerado e salvo.');
            } else {
                // Erro na obtenção do token
                $errorMessage = $result['error_description'] ?? 'Erro desconhecido ao obter o token de acesso.';
                return redirect()->route('admin.settings.melhorenvio')
                    ->with('error', 'Falha na autenticação: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.melhorenvio')
                ->with('error', 'Erro durante o processo de autenticação: ' . $e->getMessage());
        }
    }

    /**
     * Show form for mercado envio settings
     */
    public function editMercadoEnvio()
    {
        $settings = array_filter($this->settingsService->getGroup('shipping'), function($key) {
            return strpos($key, 'mercadoenvio_') === 0;
        }, ARRAY_FILTER_USE_KEY);
        
        return view('admin.settings.mercadoenvio', compact('settings'));
    }
    
    /**
     * Atualizar configurações do Mercado Envio
     */
    public function updateMercadoEnvio(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'secret_key' => 'required|string',
            'seller_id' => 'required|string',
            'sandbox' => 'boolean',
            'enabled' => 'boolean'
        ]);
        
        try {
            $this->settingsService->saveMercadoEnvio([
                'api_key' => $request->api_key,
                'secret_key' => $request->secret_key,
                'seller_id' => $request->seller_id,
                'sandbox' => $request->sandbox ?? false,
                'enabled' => $request->enabled ?? false
            ]);
            
            return redirect()->route('admin.store-settings.index')
                ->with('success', 'Configurações do Mercado Envio atualizadas com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações do Mercado Envio: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao salvar configurações: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Mostrar formulário para configurações dos Correios
     */
    public function editCorreios()
    {
        $settings = array_filter($this->settingsService->getGroup('shipping'), function($key) {
            return strpos($key, 'correios_') === 0;
        }, ARRAY_FILTER_USE_KEY);
        
        return view('admin.settings.correios', compact('settings'));
    }

    /**
     * Atualizar configurações dos Correios
     */
    public function updateCorreios(Request $request)
    {
        $request->validate([
            'usuario' => 'required|string',
            'senha' => 'required|string',
            'empresa_codigo' => 'nullable|string',
            'enabled' => 'boolean'
        ]);
        
        try {
            $this->settingsService->saveCorreios([
                'usuario' => $request->usuario,
                'senha' => $request->senha,
                'empresa_codigo' => $request->empresa_codigo,
                'enabled' => $request->enabled ?? false
            ]);
            
            return redirect()->route('admin.store-settings.index')
                ->with('success', 'Configurações dos Correios atualizadas com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações dos Correios: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao salvar configurações: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Testar conexão com serviços
     */
    public function testConnection(Request $request)
    {
        $service = $request->get('service');
        $result = ['success' => false, 'message' => 'Serviço não suportado'];
        
        switch ($service) {
            case 'google':
                // Implementar teste de conexão com Google OAuth
                break;
                
            case 'pagseguro':
                // Implementar teste de conexão com PagSeguro
                break;
                
            case 'mercadopago':
                // Implementar teste de conexão com MercadoPago
                break;
                
            case 'melhorenvio':
                // Implementar teste de conexão com Melhor Envio
                break;
                
            case 'correios':
                // Implementar teste de conexão com Correios
                break;
        }
        
        return response()->json($result);
    }
}
