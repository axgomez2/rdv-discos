<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CatStyleShop;
use Illuminate\Support\Facades\Auth;

class NavbarController extends Controller
{
    public function getData()
    {
        $cart = null;
        $cartItems = [];
        $cartCount = 0;
        $cartTotal = 0;

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
        } else {
            $sessionId = session()->get('cart_session_id');
            if ($sessionId) {
                $cart = Cart::where('session_id', $sessionId)->first();
            }
        }

        if ($cart) {
            $cart->load('items.product.productable');
            $cartItems = $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->product->productable->title ?? $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'image' => $item->product->productable->cover_image ?? null,
                ];
            });
            $cartCount = $cart->items->sum('quantity');
            $cartTotal = $cart->items->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });
        }

        $categories = CatStyleShop::all()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ];
        });

        return response()->json([
            'cartItems' => $cartItems,
            'cartCount' => $cartCount,
            'cartTotal' => $cartTotal,
            'categories' => $categories,
            'user' => Auth::check() ? [
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ] : null,
        ]);
    }
}
