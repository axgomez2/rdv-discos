<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Services\WishlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VinylMaster;
use App\Models\Wantlist;

class WishlistController extends Controller 
{
    protected $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    public function index()
    {
        $user = Auth::user();
        
        // Busca itens da wishlist com seus produtos
        $wishlistItems = $user->wishlist()
            ->with(['product'])
            ->get()
            ->map(function ($item) {
                return $item->product;
            });

        // Busca itens da wantlist com seus produtos
        $wantlistItems = $user->wantlist()
            ->with(['product'])
            ->get()
            ->map(function ($item) {
                return $item->product;
            });

        return view('site.wishlist.index', compact('wishlistItems', 'wantlistItems'));
    }

    public function toggle($type, $id)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Você precisa estar logado para adicionar itens aos favoritos.'], 401);
        }

        try {
            $fullType = "App\\Models\\{$type}";
            return response()->json(
                $this->wishlistService->toggleItem(auth()->user(), $fullType, $id)
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar sua solicitação: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleFavorite(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Você precisa estar logado para adicionar itens aos favoritos.'], 401);
        }

        $request->validate([
            'product_id' => 'required|integer',
            'product_type' => 'required|in:App\\Models\\VinylMaster,App\\Models\\Equipment',
        ]);

        try {
            $result = $this->wishlistService->toggleItem(
                auth()->user(),
                $request->product_type,
                $request->product_id
            );

            // Se o item estava na wishlist e agora está indisponível, move para wantlist
            if ($result['was_in_wishlist'] && !$result['is_in_stock']) {
                $user = auth()->user();
                $product = $request->product_type::findOrFail($request->product_id);

                // Remove da wishlist
                $user->wishlist()
                    ->where('product_type', $request->product_type)
                    ->where('product_id', $request->product_id)
                    ->delete();

                // Adiciona à wantlist se ainda não estiver lá
                if (!$user->wantlist()
                    ->where('product_type', $request->product_type)
                    ->where('product_id', $request->product_id)
                    ->exists()) {
                    $user->wantlist()->create([
                        'product_type' => $request->product_type,
                        'product_id' => $request->product_id,
                    ]);
                }

                $result['message'] = 'Item movido para sua wantlist pois está indisponível.';
                $result['in_wantlist'] = true;
                $result['in_wishlist'] = false;
            }
            // Se o item estava na wantlist e agora está disponível, move para wishlist
            elseif ($result['was_in_wantlist'] && $result['is_in_stock']) {
                $user = auth()->user();
                $product = $request->product_type::findOrFail($request->product_id);

                // Remove da wantlist
                $user->wantlist()
                    ->where('product_type', $request->product_type)
                    ->where('product_id', $request->product_id)
                    ->delete();

                // Adiciona à wishlist se ainda não estiver lá
                if (!$user->wishlist()
                    ->where('product_type', $request->product_type)
                    ->where('product_id', $request->product_id)
                    ->exists()) {
                    $user->wishlist()->create([
                        'product_type' => $request->product_type,
                        'product_id' => $request->product_id,
                    ]);
                }

                $result['message'] = 'Item movido para seus favoritos pois está disponível.';
                $result['in_wantlist'] = false;
                $result['in_wishlist'] = true;
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar sua solicitação: ' . $e->getMessage(),
            ], 500);
        }
    }
}
