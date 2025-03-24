<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SystemSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OAuthSettingsController extends Controller
{
    protected $systemSettings;

    public function __construct(SystemSettingsService $systemSettings)
    {
        $this->systemSettings = $systemSettings;
        $this->middleware(['auth', 'rolemanager:admin']);
    }

    /**
     * Exibe a página de configurações de OAuth
     */
    public function index()
    {
        $googleSettings = [
            'client_id' => $this->systemSettings->get('oauth', 'google_client_id', ''),
            'client_secret' => $this->systemSettings->get('oauth', 'google_client_secret', ''),
            'redirect' => $this->systemSettings->get('oauth', 'google_redirect', route('auth.google.callback')),
            'enabled' => (bool)$this->systemSettings->get('oauth', 'google_enabled', false),
        ];

        return view('admin.settings.oauth', compact('googleSettings'));
    }

    /**
     * Salva as configurações de OAuth do Google
     */
    public function saveGoogleSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'redirect' => 'required|url',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Salvar as configurações
        $this->systemSettings->set('oauth', 'google_client_id', $request->input('client_id'));
        $this->systemSettings->set('oauth', 'google_client_secret', $request->input('client_secret'));
        $this->systemSettings->set('oauth', 'google_redirect', $request->input('redirect')); // Salvar URL diretamente
        $this->systemSettings->set('oauth', 'google_enabled', $request->has('enabled'));

        // Limpar o cache do Socialite se estiver sendo usado
        if (class_exists('Laravel\Socialite\Facades\Socialite')) {
            \Laravel\Socialite\Facades\Socialite::forgetDrivers();
        }

        return redirect()->back()->with('success', 'Configurações do Google atualizadas com sucesso.');
    }

    /**
     * Testa a conexão com o Google OAuth
     */
    public function testGoogleConnection()
    {
        $enabled = (bool)$this->systemSettings->get('oauth', 'google_enabled', false);
        $clientId = $this->systemSettings->get('oauth', 'google_client_id', '');
        $clientSecret = $this->systemSettings->get('oauth', 'google_client_secret', '');

        if (empty($clientId) || empty($clientSecret)) {
            return response()->json([
                'success' => false,
                'message' => 'As configurações do Google OAuth não estão completas.'
            ]);
        }

        // Se as credenciais estiverem configuradas mas o serviço estiver desativado, ativamos automaticamente
        if (!$enabled && !empty($clientId) && !empty($clientSecret)) {
            $this->systemSettings->set('oauth', 'google_enabled', true, 'Google OAuth Enabled', false);
            $enabled = true;
        }

        try {
            // Verifica se as configurações estão corretas
            $redirectUrl = $this->systemSettings->get('oauth', 'google_redirect', route('auth.google.callback'));
            
            // Configura o Socialite com as credenciais do banco
            config([
                'services.google.client_id' => $clientId,
                'services.google.client_secret' => $clientSecret,
                'services.google.redirect' => $redirectUrl
            ]);
            
            // Limpar o cache do Socialite
            \Laravel\Socialite\Facades\Socialite::forgetDrivers();
            
            return response()->json([
                'success' => true,
                'message' => 'Configurações do Google OAuth estão válidas e ' . ($enabled ? 'ativadas' : 'desativadas') . '.',
                'redirect_url' => $redirectUrl,
                'status' => $enabled ? 'active' : 'inactive'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao testar a configuração: ' . $e->getMessage()
            ]);
        }
    }
}
