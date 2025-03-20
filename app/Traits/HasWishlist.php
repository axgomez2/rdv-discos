<?php

namespace App\Traits;

use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

trait HasWishlist
{
    public function wishlists()
    {
        return $this->morphMany(Wishlist::class, 'product');
    }

    public function inWishlist()
    {
        if (!Auth::check()) {
            return false;
        }

        return $this->wishlists()
            ->where('user_id', Auth::id())
            ->exists();
    }
}

