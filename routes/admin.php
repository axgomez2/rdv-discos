<?php

use App\Http\Controllers\Admin\CatStyleShopController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlaylistController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SubscriptionPackageController;
use App\Http\Controllers\Admin\TrackController;
use App\Http\Controllers\Admin\VinylController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\VinylImageController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\YouTubeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'rolemanager:admin'])->group(function () {
    //dashboard
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Vinyls
    Route::get('/admin/discos', [VinylController::class, 'index'])->name('admin.vinyls.index');
    Route::get('/admin/discos/adicionar', [VinylController::class, 'create'])->name('admin.vinyls.create');
    Route::post('/admin/discos/salvar', [VinylController::class, 'store'])->name('admin.vinyls.store');
    Route::get('/admin/disco/{id}', [VinylController::class, 'show'])->name('admin.vinyls.show');
    Route::get('/admin/disco/{id}/edit', [VinylController::class, 'edit'])->name('admin.vinyls.edit');
    Route::put('/admin/disco/{id}', [VinylController::class, 'update'])->name('admin.vinyls.update');
    Route::delete('/admin/disco/{id}', [VinylController::class, 'destroy'])->name('admin.vinyls.destroy');

    Route::get('/admin/disco/{id}/completar', [VinylController::class, 'complete'])->name('admin.vinyls.complete');
    Route::post('/admin/disco/{id}/completar', [VinylController::class, 'storeComplete'])->name('admin.vinyl.storeComplete');

    Route::get('/admin/disco/{id}/images', [VinylImageController::class, 'index'])->name('admin.vinyl.images');
    Route::post('/admin/disco/{id}/images', [VinylImageController::class, 'store'])->name('admin.vinyl.images.store');
    Route::delete('/admin/disco/{id}/images/{imageId}', [VinylImageController::class, 'destroy'])->name('admin.vinyl.images.destroy');
    Route::post('/admin/disco/update-field', [VinylController::class, 'updateField'])->name('admin.vinyls.updateField');

    Route::post('/admin/disco/{id}/fetch-discogs-image', [VinylController::class, 'fetchDiscogsImage'])->name('admin.vinyls.fetch-discogs-image');
    Route::post('/admin/disco/{id}/upload-image', [VinylController::class, 'uploadImage'])->name('admin.vinyls.upload-image');
    Route::delete('/admin/disco/{id}/remove-image', [VinylController::class, 'removeImage'])->name('admin.vinyls.remove-image');

    //faixas
    Route::get('/admin/disco/{id}/edit-tracks', [TrackController::class, 'editTracks'])->name('admin.vinyls.edit-tracks');
    Route::put('/admin/disco/{id}/update-tracks', [TrackController::class, 'updateTracks'])->name('admin.vinyls.update-tracks');
    Route::post('/admin/youtube/search', [YouTubeController::class, 'search'])->name('youtube.search');

    // equipments route
    Route::get('/admin/equipamentos', [EquipmentController::class, 'index'])->name('admin.equipments.index');
    Route::get('/admin/equipamentos/adicionar', [EquipmentController::class, 'create'])->name('admin.equipments.create');
    Route::post('/admin/equipamentos', [EquipmentController::class, 'store'])->name('admin.equipments.store');
    Route::get('/admin/equipamentos/{id}/edit', [EquipmentController::class, 'edit'])->name('admin.equipments.edit');
    Route::put('/admin/equipamentos/{id}', [EquipmentController::class, 'update'])->name('admin.equipments.update');
    Route::delete('/admin/equipamentos/{id}', [EquipmentController::class, 'destroy'])->name('admin.equipments.destroy');
    Route::delete('/admin/equipamentos/media/{mediaId}', [EquipmentController::class, 'deleteMedia'])->name('admin.equipments.deleteMedia');
    Route::post('/admin/equipamentos/gerar-descricao', [EquipmentController::class, 'generateDescription'])->name('admin.equipments.generateDescription');

    // Playlist Management
    Route::prefix('admin')->group(function () {
        // Rota de busca de discos (precisa vir antes das rotas com parâmetros)
        Route::get('playlists/search-tracks', [\App\Http\Controllers\Admin\PlaylistController::class, 'searchVinyls'])
            ->name('admin.playlists.search-tracks');

        // Rotas de CRUD padrão
        Route::resource('playlists', \App\Http\Controllers\Admin\PlaylistController::class)->names([
            'index' => 'admin.playlists.index',
            'create' => 'admin.playlists.create',
            'store' => 'admin.playlists.store',
            'edit' => 'admin.playlists.edit',
            'update' => 'admin.playlists.update',
            'destroy' => 'admin.playlists.destroy',
        ]);
    });

    // cotas de configurações
    Route::prefix('admin')->group(function () {
        // Configurações de autenticação OAuth
        Route::get('/settings/oauth', [\App\Http\Controllers\Admin\OAuthSettingsController::class, 'index'])
            ->name('admin.settings.oauth');
        Route::post('/settings/oauth/google', [\App\Http\Controllers\Admin\OAuthSettingsController::class, 'saveGoogleSettings'])
            ->name('admin.settings.oauth.google.save');
        Route::get('/settings/oauth/google/test', [\App\Http\Controllers\Admin\OAuthSettingsController::class, 'testGoogleConnection'])
            ->name('admin.settings.oauth.google.test');
            
        // Configurações de Produtos
        Route::get('/configuracoes', [SettingsController::class, 'index'])->name('admin.settings.index');
        
        // Categorias internas
        Route::get('categorias-estilo', [CatStyleShopController::class, 'index'])->name('admin.cat-style-shop.index');
        Route::get('categorias-estilo/criar', [CatStyleShopController::class, 'create'])->name('admin.cat-style-shop.create');
        Route::post('categorias-estilo', [CatStyleShopController::class, 'store'])->name('admin.cat-style-shop.store');
        Route::get('categorias-estilo/{id}/editar', [CatStyleShopController::class, 'edit'])->name('admin.cat-style-shop.edit');
        Route::put('categorias-estilo/{id}', [CatStyleShopController::class, 'update'])->name('admin.cat-style-shop.update');
        Route::delete('categorias-estilo/{id}', [CatStyleShopController::class, 'destroy'])->name('admin.cat-style-shop.destroy');

        // Configurações da Loja (Novo menu separado)
        Route::get('/configuracoes-loja', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'index'])
            ->name('admin.store-settings.index');
            
        // Antiga página de configurações da loja (agora redirecionará para a página correta)
        Route::get('/configuracoes/sistema', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'settingsIndex'])
            ->name('admin.settings.settingsIndex');
        
        // Configurações de serviços externos
        Route::prefix('configuracoes-loja')->group(function () {
            // Google OAuth
            Route::get('/google-oauth', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'editGoogleOauth'])
                ->name('admin.settings.google-oauth');
            Route::post('/google-oauth', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'updateGoogleOauth'])
                ->name('admin.settings.google-oauth.update');
                
            // PagSeguro
            Route::get('/pagseguro', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'editPagSeguro'])
                ->name('admin.settings.pagseguro');
            Route::post('/pagseguro', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'updatePagSeguro'])
                ->name('admin.settings.pagseguro.update');
                
            // MercadoPago
            Route::get('/mercadopago', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'editMercadoPago'])
                ->name('admin.settings.mercadopago');
            Route::post('/mercadopago', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'updateMercadoPago'])
                ->name('admin.settings.mercadopago.update');
                
            // Melhor Envio
            Route::get('/melhor-envio', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'editMelhorEnvio'])
                ->name('admin.settings.melhorenvio');
            Route::post('/melhor-envio', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'updateMelhorEnvio'])
                ->name('admin.settings.melhorenvio.update');
                
            // Correios
            Route::get('/correios', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'editCorreios'])
                ->name('admin.settings.correios');
            Route::post('/correios', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'updateCorreios'])
                ->name('admin.settings.correios.update');
                
            // Testar conexão com serviços
            Route::post('/test-connection', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'testConnection'])
                ->name('admin.settings.test-connection');
        });
        
        // Weight routes
        Route::post('settings/weights', [SettingsController::class, 'storeWeight'])->name('admin.settings.storeWeight');
        Route::put('settings/weights/{weight}', [SettingsController::class, 'updateWeight'])->name('admin.settings.updateWeight');
        Route::delete('settings/weights/{weight}', [SettingsController::class, 'deleteWeight'])->name('admin.settings.deleteWeight');

        // Dimension routes
        Route::post('/settings/dimensions', [SettingsController::class, 'storeDimension'])->name('admin.settings.storeDimension');
        Route::put('/settings/dimensions/{dimension}', [SettingsController::class, 'updateDimension'])->name('admin.settings.updateDimension');
        Route::delete('/settings/dimensions/{dimension}', [SettingsController::class, 'deleteDimension'])->name('admin.settings.deleteDimension');

        // Brand routes
        Route::post('/settings/brands', [SettingsController::class, 'storeBrand'])->name('admin.settings.storeBrand');
        Route::put('/settings/brands/{brand}', [SettingsController::class, 'updateBrand'])->name('admin.settings.updateBrand');
        Route::delete('/settings/brands/{brand}', [SettingsController::class, 'deleteBrand'])->name('admin.settings.deleteBrand');

        // Equipment Category routes
        Route::post('/settings/equipment-categories', [SettingsController::class, 'storeEquipmentCategory'])->name('admin.settings.storeEquipmentCategory');
        Route::put('/settings/equipment-categories/{equipmentCategory}', [SettingsController::class, 'updateEquipmentCategory'])->name('admin.settings.updateEquipmentCategory');
        Route::delete('/settings/equipment-categories/{equipmentCategory}', [SettingsController::class, 'deleteEquipmentCategory'])->name('admin.settings.deleteEquipmentCategory');

        // Customer routes
        Route::get('/clientes', [CustomerController::class, 'index'])->name('admin.customers.index');
        Route::get('/cliente/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');
        Route::get('/cliente/{customer}/editar', [CustomerController::class, 'edit'])->name('admin.customers.edit');
        Route::put('/cliente/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');

         // Shipping Management
        Route::get('/shipping', [ShippingController::class, 'index'])->name('admin.shipping.index');
        Route::post('/shipping/{order}/generate-label', [ShippingController::class, 'generateLabel'])->name('admin.shipping.generate-label');
        Route::get('/shipping/{order}/print-label', [ShippingController::class, 'printLabel'])->name('admin.shipping.print-label');
        Route::get('/shipping/{order}/track', [ShippingController::class, 'trackShipment'])->name('admin.shipping.track');

        // Subscription Package Management
        Route::get('subscription-packages', [SubscriptionPackageController::class, 'index'])->name('admin.subscription-packages.index');
        Route::get('subscription-packages/create', [SubscriptionPackageController::class, 'create'])->name('admin.subscription-packages.create');
        Route::post('subscription-packages', [SubscriptionPackageController::class, 'store'])->name('admin.subscription-packages.store');
        Route::get('subscription-packages/{package}/edit', [SubscriptionPackageController::class, 'edit'])->name('admin.subscription-packages.edit');
        Route::put('subscription-packages/{package}', [SubscriptionPackageController::class, 'update'])->name('admin.subscription-packages.update');
        Route::delete('subscription-packages/{package}', [SubscriptionPackageController::class, 'destroy'])->name('admin.subscription-packages.destroy');
        Route::patch('subscription-packages/{package}/toggle-status', [SubscriptionPackageController::class, 'toggleStatus'])
            ->name('admin.subscription-packages.toggle-status');

        // Subscription Management
        Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('admin.subscriptions.index');
        Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('admin.subscriptions.show');
        Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('admin.subscriptions.cancel');
        Route::post('subscriptions/{subscription}/create-shipment', [SubscriptionController::class, 'createShipment'])->name('admin.subscriptions.create-shipment');
        Route::post('subscriptions/{subscription}/shipments/{shipment}/generate-label', [SubscriptionController::class, 'generateShippingLabel'])->name('admin.subscriptions.generate-label');
    });
});
