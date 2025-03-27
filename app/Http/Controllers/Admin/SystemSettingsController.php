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
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'token' => 'required|string|max:100',
            'sandbox' => 'nullable',
            'enabled' => 'nullable'
        ]);
        
        try {
            // Processa os valores booleanos corretamente
            $data = [
                'email' => trim($validated['email']),
                'token' => trim($validated['token']),
                'sandbox' => isset($validated['sandbox']) ? 'true' : 'false',
                'enabled' => isset($validated['enabled']) ? 'true' : 'false'
            ];
            
            $warnings = [];
            
            // Se estamos habilitando o serviço, é melhor testar a conexão primeiro
            if ($data['enabled'] === 'true') {
                try {
                    // Definir a URL base com base no ambiente (sandbox ou produção)
                    $baseUrl = $data['sandbox'] === 'true'
                        ? 'https://ws.sandbox.pagseguro.uol.com.br' 
                        : 'https://ws.pagseguro.uol.com.br';
                    
                    // Testar a conexão consultando uma API básica
                    $client = new \GuzzleHttp\Client();
                    $response = $client->get("{$baseUrl}/v2/sessions?email={$data['email']}&token={$data['token']}", [
                        'timeout' => 10,
                        'http_errors' => false
                    ]);
                    
                    if ($response->getStatusCode() != 200) {
                        $warnings[] = 'O serviço está habilitado, mas o teste de conexão falhou. Verifique as credenciais.';
                        Log::warning('Aviso: PagSeguro habilitado mas o teste de conexão falhou');
                    }
                } catch (\Exception $e) {
                    // Apenas logamos o erro, mas não bloqueamos a ativação
                    $warnings[] = 'O serviço está habilitado, mas o teste de conexão falhou: ' . $e->getMessage();
                    Log::warning('Aviso: Erro ao testar conexão com PagSeguro: ' . $e->getMessage());
                }
            }
            
            // Avisar sobre ambiente de sandbox
            if ($data['sandbox'] === 'true' && $data['enabled'] === 'true') {
                $warnings[] = 'PagSeguro está em modo sandbox. Não serão processados pagamentos reais.';
            }
            
            $this->settingsService->savePagSeguro($data);
            
            Log::info('Configurações do PagSeguro atualizadas com sucesso. Modo sandbox: ' . $data['sandbox'] . ', Habilitado: ' . $data['enabled']);
            
            $response = redirect()->route('admin.store-settings.index')
                ->with('success', 'Configurações do PagSeguro atualizadas com sucesso.');
                
            // Adiciona avisos à sessão, se houver
            if (!empty($warnings)) {
                session()->flash('warnings', $warnings);
            }
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações do PagSeguro: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
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
        $validated = $request->validate([
            'public_key' => 'required|string|max:255',
            'access_token' => 'required|string|max:255',
            'sandbox' => 'nullable',
            'enabled' => 'nullable'
        ]);
        
        try {
            // Processa os valores booleanos corretamente
            $data = [
                'public_key' => trim($validated['public_key']),
                'access_token' => trim($validated['access_token']),
                'sandbox' => isset($validated['sandbox']) ? 'true' : 'false',
                'enabled' => isset($validated['enabled']) ? 'true' : 'false'
            ];
            
            $warnings = [];
            
            // Se estamos habilitando o serviço, é melhor testar a conexão primeiro
            if ($data['enabled'] === 'true') {
                try {
                    // Testar a conexão consultando o endpoint de "payment_methods"
                    $client = new \GuzzleHttp\Client();
                    $response = $client->get('https://api.mercadopago.com/v1/payment_methods', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $data['access_token']
                        ],
                        'timeout' => 10,
                        'http_errors' => false
                    ]);
                    
                    $responseCode = $response->getStatusCode();
                    
                    if ($responseCode != 200) {
                        $responseBody = json_decode($response->getBody()->getContents(), true);
                        $errorMessage = isset($responseBody['message']) ? $responseBody['message'] : 'Erro desconhecido';
                        $warnings[] = 'O serviço está habilitado, mas o teste de conexão falhou. Erro: ' . $errorMessage;
                        Log::warning('Aviso: MercadoPago habilitado mas o teste de conexão falhou. Erro: ' . $errorMessage);
                    }
                    
                    // Verificar formato da public_key
                    $keyPattern = $data['sandbox'] === 'true' 
                        ? '/^TEST-/' 
                        : '/^APP_USR-/';
                        
                    if (!preg_match($keyPattern, $data['public_key'])) {
                        $expectedPrefix = $data['sandbox'] === 'true' ? 'TEST-' : 'APP_USR-';
                        $warnings[] = 'O formato da Public Key parece incorreto para o ambiente selecionado. Era esperado começar com "' . $expectedPrefix . '"';
                        Log::warning('Aviso: O formato da Public Key do MercadoPago pode estar incorreto para o ambiente selecionado');
                    }
                } catch (\Exception $e) {
                    // Apenas logamos o erro, mas não bloqueamos a ativação
                    $warnings[] = 'O serviço está habilitado, mas o teste de conexão falhou: ' . $e->getMessage();
                    Log::warning('Aviso: Erro ao testar conexão com MercadoPago: ' . $e->getMessage());
                }
            }
            
            // Avisar sobre ambiente de sandbox
            if ($data['sandbox'] === 'true' && $data['enabled'] === 'true') {
                $warnings[] = 'MercadoPago está em modo sandbox. Não serão processados pagamentos reais.';
            }
            
            $this->settingsService->saveMercadoPago($data);
            
            Log::info('Configurações do MercadoPago atualizadas com sucesso. Modo sandbox: ' . $data['sandbox'] . ', Habilitado: ' . $data['enabled']);
            
            $response = redirect()->route('admin.store-settings.index')
                ->with('success', 'Configurações do MercadoPago atualizadas com sucesso.');
                
            // Adiciona avisos à sessão, se houver
            if (!empty($warnings)) {
                session()->flash('warnings', $warnings);
            }
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações do MercadoPago: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
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

            return redirect()->route('admin.store-settings.melhorenvio')->with('success', 'Configurações do Melhor Envio atualizadas com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar configurações do Melhor Envio: ' . $e->getMessage());
            return redirect()->route('admin.store-settings.melhorenvio')->with('error', 'Erro ao atualizar configurações: ' . $e->getMessage());
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
                return redirect()->route('admin.store-settings.melhorenvio')
                    ->with('error', 'Autorização falhou. Código de autorização não recebido.');
            }

            $code = $request->code;
            
            // Recuperar as configurações salvas
            $settings = array_filter($this->settingsService->getGroup('shipping'), function($key) {
                return strpos($key, 'melhorenvio_') === 0;
            }, ARRAY_FILTER_USE_KEY);
            
            // Verificar se as credenciais existem
            if (empty($settings['melhorenvio_client_id']) || empty($settings['melhorenvio_client_secret'])) {
                return redirect()->route('admin.store-settings.melhorenvio')
                    ->with('error', 'Client ID e Client Secret devem ser configurados antes da autenticação.');
            }
            
            $clientId = $settings['melhorenvio_client_id'];
            $clientSecret = $settings['melhorenvio_client_secret'];
            $redirectUri = url('/admin/configuracoes-loja/melhor-envio/callback');
            
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
                
                return redirect()->route('admin.store-settings.melhorenvio')
                    ->with('success', 'Autenticação realizada com sucesso! Token gerado e salvo.');
            } else {
                // Erro na obtenção do token
                $errorMessage = $result['error_description'] ?? 'Erro desconhecido ao obter o token de acesso.';
                return redirect()->route('admin.store-settings.melhorenvio')
                    ->with('error', 'Falha na autenticação: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.store-settings.melhorenvio')
                ->with('error', 'Erro durante o processo de autenticação: ' . $e->getMessage());
        }
    }

    /**
     * Show form for mercado envio settings
     */
    
    
    /**
     * Exibir página de configuração dos Correios
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
                try {
                    $email = $request->get('email');
                    $token = $request->get('token');
                    $sandbox = $request->get('sandbox', false);
                    
                    if (empty($email) || empty($token)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Email e token são obrigatórios para testar a conexão.'
                        ]);
                    }
                    
                    // Definir a URL base com base no ambiente (sandbox ou produção)
                    $baseUrl = $sandbox 
                        ? 'https://ws.sandbox.pagseguro.uol.com.br' 
                        : 'https://ws.pagseguro.uol.com.br';
                    
                    // Testar a conexão consultando uma API básica (ex: status do serviço)
                    $client = new \GuzzleHttp\Client();
                    $response = $client->get("{$baseUrl}/v2/sessions?email={$email}&token={$token}", [
                        'timeout' => 10,
                        'http_errors' => false
                    ]);
                    
                    $responseCode = $response->getStatusCode();
                    $responseBody = $response->getBody()->getContents();
                    
                    // Verifica se a resposta é bem-sucedida (código 200 e contém um ID de sessão)
                    if ($responseCode == 200 && strpos($responseBody, '<id>') !== false) {
                        $result = [
                            'success' => true,
                            'message' => 'Conexão com PagSeguro estabelecida com sucesso.'
                        ];
                    } else {
                        // Tenta extrair a mensagem de erro
                        $errorMessage = 'Falha na conexão com o PagSeguro.';
                        if (preg_match('/<message>(.*?)<\/message>/s', $responseBody, $matches)) {
                            $errorMessage .= ' Erro: ' . $matches[1];
                        }
                        
                        $result = [
                            'success' => false,
                            'message' => $errorMessage
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error('Erro ao testar conexão com PagSeguro: ' . $e->getMessage());
                    $result = [
                        'success' => false,
                        'message' => 'Erro ao testar conexão: ' . $e->getMessage()
                    ];
                }
                break;
                
            case 'mercadopago':
                try {
                    $publicKey = $request->get('public_key');
                    $accessToken = $request->get('access_token');
                    
                    if (empty($publicKey) || empty($accessToken)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Public Key e Access Token são obrigatórios para testar a conexão.'
                        ]);
                    }
                    
                    // Testar a conexão consultando o endpoint de "payment_methods" que é uma API pública
                    $client = new \GuzzleHttp\Client();
                    $response = $client->get('https://api.mercadopago.com/v1/payment_methods', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $accessToken
                        ],
                        'timeout' => 10,
                        'http_errors' => false
                    ]);
                    
                    $responseCode = $response->getStatusCode();
                    $responseBody = json_decode($response->getBody()->getContents(), true);
                    
                    if ($responseCode == 200 && is_array($responseBody)) {
                        // Tenta validar a public key usando o SDK do MercadoPago
                        // Isso geralmente é feito no frontend, mas verificamos se a chave 
                        // possui o formato correto
                        if (preg_match('/^(TEST-|APP_USR-)[a-zA-Z0-9-]+$/', $publicKey)) {
                            $result = [
                                'success' => true,
                                'message' => 'Conexão com MercadoPago estabelecida com sucesso.'
                            ];
                        } else {
                            $result = [
                                'success' => false,
                                'message' => 'Access Token válido, mas Public Key possui formato inválido.'
                            ];
                        }
                    } else {
                        $errorMessage = 'Falha na conexão com o MercadoPago.';
                        if (isset($responseBody['message'])) {
                            $errorMessage .= ' Erro: ' . $responseBody['message'];
                        }
                        
                        $result = [
                            'success' => false,
                            'message' => $errorMessage
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error('Erro ao testar conexão com MercadoPago: ' . $e->getMessage());
                    $result = [
                        'success' => false,
                        'message' => 'Erro ao testar conexão: ' . $e->getMessage()
                    ];
                }
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
