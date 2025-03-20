<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\CartController;
use App\Http\Controllers\Site\CartItemController;
use App\Http\Controllers\Site\ShippingController;

Route::prefix('carrinho')->group(function () {
    // Cart routes
    Route::get('/', [CartController::class, 'index'])->name('site.cart.index');
    Route::post('/items', [CartItemController::class, 'store'])->name('site.cart.add');
    Route::delete('/items/{cartItem}', [CartItemController::class, 'destroy'])->name('site.cart.items.destroy');
    Route::post('/items/{cartItem}', [CartItemController::class, 'update'])->name('site.cart.update');
    Route::post('/check-stock', [CartItemController::class, 'checkStock'])->name('site.cart.checkStock');

    // Shipping routes
    Route::post('/postal-code', [ShippingController::class, 'updatePostalCode'])->name('site.cart.updatePostalCode');
    Route::post('/shipping/update', [ShippingController::class, 'updateShipping'])->name('site.cart.updateShipping');
});
