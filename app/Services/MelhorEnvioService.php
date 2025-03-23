<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\SystemSettingsService;

class MelhorEnvioService
{
    protected $apiToken;
    protected $baseUrl;
    protected $fromPostalCode;
    protected $fromData;
    protected $config;
    protected $systemSettings;
    protected $enabled;

    public function __construct(SystemSettingsService $systemSettings)
    {
        try {
            $this->systemSettings = $systemSettings;
            $this->enabled = false; // Definir como false por padrão
            
            // Verificar se a tabela cache existe antes de tentar acessá-la
            if ($this->checkIfCacheTableExists()) {
                $this->enabled = (bool)$this->systemSettings->get('shipping', 'melhorenvio_enabled', false);
                $this->apiToken = $this->systemSettings->get('shipping', 'melhorenvio_client_token', '');
                $this->sandbox = (bool)$this->systemSettings->get('shipping', 'melhorenvio_sandbox', true);
            } else {
                $this->apiToken = '';
                $this->sandbox = true;
            }
            
            // Configuração base
            $this->baseUrl = $this->sandbox
                ? 'https://sandbox.melhorenvio.com.br/api/v2/'
                : 'https://api.melhorenvio.com.br/v2/';
            
            // Configuração dos dados de origem
            $defaultPostalCode = config('melhorenvio.from.postal_code', '');
            
            if ($this->checkIfCacheTableExists()) {
                $this->fromPostalCode = $this->systemSettings->get('shipping', 'melhorenvio_postal_code', $defaultPostalCode);
                
                // Dados do remetente
                $this->fromData = [
                    'postal_code' => $this->fromPostalCode,
                    'address' => $this->systemSettings->get('shipping', 'melhorenvio_address', ''),
                    'number' => $this->systemSettings->get('shipping', 'melhorenvio_number', ''),
                    'complement' => $this->systemSettings->get('shipping', 'melhorenvio_complement', ''),
                    'district' => $this->systemSettings->get('shipping', 'melhorenvio_district', ''),
                    'city' => $this->systemSettings->get('shipping', 'melhorenvio_city', ''),
                    'state' => $this->systemSettings->get('shipping', 'melhorenvio_state', ''),
                    'country' => $this->systemSettings->get('shipping', 'melhorenvio_country', 'BR'),
                ];
            } else {
                $this->fromPostalCode = $defaultPostalCode;
                
                // Dados do remetente vazios
                $this->fromData = [
                    'postal_code' => $this->fromPostalCode,
                    'country' => 'BR',
                ];
            }
            
            // Garantir que as outras configurações sejam carregadas do arquivo de configuração
            $this->config = config('melhorenvio');
        } catch (\Exception $e) {
            Log::error('Erro ao inicializar MelhorEnvioService: ' . $e->getMessage());
            $this->enabled = false;
            $this->apiToken = '';
            $this->sandbox = true;
            $this->fromPostalCode = '';
            $this->fromData = [];
            $this->config = config('melhorenvio');
        }
    }
    
