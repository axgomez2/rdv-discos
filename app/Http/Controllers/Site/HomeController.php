<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\VinylMaster;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $latestVinyls = VinylMaster::with(['artists', 'vinylSec', 'catStyleShops'])
            ->whereHas('vinylSec', function ($query) {
                $query->where('in_stock', true);
            })
            ->latest()
            ->take(value: 20)
            ->get();

        $slideVinyls = VinylMaster::with(['artists', 'vinylSec', 'catStyleShops'])
        ->whereHas('vinylSec', function ($query) {
            $query->where('in_stock', true);
        })
        ->latest()
        ->take(value: 10)
        ->get();

        // Obter as 3 playlists mais recentes que estão ativas
        $featuredPlaylists = Playlist::with(['tracks.vinylMaster', 'tracks.trackable'])
            ->where('is_active', true)
            ->latest()
            ->take(3)
            ->get();

        return view('site.index', compact('latestVinyls', 'slideVinyls', 'featuredPlaylists'));
    }

    public function about()
    {
        return view('site.about');
    }
}
