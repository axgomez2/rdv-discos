<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ShippingService;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class ShippingController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    public function index()
    {
        $orders = Order::with(['items.product', 'shippingAddress', 'user'])
            ->whereNull('shipping_label')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.shipping.index', compact('orders'));
    }

    public function generateLabel(Request $request, Order $order)
    {
        try {
            $response = $this->shippingService->generateShippingLabel($order);

            if (isset($response['error'])) {
                return back()->with('error', 'Erro ao gerar etiqueta: ' . $response['error']);
            }

            $order->update([
                'shipping_label' => $response['label_url'] ?? $response['url'] ?? null,
                'tracking_code' => $response['tracking_code'] ?? $response['tracking'] ?? null
            ]);

            return back()->with('success', 'Etiqueta gerada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao gerar etiqueta de envio:', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erro ao gerar etiqueta. Por favor, tente novamente: ' . $e->getMessage());
        }
    }

    public function printLabel(Order $order)
    {
        if (!$order->shipping_label) {
            return back()->with('error', 'Etiqueta não encontrada para este pedido.');
        }

        return redirect($order->shipping_label);
    }

    public function trackShipment(Order $order)
    {
        try {
            if (!$order->tracking_code) {
                return back()->with('error', 'Código de rastreamento não encontrado para este pedido.');
            }
            
            $trackingInfo = $this->shippingService->getTrackingInfo($order->tracking_code);
            return view('admin.shipping.tracking', compact('order', 'trackingInfo'));
        } catch (\Exception $e) {
            Log::error('Erro ao rastrear envio:', [
                'order_id' => $order->id,
                'tracking_code' => $order->tracking_code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erro ao rastrear envio: ' . $e->getMessage());
        }
    }
}
