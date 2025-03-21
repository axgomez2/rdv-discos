<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CartItemController extends Controller
{
    protected $cartController;

    public function __construct(CartController $cartController)
    {
        $this->cartController = $cartController;
    }

    public function store(Request $request)
    {
        Log::info('Requisição recebida para adicionar item ao carrinho', $request->all());

        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1'
            ]);
            
            DB::beginTransaction();

            $cart = $this->cartController->getOrCreateCart();
            Log::info('Carrinho obtido/criado', ['cart_id' => $cart->id]);

            $product = Product::findOrFail($request->product_id);
            Log::info('Produto encontrado', ['product_id' => $product->id]);
            
            // Verificação de estoque para produtos do tipo VinylMaster
            if ($product->productable_type === 'App\\Models\\VinylMaster') {
                $vinylMaster = $product->productable;
                $vinylSec = $vinylMaster->vinylSec;
                
                if (!$vinylSec) {
                    throw new \Exception('Detalhes do produto não encontrados.');
                }
                
                // Verificar quantidade disponível
                $currentCartItem = $cart->items()->where('product_id', $product->id)->first();
                $currentQuantity = $currentCartItem ? $currentCartItem->quantity : 0;
                $requestedTotal = $currentQuantity + $request->quantity;
                
                if ($requestedTotal > $vinylSec->quantity) {
                    throw new \Exception('Quantidade solicitada excede o estoque disponível. Estoque atual: ' . $vinylSec->quantity);
                }
            }

            $cartItem = $cart->items()->updateOrCreate(
                ['product_id' => $product->id],
                ['quantity' => DB::raw('quantity + ' . $request->quantity)]
            );
            Log::info('Item do carrinho atualizado/criado', ['cart_item_id' => $cartItem->id]);

            DB::commit();

            $message = 'Item adicionado ao carrinho com sucesso.';
            $success = true;
            $cartCount = $cart->items->sum('quantity');

            Log::info('Item adicionado ao carrinho com sucesso', [
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'cart_count' => $cartCount
            ]);
        } catch (ValidationException $e) {
            Log::error('Erro de validação ao adicionar item ao carrinho: ' . $e->getMessage(), [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            $message = 'Dados de requisição inválidos. Por favor, verifique os dados e tente novamente.';
            $success = false;
            $cartCount = null;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao adicionar item ao carrinho: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            $message = $e->getMessage();
            $success = false;
            $cartCount = null;
        }

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $message,
                'cartCount' => $cartCount
            ]);
        }

        if ($success) {
            return redirect()->route('site.cart.index')->with('success', $message);
        } else {
            return redirect()->back()->with('error', $message);
        }
    }

    public function update(Request $request, CartItem $cartItem)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $product = $cartItem->product;
            
            // Verificação de estoque para produtos do tipo VinylMaster
            if ($product->productable_type === 'App\\Models\\VinylMaster') {
                $vinylMaster = $product->productable;
                $vinylSec = $vinylMaster->vinylSec;
                
                if (!$vinylSec) {
                    throw new \Exception('Detalhes do produto não encontrados.');
                }
                
                // Verificar quantidade disponível
                if ($request->quantity > $vinylSec->quantity) {
                    throw new \Exception('Quantidade solicitada excede o estoque disponível. Estoque atual: ' . $vinylSec->quantity);
                }
            }

            $cartItem->update(['quantity' => $request->quantity]);
            
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                $cart = $this->cartController->getOrCreateCart();
                $cartCount = $cart->items->sum('quantity');
                $subtotal = $cart->items->sum(function ($item) {
                    return $item->quantity * $item->product->price;
                });
                
                return response()->json([
                    'success' => true,
                    'message' => 'Quantidade atualizada com sucesso.',
                    'cartCount' => $cartCount,
                    'itemSubtotal' => $cartItem->quantity * $product->price,
                    'cartSubtotal' => $subtotal
                ]);
            }

            return redirect()->route('site.cart.index')->with('success', 'Item atualizado no carrinho.');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar item do carrinho: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'cart_item_id' => $cartItem->id
            ]);
            
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            
            return redirect()->route('site.cart.index')->with('error', $e->getMessage());
        }
    }

    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();
        return redirect()->route('site.cart.index')->with('success', 'Item removed from cart.');
    }


    public function checkStock(Request $request)
    {
        try {
            $cartItems = $request->input('items');
            $stockStatus = [];
            $allInStock = true;
            $outOfStockItems = [];

            foreach ($cartItems as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->productable_type === 'App\\Models\\VinylMaster') {
                    $vinylMaster = $product->productable;
                    $vinylSec = $vinylMaster->vinylSec;
                    
                    if (!$vinylSec) {
                        throw new \Exception('Detalhes do produto não encontrados para o produto ID: ' . $product->id);
                    }
                    
                    $availableStock = $vinylSec->quantity;
                    $isInStock = $availableStock >= $item['quantity'];
                    
                    if (!$isInStock) {
                        $allInStock = false;
                        $outOfStockItems[] = [
                            'id' => $product->id,
                            'name' => $product->name,
                            'requested' => $item['quantity'],
                            'available' => $availableStock
                        ];
                    }

                    $stockStatus[$item['product_id']] = [
                        'available' => $availableStock,
                        'requested' => $item['quantity'],
                        'status' => $isInStock ? 'ok' : 'insufficient',
                        'product_name' => $product->name
                    ];
                } else {
                    // Para outros tipos de produtos, vamos assumir que há estoque
                    $stockStatus[$item['product_id']] = [
                        'available' => 999,
                        'requested' => $item['quantity'],
                        'status' => 'ok',
                        'product_name' => $product->name
                    ];
                }
            }

            return response()->json([
                'all_in_stock' => $allInStock,
                'out_of_stock_items' => $outOfStockItems,
                'items' => $stockStatus
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao verificar estoque: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
