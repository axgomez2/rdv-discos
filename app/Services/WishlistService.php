<?php

namespace App\Services;

use App\Models\User;
use App\Models\VinylMaster;
use App\Models\Equipment;

class WishlistService
{
    public function toggleItem(User $user, string $productType, int $productId): array
    {
        $product = $productType::findOrFail($productId);
        $isInStock = $product->is_available ?? true;

        // Verifica se o item está na wishlist
        $inWishlist = $user->wishlist()
            ->where('product_type', $productType)
            ->where('product_id', $productId)
            ->exists();

        // Verifica se o item está na wantlist
        $inWantlist = $user->wantlist()
            ->where('product_type', $productType)
            ->where('product_id', $productId)
            ->exists();

        $wasInWishlist = $inWishlist;
        $wasInWantlist = $inWantlist;

        // Se o item está na wishlist, remove
        if ($inWishlist) {
            $user->wishlist()
                ->where('product_type', $productType)
                ->where('product_id', $productId)
                ->delete();
            $inWishlist = false;
            $message = 'Item removido dos seus favoritos.';
        }
        // Se o item está na wantlist, remove
        elseif ($inWantlist) {
            $user->wantlist()
                ->where('product_type', $productType)
                ->where('product_id', $productId)
                ->delete();
            $inWantlist = false;
            $message = 'Item removido da sua wantlist.';
        }
        // Se não está em nenhuma lista, adiciona na lista apropriada
        else {
            if ($isInStock) {
                $user->wishlist()->create([
                    'product_type' => $productType,
                    'product_id' => $productId,
                ]);
                $inWishlist = true;
                $message = 'Item adicionado aos seus favoritos!';
            } else {
                $user->wantlist()->create([
                    'product_type' => $productType,
                    'product_id' => $productId,
                ]);
                $inWantlist = true;
                $message = 'Item adicionado à sua wantlist!';
            }
        }

        // Conta total de itens na wishlist
        $wishlistCount = $user->wishlist()->count();

        return [
            'success' => true,
            'message' => $message,
            'in_wishlist' => $inWishlist,
            'in_wantlist' => $inWantlist,
            'was_in_wishlist' => $wasInWishlist,
            'was_in_wantlist' => $wasInWantlist,
            'is_in_stock' => $isInStock,
            'wishlistCount' => $wishlistCount,
        ];
    }
}
