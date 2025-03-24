<?php

use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Pdv\PainelController;
use App\Http\Controllers\Site\AboutController;
use App\Http\Controllers\Site\PlaylistController;
use App\Http\Controllers\Site\RecommendationController;
use App\Http\Controllers\Site\WantlistController;
use App\Http\Controllers\Site\AddressController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\SearchController;
use App\Http\Controllers\Site\VinylWebController;
use App\Http\Controllers\Site\VinylDetailsController;
use App\Http\Controllers\Site\WishlistController;
use App\Http\Controllers\Site\ChartDjsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Site\NavbarController;
use App\Http\Controllers\Auth\SocialiteController;
use Laravel\Socialite\Facades\Socialite;

// Rotas para o Navbar
Route::prefix('navbar')->group(function () {
    Route::get('/data', [NavbarController::class, 'getNavbarData']);
    Route::get('/cart-preview', [NavbarController::class, 'getCartPreview']);
});

// Rotas para Wishlist
Route::middleware('auth')->group(function () {
    Route::post('/wishlist/toggle-favorite', [WishlistController::class, 'toggleFavorite'])->name('wishlist.toggle-favorite');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
});

require __DIR__.'/admin.php';
// require __DIR__.'/oauth.php'; // Removido para evitar conflitos

Route::middleware(['auth', 'verified', 'rolemanager:resale'])->group(function () {
    Route::prefix('pdv')->group(function () {
        Route::get('/', [PainelController::class, 'index'])->name('pdv.dashboard');
    });
});

// Site Routes
Route::get('/', [HomeController::class, 'index'])->name('site.home');
Route::get('/discos', [VinylWebController::class, 'index'])->name('site.vinyls.index');
Route::get('/discos/promocoes', [VinylWebController::class, 'promotions'])->name('site.vinyls.promotions');
Route::get('/busca', [SearchController::class, 'index'])->name('site.search');
// Route::get('/djcharts', [ChartDjsController::class, 'index'])->name('site.djcharts.index');
// Route::get('/djcharts/{dj:slug}', [ChartDjsController::class, 'show'])->name('site.djcharts.show');
Route::get('/equipamentos', [EquipmentController::class, 'index'])->name('site.equipments.index');
Route::get('/equipamentos/{slug}', [EquipmentController::class, 'show'])->name('site.equipments.show');
Route::get('/sobre-a-loja', [AboutController::class, 'index'])->name('site.about');

Route::get('/discos/categoria/{slug}', [VinylWebController::class, 'byCategory'])->name('vinyls.byCategory');

// Playlist Routes
Route::get('/playlists', [PlaylistController::class, 'index'])->name('site.playlists.index');
Route::get('/playlists/{slug}', [PlaylistController::class, 'show'])->name('site.playlists.show');
Route::get('/playlists/{slug}/tracks', [PlaylistController::class, 'getPlaylistTracks'])->name('site.playlists.tracks');

// Rota genérica para detalhes de vinil - deve vir por último para não conflitar com outras rotas
Route::get('/{artistSlug}/{titleSlug}', [VinylDetailsController::class, 'show'])->name('site.vinyl.show');

Route::post('/address/store', [AddressController::class, 'store'])->name('address.store');

// Rotas de autenticação com Google movidas para auth.php

Route::get('/test-mercadopago', function() {
    $mercadoPagoService = app(App\Services\MercadoPagoService::class);
    
    // Verifica se o serviço está configurado corretamente
    try {
        $publicKey = $mercadoPagoService->getPublicKey();
        
        return view('test-mercadopago', [
            'publicKey' => $publicKey,
            'message' => 'SDK do MercadoPago inicializado com sucesso!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erro ao inicializar MercadoPago: ' . $e->getMessage()
        ], 500);
    }
});

// Rota para testar criação de preferência
Route::get('/test-payment', function() {
    $mercadoPagoService = app(App\Services\MercadoPagoService::class);
    
    try {
        // Buscar um usuário válido com endereço
        $user = App\Models\User::whereHas('addresses')->first();
        
        if (!$user) {
            return response()->json([
                'error' => 'Não foi encontrado um usuário com endereço cadastrado. Crie um usuário e endereço primeiro.'
            ], 400);
        }
        
        // Criar um pedido de teste com endereço válido
        $order = new App\Models\Order();
        $order->id = time(); // ID temporário para teste
        $order->total = 99.99;
        $order->user_id = $user->id;
        $order->user = $user;
        
        // Definir o endereço de envio
        $shippingAddress = $user->addresses()->first();
        $order->shippingAddress = $shippingAddress;
        
        // Adicionar alguns itens ao pedido
        $order->items = collect([
            (object)[
                'id' => 1,
                'quantity' => 1,
                'price' => 99.99,
                'product' => (object)[
                    'name' => 'Produto de Teste',
                    'description' => 'Descrição do produto de teste'
                ]
            ]
        ]);
        
        // Criar a preferência de pagamento
        $result = $mercadoPagoService->createPreference($order);
        
        // Verificar se houve erro
        if (!$result['success']) {
            return response()->json([
                'error' => $result['message']
            ], 500);
        }
        
        return view('test-payment', [
            'result' => $result,
            'publicKey' => $mercadoPagoService->getPublicKey(),
            'preferenceId' => $result['preference_id']
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erro ao processar teste: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

Route::get('/privacy-policy', function () {
    return view('site.privacy-policy');
})->name('privacy.policy');

Route::get('/terms-of-service', function () {
    return view('site.terms-of-service');
})->name('terms.service');

// Rota de diagnóstico para Google OAuth (desabilitar em produção após testes)
Route::get('/debug-oauth', function () {
    $systemSettings = app(\App\Services\SystemSettingsService::class);
    
    $settings = [
        'enabled' => (bool)$systemSettings->get('oauth', 'google_enabled', false),
        'client_id' => $systemSettings->get('oauth', 'google_client_id', ''),
        'has_secret' => !empty($systemSettings->get('oauth', 'google_client_secret', '')),
        'redirect' => $systemSettings->get('oauth', 'google_redirect', 'não configurado'),
        'raw_enabled_value' => $systemSettings->get('oauth', 'google_enabled', 'valor não definido'),
        'auth_url' => url('/auth/google'),
        'callback_url' => url('/auth/google/callback'),
        'actual_routes' => [
            'auth.google' => route('auth.google'),
            'auth.google.callback' => route('auth.google.callback')
        ]
    ];
    
    return response()->json($settings);
});

require __DIR__.'/auth.php';
require __DIR__.'/users.php';
require __DIR__.'/checkout.php';
require __DIR__.'/cart.php';
