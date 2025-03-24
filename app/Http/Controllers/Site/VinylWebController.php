<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\CatStyleShop;
use App\Models\VinylMaster;
use App\Models\Style;
use App\Models\VinylSec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VinylWebController extends Controller
{
    public function index(Request $request)
    {
        $query = VinylMaster::with(['artists', 'recordLabel', 'vinylSec', 'styles', 'product', 'tracks']);

        // Apply search independently with more comprehensive search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('release_year', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('artists', function ($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('recordLabel', function ($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('styles', function ($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('tracks', function ($q) use ($searchTerm) {
                      $q->where('title', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        // Apply category filter
        if ($request->filled('category')) {
            $query->whereHas('catStyleShops', function ($q) use ($request) {
                $q->where('id', $request->category);
            });
        }

        // Apply other filters
        $query = $this->applyFilters($query, $request);

        // Apply sorting
        $query = $this->applySorting($query, $request);

        // Ensure in_stock filter is applied
        if (!$request->has('show_out_of_stock') || !$request->show_out_of_stock) {
            $query->whereHas('vinylSec', function ($q) {
                $q->where('in_stock', true);
            });
        }
        
        // Apply promotional filter
        if ($request->has('only_promotions') && $request->only_promotions) {
            $query->whereHas('vinylSec', function ($q) {
                $q->where('is_promotional', true);
            });
        }

        $vinyls = $query->paginate(20)->appends($request->all());

        // Transform the vinyls to include track information
        $vinyls->getCollection()->transform(function ($vinyl) {
            $vinyl->tracks->transform(function ($track) use ($vinyl) {
                $track->artist = $vinyl->artists->pluck('name')->implode(', ');
                $track->cover_url = $vinyl->cover_image_url;
                return $track;
            });
            return $vinyl;
        });

        $styles = Style::all();
        $categories = CatStyleShop::all();

        // Get min and max prices
        $priceRange = VinylSec::selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();

        return view('site.vinyls.index', compact('vinyls', 'styles', 'priceRange', 'categories'));
    }

    private function applyFilters($query, $request)
    {
        // Apply style filter
        if ($request->filled('style')) {
            $query->whereHas('styles', function ($q) use ($request) {
                $q->where('id', $request->style);
            });
        }

        // Apply price filter
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereHas('vinylSec', function ($q) use ($request) {
                $q->whereBetween('price', [$request->min_price, $request->max_price]);
            });
        }

        return $query;
    }

    private function applySorting($query, $request)
    {
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        switch ($sortBy) {
            case 'price':
                $query->join('vinyl_secs', 'vinyl_masters.id', '=', 'vinyl_secs.vinyl_master_id')
                      ->orderBy('vinyl_secs.price', $sortOrder)
                      ->select('vinyl_masters.*')
                      ->distinct();
                break;
            case 'release_year':
                $query->orderBy('release_year', $sortOrder);
                break;
            case 'title':
                $query->orderBy('title', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
                break;
        }

        return $query;
    }

    public function byCategory(Request $request, $slug)
{
    // Busca a categoria pelo slug
    $category = CatStyleShop::where('slug', $slug)->firstOrFail();

    // Query base
    $query = VinylMaster::whereHas('catStyleShops', function($q) use ($slug) {
        $q->where('slug', $slug);
    })->with(['vinylSec', 'artists', 'recordLabel', 'catStyleShops']);

    // Aplicar ordenação
    switch ($request->get('sort')) {
        case 'price_asc':
            $query->join('vinyl_secs', 'vinyl_masters.id', '=', 'vinyl_secs.vinyl_master_id')
                  ->orderBy('vinyl_secs.price', 'asc')
                  ->select('vinyl_masters.*');
            break;
        case 'price_desc':
            $query->join('vinyl_secs', 'vinyl_masters.id', '=', 'vinyl_secs.vinyl_master_id')
                  ->orderBy('vinyl_secs.price', 'desc')
                  ->select('vinyl_masters.*');
            break;
        case 'artist_asc':
            $query->join('artist_vinyl_master', 'vinyl_masters.id', '=', 'artist_vinyl_master.vinyl_master_id')
                  ->join('artists', 'artists.id', '=', 'artist_vinyl_master.artist_id')
                  ->orderBy('artists.name', 'asc')
                  ->select('vinyl_masters.*')
                  ->distinct();
            break;
        case 'artist_desc':
            $query->join('artist_vinyl_master', 'vinyl_masters.id', '=', 'artist_vinyl_master.vinyl_master_id')
                  ->join('artists', 'artists.id', '=', 'artist_vinyl_master.artist_id')
                  ->orderBy('artists.name', 'desc')
                  ->select('vinyl_masters.*')
                  ->distinct();
            break;
        case 'name_asc':
            $query->orderBy('title', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('title', 'desc');
            break;
        default:
            $query->latest();
            break;
    }

    // Executar a query com paginação
    $vinyls = $query->paginate(20)->withQueryString();

    return view('site.vinyls.category', compact('vinyls', 'category'));
}

    /**
     * Mostra apenas os discos em promoção
     */
    public function promotions(Request $request)
    {
        $query = VinylMaster::with(['artists', 'recordLabel', 'vinylSec', 'styles', 'product', 'tracks'])
            ->whereHas('vinylSec', function ($q) {
                $q->where('is_promotional', true)
                  ->where('in_stock', true);
            });

        // Apply search if provided
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('release_year', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('artists', function ($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('recordLabel', function ($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('styles', function ($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('tracks', function ($q) use ($searchTerm) {
                      $q->where('title', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        // Apply category filter
        if ($request->filled('category')) {
            $query->whereHas('catStyleShops', function ($q) use ($request) {
                $q->where('id', $request->category);
            });
        }

        // Apply style filter
        if ($request->filled('style')) {
            $query->whereHas('styles', function ($q) use ($request) {
                $q->where('id', $request->style);
            });
        }

        // Apply price filter
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereHas('vinylSec', function ($q) use ($request) {
                $q->whereBetween('promotional_price', [$request->min_price, $request->max_price]);
            });
        }

        // Apply sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        switch ($sortBy) {
            case 'price':
                $query->join('vinyl_secs', 'vinyl_masters.id', '=', 'vinyl_secs.vinyl_master_id')
                      ->orderBy('vinyl_secs.promotional_price', $sortOrder)
                      ->select('vinyl_masters.*')
                      ->distinct();
                break;
            case 'release_year':
                $query->orderBy('release_year', $sortOrder);
                break;
            case 'title':
                $query->orderBy('title', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
                break;
        }

        $vinyls = $query->paginate(20)->appends($request->all());

        // Transform the vinyls to include track information
        $vinyls->getCollection()->transform(function ($vinyl) {
            $vinyl->tracks->transform(function ($track) use ($vinyl) {
                $track->artist = $vinyl->artists->pluck('name')->implode(', ');
                $track->cover_url = $vinyl->cover_image_url;
                return $track;
            });
            return $vinyl;
        });

        $styles = Style::all();
        $categories = CatStyleShop::all();

        // Get min and max prices for promotional items
        $priceRange = VinylSec::where('is_promotional', true)
            ->selectRaw('MIN(promotional_price) as min_price, MAX(promotional_price) as max_price')
            ->first();

        // Fallback if no promotional items exist
        if (!$priceRange->min_price) {
            $priceRange = (object)[
                'min_price' => 0,
                'max_price' => 1000
            ];
        }

        return view('site.vinyls.promotions', compact('vinyls', 'styles', 'priceRange', 'categories'));
    }
}
