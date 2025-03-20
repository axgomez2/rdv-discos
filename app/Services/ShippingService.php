<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\MelhorEnvioService;

class ShippingService
{
    protected $melhorEnvioService;
    protected $config;

    public function __construct(MelhorEnvioService $melhorEnvioService)
    {
        $this->melhorEnvioService = $melhorEnvioService;
        $this->config = config('melhorenvio');
    }

    public function calculateShipping($cartItems, $zipCode)
    {
        Log::info('Calculando frete para CEP: ' . $zipCode);

        try {
            // Delegamos o cálculo para o serviço especializado do Melhor Envio
            $shippingOptions = $this->melhorEnvioService->calculateShipping($cartItems, $zipCode);

            if (!empty($shippingOptions)) {
                Log::info('Opções de frete calculadas com sucesso', ['options_count' => count($shippingOptions)]);
                return $shippingOptions;
            } else {
                Log::error('Nenhuma opção de frete disponível para o CEP: ' . $zipCode);
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [];
        }
    }

    public function generateShippingLabel($order)
    {
        try {
            if (!$order->shippingAddress) {
                return [
                    'error' => 'Endereço de entrega não encontrado'
                ];
            }

            // Formatamos o pedido para a geração de etiqueta
            $data = [
                'id' => $order->id,
                'user' => $order->user,
                'items' => $order->items,
                'shippingAddress' => $order->shippingAddress,
                'total' => $order->total,
                'service_id' => 1 // PAC por padrão
            ];

            // Chamamos o serviço do Melhor Envio para gerar a etiqueta
            $result = $this->melhorEnvioService->generateLabel($data);
            
            if (!isset($result['error']) && isset($result['tracking_code'])) {
                // Atualizamos o status do pedido
                $order->status = 'shipped';
                $order->shipped_at = now();
                $order->save();
                
                Log::info('Etiqueta gerada com sucesso', [
                    'order_id' => $order->id,
                    'tracking' => $result['tracking_code']
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Erro ao gerar etiqueta de envio:', [
                'order_id' => $order->id ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'error' => 'Erro interno ao gerar etiqueta: ' . $e->getMessage()
            ];
        }
    }
    
    public function getTrackingInfo($trackingCode)
    {
        $cacheKey = "tracking_{$trackingCode}";
        $cacheTtl = 30; // 30 minutos

        try {
            return Cache::remember($cacheKey, now()->addMinutes($cacheTtl), function () use ($trackingCode) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->config['api_token'],
                ])->get($this->melhorEnvioService->baseUrl . 'me/shipment/tracking', [
                    'code' => $trackingCode
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Formatamos os dados de tracking para nossa aplicação
                    $events = [];
                    if (isset($data['tracking']['events']) && is_array($data['tracking']['events'])) {
                        foreach ($data['tracking']['events'] as $event) {
                            $events[] = [
                                'status' => $event['status'] ?? 'Status desconhecido',
                                'date' => $event['date'] ?? now()->format('Y-m-d H:i:s'),
                                'location' => $event['location'] ?? null,
                            ];
                        }
                    }

                    // Ordenamos os eventos do mais antigo para o mais recente
                    $events = collect($events)->sortBy('date')->values()->toArray();

                    return [
                        'code' => $trackingCode,
                        'status' => $data['tracking']['status'] ?? 'Desconhecido',
                        'events' => $events,
                        'delivered' => ($data['tracking']['status'] ?? '') === 'delivered',
                        'last_update' => end($events)['date'] ?? now()->format('Y-m-d H:i:s'),
                    ];
                }

                Log::warning('Erro ao rastrear código:', [
                    'tracking_code' => $trackingCode,
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                
                throw new \Exception('Não foi possível obter informações de rastreamento: ' . ($response->json()['message'] ?? 'Erro desconhecido'));
            });
        } catch (\Exception $e) {
            Log::error('Erro ao rastrear envio:', [
                'tracking_code' => $trackingCode,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function formatOrderItemsForShipping($orderItems)
    {
        return $orderItems->map(function ($item) {
            return [
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'unitary_value' => $item->price,
                'weight' => $item->product->weight,
                'width' => $item->product->width,
                'height' => $item->product->height,
                'length' => $item->product->length,
            ];
        })->toArray();
    }
}
