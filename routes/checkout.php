<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\CheckoutController;
use App\Http\Controllers\Site\PaymentController;
use App\Http\Controllers\Site\PaymentNotificationController;

Route::middleware(['auth'])->prefix('checkout')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('site.checkout.index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('site.checkout.process');
    
    // Rotas de retorno do MercadoPago
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('site.checkout.success');
    Route::get('/failure/{order}', [CheckoutController::class, 'failure'])->name('site.checkout.failure');
    Route::get('/pending/{order}', [CheckoutController::class, 'pending'])->name('site.checkout.pending');
});

// Rotas de Pagamento
Route::middleware(['auth'])->prefix('pagamentos')->group(function () {
    Route::get('/boleto/{order}', [PaymentController::class, 'showBoletoPage'])->name('site.payments.boleto');
    Route::get('/pix/{order}', [PaymentController::class, 'showPixPage'])->name('site.payments.pix');
});

// Rota de webhook do Mercado Pago (não requer autenticação)
Route::post('/mercadopago/webhook', [PaymentNotificationController::class, 'handleMercadoPagoWebhook'])->name('mercadopago.webhook');