    /**
     * Verifica se a tabela cache existe no banco de dados
     * 
     * @return bool
     */
    protected function checkIfCacheTableExists()
    {
        try {
            $tables = \Illuminate\Support\Facades\DB::select("SHOW TABLES LIKE 'cache'");
            return count($tables) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Verifica se o serviço está habilitado
     * 
     * @return bool
     */
    public function isEnabled()
    {
        if (!$this->checkIfCacheTableExists()) {
            return false;
        }
        return $this->enabled && !empty($this->apiToken);
    }

    public function calculateShipping($cartItems, $toPostalCode)
    {
        if (!$this->isEnabled()) {
            return [];
        }

        $cacheKey = "shipping_calc_{$toPostalCode}_" . md5(json_encode($cartItems));

        return Cache::remember($cacheKey, now()->addMinutes($this->config['cache_time']), function () use ($cartItems, $toPostalCode) {
            try {
                $products = $this->formatCartItemsForShipping($cartItems);
                $totalWeight = collect($products)->sum(function ($product) {
                    return $product['weight'] * $product['quantity'];
                });
                $maxDimensions = collect($products)->reduce(function ($carry, $product) {
                    return [
                        'width' => max($carry['width'], $product['width']),
                        'height' => max($carry['height'], $product['height']),
                        'length' => max($carry['length'], $product['length'])
                    ];
                }, ['width' => 0, 'height' => 0, 'length' => 0]);

                $totalValue = collect($products)->sum(function ($product) {
                    return $product['insurance_value'] * $product['quantity'];
                });

                $payload = [
                    'from' => array_filter($this->fromData),
                    'to' => [
                        'postal_code' => $toPostalCode,
                    ],
                    'package' => [
                        'width' => $maxDimensions['width'],
                        'height' => $maxDimensions['height'],
                        'length' => $maxDimensions['length'],
                        'weight' => $totalWeight
                    ],
                    'options' => [
                        'insurance_value' => $totalValue,
                        'receipt' => $this->config['defaults']['receipt'],
                        'own_hand' => $this->config['defaults']['own_hand'],
                        'collect' => $this->config['defaults']['collect']
                    ],
                    'services' => implode(',', array_keys($this->config['services']))
                ];

                Log::info('Calculando frete Melhor Envio:', ['payload' => $payload]);

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiToken,
                ])->post($this->baseUrl . 'me/shipment/calculate', $payload);

                if ($response->successful()) {
                    return $this->formatShippingOptions($response->json());
                }

                Log::error('Erro na API do Melhor Envio:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            } catch (\Exception $e) {
                Log::error('Erro ao calcular frete:', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return [];
            }
        });
    }

    protected function formatCartItemsForShipping($cartItems)
    {
        $defaults = $this->config['defaults']['dimensions'];

        return $cartItems->map(function ($item) use ($defaults) {
            $product = $item->product;
            $dimensions = $product->dimensions;
            $weight = $product->weight;

            return [
                'id' => $product->id,
                'width' => $dimensions ? $dimensions->width : $defaults['width'],
                'height' => $dimensions ? $dimensions->height : $defaults['height'],
                'length' => $dimensions ? $dimensions->length : $defaults['length'],
                'weight' => $weight ? $weight->value : $defaults['weight'],
                'insurance_value' => $product->price,
                'quantity' => $item->quantity,
            ];
        })->toArray();
    }

    protected function formatShippingOptions($response)
    {
        $options = [];
        $services = $this->config['services'];

        foreach ($response as $option) {
            if (isset($option['price']) && $option['price'] > 0) {
                $serviceId = $option['id'];
                $serviceName = $services[$serviceId]['name'] ?? $option['name'];
                $companyName = $services[$serviceId]['company'] ?? $option['company']['name'] ?? 'Correios';

                $options[] = [
                    'id' => $serviceId,
                    'name' => $serviceName,
                    'price' => $option['price'],
                    'delivery_time' => $option['delivery_time'],
                    'company' => $companyName,
                    'custom_delivery_time' => $this->formatDeliveryTime($option['delivery_time']),
                    'custom_price' => 'R$ ' . number_format($option['price'], 2, ',', '.'),
                ];
            }
        }
        return $options;
    }

    protected function formatDeliveryTime($days)
    {
        return $days . ' ' . ($days > 1 ? 'dias úteis' : 'dia útil');
    }

    /**
     * Gera uma etiqueta de envio
     * 
     * @param array $data Dados do pedido contendo informações de envio, produtos, etc.
     * @return array
     */
    public function generateLabel($data)
    {
        try {
            if (!$this->isEnabled()) {
                return ['success' => false, 'message' => 'Serviço do Melhor Envio não está habilitado'];
            }

            $payload = $this->formatOrderForLabel($data);

            Log::info('Gerando etiqueta Melhor Envio:', ['order_id' => $data['id'] ?? 'N/A']);

            // Primeiro, criamos a etiqueta no carrinho
            $cartResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->post($this->baseUrl . 'me/cart', $payload);

            if (!$cartResponse->successful()) {
                Log::error('Erro ao adicionar etiqueta ao carrinho Melhor Envio:', [
                    'status' => $cartResponse->status(),
                    'body' => $cartResponse->json(),
                ]);
                return [
                    'error' => 'Erro ao adicionar etiqueta ao carrinho: ' . ($cartResponse->json()['message'] ?? 'Erro desconhecido')
                ];
            }

            $cartData = $cartResponse->json();
            $orderCode = $cartData['id'] ?? null;

            if (!$orderCode) {
                return [
                    'error' => 'Código de pedido não encontrado na resposta do carrinho'
                ];
            }

            // Agora vamos para o checkout
            $checkoutResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->post($this->baseUrl . 'me/shipment/checkout', [
                'orders' => [$orderCode]
            ]);

            if (!$checkoutResponse->successful()) {
                Log::error('Erro ao finalizar checkout Melhor Envio:', [
                    'status' => $checkoutResponse->status(),
                    'body' => $checkoutResponse->json(),
                ]);
                return [
                    'error' => 'Erro ao finalizar checkout: ' . ($checkoutResponse->json()['message'] ?? 'Erro desconhecido')
                ];
            }

            // Geramos a etiqueta
            $generateResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->post($this->baseUrl . 'me/shipment/generate', [
                'orders' => [$orderCode]
            ]);

            if (!$generateResponse->successful()) {
                Log::error('Erro ao gerar etiqueta Melhor Envio:', [
                    'status' => $generateResponse->status(),
                    'body' => $generateResponse->json(),
                ]);
                return [
                    'error' => 'Erro ao gerar etiqueta: ' . ($generateResponse->json()['message'] ?? 'Erro desconhecido')
                ];
            }

            // Buscamos a etiqueta gerada
            $trackingResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->get($this->baseUrl . 'me/shipment/tracking', [
                'orders' => $orderCode
            ]);

            $trackingData = $trackingResponse->json();
            
            // Obtemos a URL da etiqueta
            $labelResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->post($this->baseUrl . 'me/shipment/print', [
                'orders' => [$orderCode],
                'mode' => 'public'
            ]);

            if (!$labelResponse->successful()) {
                Log::error('Erro ao obter URL da etiqueta Melhor Envio:', [
                    'status' => $labelResponse->status(),
                    'body' => $labelResponse->json(),
                ]);
                return [
                    'error' => 'Erro ao obter URL da etiqueta: ' . ($labelResponse->json()['message'] ?? 'Erro desconhecido')
                ];
            }

            $labelData = $labelResponse->json();
            
            return [
                'label_url' => $labelData['url'] ?? null,
                'tracking_code' => $trackingData[$orderCode]['tracking'] ?? null,
                'order_id' => $orderCode
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao gerar etiqueta:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'error' => 'Erro ao gerar etiqueta: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Formata um pedido para geração de etiqueta
     * 
     * @param array $data
     * @return array
     */
    protected function formatOrderForLabel($data)
    {
        $user = $data['user'];
        $items = $data['items'];
        $shippingAddress = $data['shippingAddress'];
        
        $products = [];
        foreach ($items as $item) {
            $product = $item->product;
            
            $products[] = [
                'name' => mb_substr($product->name, 0, 250),
                'quantity' => $item->quantity,
                'unitary_value' => $product->price,
                'weight' => $product->weight ?? $this->config['defaults']['dimensions']['weight'],
                'width' => $product->width ?? $this->config['defaults']['dimensions']['width'],
                'height' => $product->height ?? $this->config['defaults']['dimensions']['height'],
                'length' => $product->length ?? $this->config['defaults']['dimensions']['length'],
            ];
        }
        
        $serviceId = $data['service_id'] ?? 1; // PAC por padrão
        $totalValue = $data['total'] ?? collect($products)->sum(function ($product) {
            return $product['unitary_value'] * $product['quantity'];
        });
        
        return [
            'service' => $serviceId,
            'agency' => 49, // Agência padrão
            'from' => array_filter([
                'name' => config('app.name'),
                'phone' => $this->fromData['phone'],
                'email' => $this->fromData['email'],
                'document' => $this->fromData['document'],
                'address' => $this->fromData['address'],
                'number' => $this->fromData['number'],
                'complement' => $this->fromData['complement'],
                'district' => $this->fromData['district'],
                'city' => $this->fromData['city'],
                'state_abbr' => $this->fromData['state'],
                'postal_code' => $this->fromData['postal_code'],
                'country_id' => 'BR'
            ]),
            'to' => array_filter([
                'name' => $user->name,
                'phone' => $user->phone ?? '',
                'email' => $user->email,
                'document' => $user->cpf ?? '',
                'address' => $shippingAddress->street,
                'number' => $shippingAddress->number,
                'complement' => $shippingAddress->complement ?? '',
                'district' => $shippingAddress->district ?? $shippingAddress->neighborhood ?? '',
                'city' => $shippingAddress->city,
                'state_abbr' => $shippingAddress->state,
                'postal_code' => $shippingAddress->zip_code,
                'country_id' => 'BR'
            ]),
            'products' => $products,
            'volumes' => [
                [
                    'height' => $this->config['defaults']['dimensions']['height'],
                    'width' => $this->config['defaults']['dimensions']['width'],
                    'length' => $this->config['defaults']['dimensions']['length'],
                    'weight' => $this->config['defaults']['dimensions']['weight']
                ]
            ],
            'options' => [
                'insurance_value' => $totalValue,
                'receipt' => $this->config['defaults']['receipt'],
                'own_hand' => $this->config['defaults']['own_hand'],
                'collect' => $this->config['defaults']['collect'],
                'reverse' => false,
                'non_commercial' => true,
                'invoice' => [
                    'key' => null
                ],
                'platform' => config('app.name'),
                'tags' => [
                    [
                        'tag' => 'Pedido #' . ($data['id'] ?? 'N/A'),
                        'url' => null
                    ]
                ]
            ]
        ];
    }
}
