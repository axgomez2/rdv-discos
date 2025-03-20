<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Wantlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WantlistController extends Controller
{
    public function index()
    {
        $wantlistItems = Wantlist::where('user_id', Auth::id())
            ->with(['product'])
            ->get()
            ->map(function ($item) {
                return $item->product;
            });

        return view('site.wishlist.wantlist', compact('wantlistItems'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'product_type' => 'required|string',
        ]);

        $wantlistItem = Wantlist::where([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'product_type' => $request->product_type,
        ])->first();

        if ($wantlistItem) {
            $wantlistItem->delete();
            $message = 'Item removido da Wantlist';
            $added = false;
        } else {
            Wantlist::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'product_type' => $request->product_type,
            ]);
            $message = 'Item adicionado à Wantlist';
            $added = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'added' => $added
        ]);
    }
}

