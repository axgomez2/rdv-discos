<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Exibe a lista de pedidos do usuário autenticado
     */
    public function index()
    {
        $user = Auth::user();
        $orders = $user->orders()->orderBy('created_at', 'desc')->paginate(10);

        return view('site.orders.index', compact('orders'));
    }

    /**
     * Exibe os detalhes de um pedido específico
     */
    public function show($id)
    {
        $user = Auth::user();
        $order = $user->orders()->findOrFail($id);

        return view('site.orders.show', compact('order'));
    }
}
