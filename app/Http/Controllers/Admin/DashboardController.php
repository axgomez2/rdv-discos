<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VinylMaster;
use App\Models\VinylSec;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalVinyls = VinylMaster::count();
        $completedVinyls = VinylMaster::has('vinylSec')->count();
        $outOfStockVinyls = VinylSec::where('quantity', 0)->count();
        $totalCustomers = User::count();

        return view('admin.index', compact('totalVinyls', 'completedVinyls', 'outOfStockVinyls', 'totalCustomers'));
    }
}
