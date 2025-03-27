<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VinylSec;
use App\Models\VinylMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function vinyl()
    {
        // Estatísticas gerais de estoque
        $totalDiscs = VinylSec::count();
        $availableDiscs = VinylSec::where('in_stock', true)->count();
        $unavailableDiscs = $totalDiscs - $availableDiscs;
        
        // Valores totais
        $totalBuyValue = VinylSec::sum('buy_price');
        $totalSellValue = VinylSec::sum('price');
        $potentialProfit = $totalSellValue - $totalBuyValue;
        
        // Dados para a lista de discos
        $discs = VinylSec::with(['vinylMaster', 'product'])
            ->select([
                'vinyl_secs.*',
                'vinyl_masters.title',
                'vinyl_masters.cover_image',
            ])
            ->join('vinyl_masters', 'vinyl_secs.vinyl_master_id', '=', 'vinyl_masters.id')
            ->orderBy('vinyl_masters.title')
            ->get();
        
        // Dados agrupados por fornecedor
        $supplierStats = VinylSec::whereNotNull('supplier')
            ->select('supplier', 
                     DB::raw('COUNT(*) as total_discs'),
                     DB::raw('SUM(buy_price) as total_buy'),
                     DB::raw('SUM(price) as total_sell'),
                     DB::raw('SUM(case when in_stock = 1 then 1 else 0 end) as available'))
            ->groupBy('supplier')
            ->get();

        return view('admin.reports.vinyl', compact(
            'totalDiscs', 
            'availableDiscs', 
            'unavailableDiscs', 
            'totalBuyValue', 
            'totalSellValue', 
            'potentialProfit',
            'discs',
            'supplierStats'
        ));
    }
}
