<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Services\ShippingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cart->load('items.product.productable.vinylSec');

        $subtotal = $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        $user = Auth::user();
        $address = $user ? $user->addresses()->where('is_default', 1)->first() : null;

        $postalCode = null;
        $shippingOptions = [];
        $shipping = 0;

        if ($address && $address->zip_code) {
            $postalCode = preg_replace('/[^0-9]/', '', $address->zip_code);
        } elseif(session('shipping_postal_code')) {
            $postalCode = session('shipping_postal_code');
        }

        if ($postalCode && !empty($cart->items)) {
            try {
                $shippingOptions = $this->shippingService->calculateShipping($cart->items, $postalCode);

                if (session('selected_shipping_price')) {
                    $shipping = session('selected_shipping_price');
                } elseif (!empty($shippingOptions)) {
                    $shipping = collect($shippingOptions)->min('price') ?? 0;
                }

                Log::info('Opções de frete calculadas:', ['options' => $shippingOptions, 'shipping' => $shipping]);
            } catch (\Exception $e) {
                Log::error('Erro ao calcular frete no carrinho:', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $tax = $subtotal * 0.1; // 10% de imposto
        $total = $subtotal + $shipping + $tax;

        return view('site.cart.index', compact('cart', 'subtotal', 'shipping', 'tax', 'total', 'shippingOptions', 'address'));
    }

    public function addToCart(Request $request)
    {
        $cart = $this->getOrCreateCart();
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $product = Product::findOrFail($productId);
        $vinylSec = $product->productable->vinylSec;

        if ($vinylSec->quantity < $quantity) {
            return redirect()->back()->with('error', 'Desculpe, não há estoque suficiente para este disco.');
        }

        $cartItem = $cart->items()->where('product_id', $productId)->first();

        if ($cartItem) {
            if ($vinylSec->quantity < ($cartItem->quantity + $quantity)) {
                return redirect()->back()->with('error', 'Desculpe, não há estoque suficiente para adicionar mais unidades deste disco.');
            }
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        return redirect()->route('site.cart.index')->with('success', 'Item adicionado ao carrinho.');
    }

    public function updatePostalCode(Request $request)
    {
        $request->validate([
            'postal_code' => 'required|string'
        ]);

        $postalCode = preg_replace('/[^0-9]/', '', $request->postal_code);

        if (strlen($postalCode) !== 8) {
            return redirect()->back()->withErrors(['postal_code' => 'CEP inválido']);
        }

        session(['shipping_postal_code' => $postalCode]);
        session()->forget('selected_shipping_price');
        session()->forget('selected_shipping_option');

        return redirect()->route('site.cart.index');
    }

    public function removeItem($itemId)
    {
        CartItem::destroy($itemId);
        return redirect()->route('site.cart.index')->with('success', 'Item removido do carrinho.');
    }

    public function getOrCreateCart()
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

            // Verifica se existe um carrinho na sessão para migrar
            $sessionId = session()->get('cart_session_id');
            if ($sessionId) {
                $sessionCart = Cart::where('session_id', $sessionId)->first();
                if ($sessionCart) {
                    // Migra os itens do carrinho da sessão para o carrinho do usuário
                    foreach ($sessionCart->items as $item) {
                        $existingItem = $cart->items()->where('product_id', $item->product_id)->first();
                        if ($existingItem) {
                            $existingItem->quantity += $item->quantity;
                            $existingItem->save();
                        } else {
                            $item->cart_id = $cart->id;
                            $item->save();
                        }
                    }
                    $sessionCart->delete();
                }
                session()->forget('cart_session_id');
            }
            return $cart;
        } else {
            $sessionId = session()->get('cart_session_id');

            if (!$sessionId) {
                $sessionId = Str::uuid();
                session(['cart_session_id' => $sessionId]);
            }

            return Cart::firstOrCreate(['session_id' => $sessionId]);
        }
    }
}
