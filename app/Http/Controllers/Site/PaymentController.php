<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Mostra a página do boleto
     *
     * @param int $orderId
     * @return \Illuminate\View\View
     */
    public function showBoletoPage($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Verificar se o pedido pertence ao usuário logado
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $boletoUrl = session('boleto_url');

        if (!$boletoUrl) {
            return redirect()->route('site.orders.show', $order->id)
                ->with('error', 'Link do boleto não encontrado.');
        }

        return view('site.payments.boleto', compact('order', 'boletoUrl'));
    }

    /**
     * Mostra a página do PIX
     *
     * @param int $orderId
     * @return \Illuminate\View\View
     */
    public function showPixPage($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Verificar se o pedido pertence ao usuário logado
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $qrCodeUrl = session('qr_code_url');

        if (!$qrCodeUrl) {
            return redirect()->route('site.orders.show', $order->id)
                ->with('error', 'QR Code do PIX não encontrado.');
        }

        return view('site.payments.pix', compact('order', 'qrCodeUrl'));
    }
}
