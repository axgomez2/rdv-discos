<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\VinylMaster;
use App\Models\Equipment;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        $vinyls = VinylMaster::with(['artists', 'recordLabel', 'vinylSec', 'styles', 'tracks'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhereHas('artists', function ($q) use ($query) {
                      $q->where('name', 'LIKE', "%{$query}%");
                  });
            })
            ->get();

        $vinyls->transform(function ($vinyl) {
            $vinyl->tracks->transform(function ($track) use ($vinyl) {
                $track->artist = $vinyl->artists->pluck('name')->implode(', ');
                $track->cover_url = $vinyl->cover_image_url;
                return $track;
            });
            return $vinyl;
        });

        $equipments = Equipment::where(function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
              ->orWhere('description', 'LIKE', "%{$query}%");
        })->get();

        $results = $vinyls->concat($equipments);

        return view('site.search', compact('results', 'query'));
    }
}
