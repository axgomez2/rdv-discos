<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Services\MercadoPagoService;
use App\Services\ShippingService;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected $mercadoPagoService;
    protected $shippingService;

    public function __construct(
        MercadoPagoService $mercadoPagoService,
        ShippingService $shippingService
    ) {
        $this->mercadoPagoService = $mercadoPagoService;
        $this->shippingService = $shippingService;
    }

    public function index()
    {
        $subscriptions = Subscription::with(['user', 'package'])
            ->latest()
            ->paginate(10);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'package', 'shipments.address']);
        
        // Carrega os endereços do usuário para o formulário de criação de envio
        $addresses = $subscription->user->addresses;
        
        return view('admin.subscriptions.show', compact('subscription', 'addresses'));
    }

    public function cancel(Subscription $subscription)
    {
        try {
            // Verifica se a assinatura já está cancelada
            if ($subscription->isCancelled()) {
                return back()->with('error', 'Esta assinatura já está cancelada.');
            }

            // Tenta cancelar usando external_reference primeiro, com fallback para mercadopago_subscription_id
            $subscriptionId = $subscription->external_reference ?? $subscription->mercadopago_subscription_id;
            
            if (empty($subscriptionId)) {
                return back()->with('error', 'ID da assinatura no MercadoPago não encontrado.');
            }

            // Cancela a assinatura no MercadoPago
            $result = $this->mercadoPagoService->cancelSubscription($subscriptionId);

            if (isset($result['error'])) {
                Log::error('Erro ao cancelar assinatura no MercadoPago', [
                    'subscription_id' => $subscription->id,
                    'external_id' => $subscriptionId,
                    'error' => $result['error']
                ]);
                
                return back()->with('error', 'Erro ao cancelar assinatura: ' . $result['error']);
            }

            // Atualiza os dados da assinatura no banco
            $subscription->status = 'cancelled';
            $subscription->cancelled_at = now();
            $subscription->save();

            Log::info('Assinatura cancelada com sucesso', [
                'subscription_id' => $subscription->id,
                'external_id' => $subscriptionId
            ]);

            return back()->with('success', 'Assinatura cancelada com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar assinatura', [
                'subscription_id' => $subscription->id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Erro ao cancelar assinatura: ' . $e->getMessage());
        }
    }

    public function createShipment(Request $request, Subscription $subscription)
    {
        try {
            $validated = $request->validate([
                'shipping_date' => 'required|date',
                'address_id' => 'required|exists:addresses,id'
            ]);

            $address = Address::findOrFail($validated['address_id']);

            // Criamos um array com um item fictício que representa o pacote da assinatura
            $items = collect([
                (object)[
                    'product' => (object)[
                        'name' => 'Pacote de Assinatura: ' . $subscription->package->name,
                        'price' => $subscription->package->price,
                        'weight' => config('shipping.defaults.weight', 0.5), // peso padrão do pacote
                        'height' => config('shipping.defaults.height', 15),
                        'width' => config('shipping.defaults.width', 20),
                        'length' => config('shipping.defaults.length', 30)
                    ],
                    'price' => $subscription->package->price,
                    'quantity' => 1
                ]
            ]);

            // Calculamos o frete usando o ShippingService
            $shippingOptions = $this->shippingService->calculateShipping($items, $address->zip_code);
            
            // Usamos o primeiro método disponível ou um valor padrão
            $shippingCost = !empty($shippingOptions) ? $shippingOptions[0]['price'] : 15.00;

            // Criamos o registro de envio
            $shipment = $subscription->shipments()->create([
                'address_id' => $validated['address_id'],
                'shipping_cost' => $shippingCost,
                'status' => 'pending',
                'shipping_date' => $validated['shipping_date']
            ]);

            return redirect()->route('admin.subscriptions.show', $subscription)
                ->with('success', 'Envio criado com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao criar envio:', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao criar envio: ' . $e->getMessage());
        }
    }

    public function generateShippingLabel(Subscription $subscription, $shipmentId)
    {
        try {
            $shipment = $subscription->shipments()->findOrFail($shipmentId);
            $address = $shipment->address;
            
            if (!$address) {
                return redirect()->back()->with('error', 'Endereço não encontrado para este envio.');
            }

            // Criamos um objeto similar a um Order para usar com o ShippingService
            $mockOrder = new \stdClass();
            $mockOrder->id = 'SUB-' . $subscription->id . '-SHIP-' . $shipment->id;
            $mockOrder->user = $subscription->user;
            $mockOrder->total = $subscription->package->price;
            $mockOrder->shippingAddress = $address;
            
            // Criamos itens simulados para o envio
            $mockOrder->items = collect([
                (object)[
                    'product' => (object)[
                        'name' => 'Pacote de Assinatura: ' . $subscription->package->name,
                        'price' => $subscription->package->price,
                        'weight' => config('shipping.defaults.weight', 0.5),
                        'height' => config('shipping.defaults.height', 15),
                        'width' => config('shipping.defaults.width', 20),
                        'length' => config('shipping.defaults.length', 30)
                    ],
                    'price' => $subscription->package->price,
                    'quantity' => 1
                ]
            ]);

            // Geramos a etiqueta usando o serviço de envio
            $labelResult = $this->shippingService->generateShippingLabel($mockOrder);

            if (isset($labelResult['error'])) {
                return redirect()->back()
                    ->with('error', 'Erro ao gerar etiqueta: ' . $labelResult['error']);
            }

            // Atualizamos o registro de envio com as informações da etiqueta
            $shipment->update([
                'tracking_code' => $labelResult['tracking_code'] ?? $labelResult['tracking'] ?? null,
                'shipping_label_url' => $labelResult['label_url'] ?? $labelResult['url'] ?? null,
                'status' => 'processing'
            ]);

            return redirect()->route('admin.subscriptions.show', $subscription)
                ->with('success', 'Etiqueta de envio gerada com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao gerar etiqueta:', [
                'subscription_id' => $subscription->id,
                'shipment_id' => $shipmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao gerar etiqueta: ' . $e->getMessage());
        }
    }
}
