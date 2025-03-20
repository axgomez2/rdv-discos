<?php

use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Site\OrderController;
use App\Http\Controllers\Site\WantlistController;
use App\Http\Controllers\Site\WishlistController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'rolemanager:user'])->group(function () {
    // Rotas de Wishlist/Favoritos
    Route::get('/favoritos', [WishlistController::class, 'index'])->name('site.wishlist.index');
    Route::post('/favoritos/add', [WishlistController::class, 'add'])->name('site.wishlist.add');
    Route::delete('/favoritos/remove/{id}', [WishlistController::class, 'remove'])->name('site.wishlist.remove');
    Route::post('/favoritos/toggle', [WishlistController::class, 'toggleFavorite'])->name('site.wishlist.toggle.favorite');

    // Rotas de Wantlist
    Route::get('/wantlist', [WantlistController::class, 'index'])->name('site.wantlist.index');
    Route::post('/wantlist/add', [WantlistController::class, 'add'])->name('site.wantlist.add');
    Route::delete('/wantlist/remove/{id}', [WantlistController::class, 'remove'])->name('site.wantlist.remove');
    Route::post('/wantlist/toggle', [WantlistController::class, 'toggle'])->name('site.wantlist.toggle');

    // Rota de toggle para wishlist (usada pelo cart.js)
    Route::post('/wishlist/toggle/{type}/{id}', [WishlistController::class, 'toggle'])->name('site.wishlist.toggle');

    // Rotas de perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotas de pedidos
    Route::get('/pedidos', [OrderController::class, 'index'])->name('site.orders.index');
    Route::get('/pedidos/{id}', [OrderController::class, 'show'])->name('site.orders.show');

    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('subscriptions/{package}', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
    Route::post('subscriptions/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::get('subscriptions/callback', [SubscriptionController::class, 'callback'])->name('subscriptions.callback');

    Route::post('subscriptions/webhook', [SubscriptionController::class, 'webhook'])
    ->name('subscriptions.webhook');
});
