<?php

namespace App\Traits;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

trait HasCart
{
    /**
     * Verifica se o modelo está no carrinho do usuário atual
     *
     * @return bool
     */
    public function inCart()
    {
        // Identificar o carrinho atual (do usuário logado ou da sessão)
        $cart = $this->getCurrentCart();
        
        if (!$cart) {
            return false;
        }
        
        // Para VinylMaster precisamos verificar se algum produto associado a este master está no carrinho
        $products = Product::where('productable_type', get_class($this))
                           ->where('productable_id', $this->id)
                           ->pluck('id');
        
        if ($products->isEmpty()) {
            return false;
        }
        
        // Verificar se existe algum item no carrinho com estes produtos
        return CartItem::whereIn('product_id', $products)
                       ->where('cart_id', $cart->id)
                       ->exists();
    }
    
    /**
     * Obtém o carrinho atual do usuário ou da sessão
     *
     * @return \App\Models\Cart|null
     */
    protected function getCurrentCart()
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->first();
        }
        
        $sessionId = Session::getId();
        return Cart::where('session_id', $sessionId)->first();
    }
}
