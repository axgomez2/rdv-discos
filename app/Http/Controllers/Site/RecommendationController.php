<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\DJ;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function index()
    {
        $activeDJs = DJ::where('is_active', true)->get();
        return view('site.recommendations.index', compact('activeDJs'));
    }

}
