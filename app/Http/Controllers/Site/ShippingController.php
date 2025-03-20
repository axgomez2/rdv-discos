<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MelhorEnvioService;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    protected $melhorEnvioService;

    public function __construct(MelhorEnvioService $melhorEnvioService)
    {
        $this->melhorEnvioService = $melhorEnvioService;
    }

    public function updatePostalCode(Request $request)
    {
        $request->validate([
            'postal_code' => 'required|string'
        ]);

        $postalCode = preg_replace('/[^0-9]/', '', $request->postal_code);

        if (strlen($postalCode) !== 8) {
            return redirect()->back()->withErrors(['postal_code' => 'CEP inválido']);
        }

        session(['shipping_postal_code' => $postalCode]);
        session()->forget('selected_shipping_price');
        session()->forget('selected_shipping_option');

        return redirect()->route('site.cart.index');
    }

    public function updateShipping(Request $request)
    {
        $request->validate([
            'shipping_option' => 'required'
        ]);

        $cart = app(\App\Http\Controllers\Site\CartController::class)->getOrCreateCart();
        $postalCode = session('shipping_postal_code');

        if (!$postalCode) {
            return response()->json(['error' => 'CEP não informado'], 400);
        }

        try {
            $shippingOptions = $this->melhorEnvioService->calculateShipping($cart->items, $postalCode);

            if (empty($shippingOptions)) {
                return response()->json(['error' => 'Não foi possível calcular o frete. Tente novamente.'], 400);
            }

            $selectedOption = collect($shippingOptions)
                ->firstWhere('id', $request->shipping_option);

            if (!$selectedOption) {
                return response()->json(['error' => 'Opção de frete inválida'], 400);
            }

            session([
                'selected_shipping_option' => $request->shipping_option,
                'selected_shipping_price' => $selectedOption['price']
            ]);

            return response()->json([
                'success' => true,
                'shipping' => $selectedOption['price'],
                'delivery_time' => $selectedOption['delivery_time']
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar frete:', [
                'error' => $e->getMessage(),
                'shipping_option' => $request->shipping_option
            ]);

            return response()->json([
                'error' => 'Erro ao atualizar frete. Tente novamente.'
            ], 500);
        }
    }
}
