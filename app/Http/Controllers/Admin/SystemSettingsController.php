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
        $request->validate([
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'sandbox' => 'boolean',
            'enabled' => 'boolean'
        ]);
        
        try {
            $this->settingsService->saveMelhorEnvio([
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
                'sandbox' => $request->sandbox ?? false,
                'enabled' => $request->enabled ?? false
            ]);
            
            return redirect()->route('admin.store-settings.index')
                ->with('success', 'Configurações do Melhor Envio atualizadas com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações do Melhor Envio: ' . $e->getMessage());
            
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
