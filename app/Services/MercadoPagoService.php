<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\MercadoPagoClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Subscription\SubscriptionClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;

class MercadoPagoService
{
    private $accessToken;
    private $mpClient;

    public function __construct()
    {
        $this->accessToken = config('services.mercadopago.access_token');
        $this->configureSdk();
    }

    /**
     * Prepara os dados para a sessão do MercadoPago
     *
     * @return string
     */
    public function getPublicKey()
    {
        return config('services.mercadopago.public_key');
    }

    /**
     * Cria uma preferência de pagamento para checkout
     *
     * @param Order $order
     * @return array
     */
    public function createPreference(Order $order)
    {
        try {
            $user = $order->user;
            
            // Verificar se o endereço existe
            if (!$order->shippingAddress) {
                return [
                    'success' => false,
                    'message' => 'Endereço de entrega não encontrado para este pedido'
                ];
            }
            
            $address = $order->shippingAddress;
            
            $preferenceClient = new PreferenceClient();
            
            // Configuração da preferência
            $preferenceData = [
                'items' => $this->getItems($order),
                'payer' => [
                    'name' => $user->first_name ?? $user->name,
                    'surname' => $user->last_name ?? '',
                    'email' => $user->email
                ],
                'external_reference' => (string) $order->id,
                'back_urls' => [
                    'success' => route('site.checkout.success', ['order' => $order->id]),
                    'failure' => route('site.checkout.failure', ['order' => $order->id]),
                    'pending' => route('site.checkout.pending', ['order' => $order->id])
                ],
                'auto_return' => 'approved',
                'notification_url' => config('services.mercadopago.webhook_url'),
                'payment_methods' => [
                    'excluded_payment_types' => [
                        ['id' => 'ticket'] // Exclui pagamento por boleto (será tratado separadamente)
                    ],
                    'installments' => 12 // Máximo de parcelas
                ]
            ];
            
            // Adicionar dados do telefone se disponível
            if (!empty($user->phone)) {
                $preferenceData['payer']['phone'] = [
                    'area_code' => substr(preg_replace('/[^0-9]/', '', $user->phone), 0, 2),
                    'number' => substr(preg_replace('/[^0-9]/', '', $user->phone), 2)
                ];
            }
            
            // Adicionar dados do endereço
            if ($address) {
                $preferenceData['payer']['address'] = [
                    'street_name' => $address->street,
                    'street_number' => $address->number,
                    'zip_code' => preg_replace('/[^0-9]/', '', $address->zip_code ?? $address->cep)
                ];
            }
            
            // Na SDK v3, o método é create() em vez de createPreference()
            $preference = $preferenceClient->create($preferenceData);
            
            // Os dados são retornados diretamente, sem estar dentro de ['response']
            return [
                'success' => true,
                'preference_id' => $preference->id,
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point
            ];
        } catch (MPApiException $e) {
            Log::error('MercadoPago Preference Error: ' . $e->getMessage(), [
                'response' => $e->getApiResponse()
            ]);
            return [
                'success' => false,
                'message' => 'Erro ao criar preferência no MercadoPago: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('MercadoPago Preference Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao criar preferência no MercadoPago: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Processa pagamento via PIX
     *
     * @param Order $order
     * @param array $formData
     * @return array
     */
    public function processPixPayment(Order $order, $formData)
    {
        try {
            // Configurações de pagamento via PIX
            $paymentData = [
                'transaction_amount' => (float) $order->total,
                'description' => 'Pedido #' . $order->id,
                'payment_method_id' => 'pix',
                'payer' => [
                    'email' => $order->user->email,
                    'first_name' => $order->user->first_name,
                    'last_name' => $order->user->last_name,
                    'identification' => [
                        'type' => 'CPF',
                        'number' => $formData['cpf'] ?? $order->user->cpf
                    ]
                ],
                'external_reference' => (string) $order->id,
                'notification_url' => config('services.mercadopago.webhook_url')
            ];

            // Cria o pagamento usando PaymentClient da SDK v3
            $paymentClient = new PaymentClient();
            $payment = $paymentClient->create($paymentData);
            
            // Salva o ID do pagamento e o QR Code para uso futuro
            $order->payment_id = $payment->id;
            $order->payment_status = $payment->status;
            $order->payment_method = 'pix';
            $order->payment_data = json_encode([
                'qr_code' => $payment->point_of_interaction->transaction_data->qr_code ?? null,
                'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64 ?? null,
                'ticket_url' => $payment->point_of_interaction->transaction_data->ticket_url ?? null
            ]);
            $order->save();

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'qr_code' => $payment->point_of_interaction->transaction_data->qr_code ?? null,
                'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64 ?? null,
                'ticket_url' => $payment->point_of_interaction->transaction_data->ticket_url ?? null
            ];
        } catch (MPApiException $e) {
            Log::error('MercadoPago Pix Payment Error: ' . $e->getMessage(), [
                'response' => $e->getApiResponse()
            ]);
            return [
                'success' => false,
                'message' => 'Erro ao processar pagamento PIX: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('MercadoPago Pix Payment Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao processar pagamento PIX: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Processa pagamento via Boleto
     *
     * @param Order $order
     * @param array $formData
     * @return array
     */
    public function processBoletoPayment(Order $order, $formData)
    {
        try {
            // Configurações de pagamento via Boleto
            $paymentData = [
                'transaction_amount' => (float) $order->total,
                'description' => 'Pedido #' . $order->id,
                'payment_method_id' => 'bolbradesco',
                'payer' => [
                    'email' => $order->user->email,
                    'first_name' => $order->user->first_name,
                    'last_name' => $order->user->last_name,
                    'identification' => [
                        'type' => 'CPF',
                        'number' => $formData['cpf'] ?? $order->user->cpf
                    ],
                    'address' => [
                        'zip_code' => $order->shippingAddress->cep,
                        'street_name' => $order->shippingAddress->street,
                        'street_number' => $order->shippingAddress->number,
                        'neighborhood' => $order->shippingAddress->district,
                        'city' => $order->shippingAddress->city,
                        'federal_unit' => $order->shippingAddress->state
                    ]
                ],
                'external_reference' => (string) $order->id,
                'notification_url' => config('services.mercadopago.webhook_url')
            ];

            // Cria o pagamento usando PaymentClient da SDK v3
            $paymentClient = new PaymentClient();
            $payment = $paymentClient->create($paymentData);
            
            // Salva o ID do pagamento e a URL do boleto para uso futuro
            $order->payment_id = $payment->id;
            $order->payment_status = $payment->status;
            $order->payment_method = 'boleto';
            $order->payment_data = json_encode([
                'transaction_id' => $payment->id,
                'ticket_url' => $payment->transaction_details->external_resource_url ?? null,
                'barcode' => $payment->barcode->content ?? null
            ]);
            $order->save();

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'ticket_url' => $payment->transaction_details->external_resource_url ?? null,
                'barcode' => $payment->barcode->content ?? null
            ];
        } catch (MPApiException $e) {
            Log::error('MercadoPago Boleto Payment Error: ' . $e->getMessage(), [
                'response' => $e->getApiResponse()
            ]);
            return [
                'success' => false,
                'message' => 'Erro ao processar pagamento Boleto: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('MercadoPago Boleto Payment Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao processar pagamento Boleto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Processa pagamento via Cartão de Crédito
     *
     * @param Order $order
     * @param array $formData
     * @return array
     */
    public function processCreditCardPayment(Order $order, $formData)
    {
        try {
            // Configurações de pagamento via Cartão
            $paymentData = [
                'transaction_amount' => (float) $order->total,
                'token' => $formData['token'],
                'description' => 'Pedido #' . $order->id,
                'installments' => (int) ($formData['installments'] ?? 1),
                'payment_method_id' => $formData['payment_method_id'],
                'payer' => [
                    'email' => $order->user->email,
                    'identification' => [
                        'type' => 'CPF',
                        'number' => $formData['cpf'] ?? $order->user->cpf
                    ]
                ],
                'external_reference' => (string) $order->id,
                'notification_url' => config('services.mercadopago.webhook_url')
            ];

            // Cria o pagamento usando PaymentClient da SDK v3
            $paymentClient = new PaymentClient();
            $payment = $paymentClient->create($paymentData);
            
            // Salva o ID do pagamento e o status
            $order->payment_id = $payment->id;
            $order->payment_status = $payment->status;
            $order->payment_method = 'credit_card';
            $order->save();

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'status' => $payment->status
            ];
        } catch (MPApiException $e) {
            Log::error('MercadoPago Credit Card Payment Error: ' . $e->getMessage(), [
                'response' => $e->getApiResponse()
            ]);
            return [
                'success' => false,
                'message' => 'Erro ao processar pagamento com cartão: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('MercadoPago Credit Card Payment Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao processar pagamento com cartão: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Processa notificação do webhook
     *
     * @param array $data
     * @return bool
     */
    public function processNotification($data)
    {
        try {
            Log::info('MercadoPago Notification', $data);
            
            // Verifica o tipo de notificação
            if ($data['type'] === 'payment') {
                $paymentId = $data['data']['id'];
                
                // Na SDK v3, usamos o PaymentClient para obter detalhes do pagamento
                $paymentClient = new PaymentClient();
                $payment = $paymentClient->get($paymentId);
                
                if (!$payment) {
                    Log::error('MercadoPago: Pagamento não encontrado', ['payment_id' => $paymentId]);
                    return false;
                }
                
                // Buscar o pedido usando o external_reference ou outro identificador
                $orderId = $payment->external_reference;
                $order = Order::find($orderId);
                
                if (!$order) {
                    Log::error('MercadoPago: Pedido não encontrado', ['order_id' => $orderId]);
                    return false;
                }
                
                // Atualiza o status do pedido com base no status do pagamento
                $this->updateOrderStatus($order, $payment->status);
                
                return true;
            } 
            // Webhook de assinatura
            elseif ($data['type'] === 'subscription_preapproval') {
                return $this->handleWebhook($data);
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('MercadoPago Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza o status do pedido
     *
     * @param Order $order
     * @param string $paymentStatus
     * @return void
     */
    protected function updateOrderStatus(Order $order, $paymentStatus)
    {
        switch ($paymentStatus) {
            case 'approved':
                $order->payment_status = 'approved';
                $order->status = 'processing';
                break;
            case 'pending':
                $order->payment_status = 'pending';
                $order->status = 'pending';
                break;
            case 'in_process':
                $order->payment_status = 'in_process';
                $order->status = 'pending';
                break;
            case 'rejected':
                $order->payment_status = 'rejected';
                $order->status = 'failed';
                break;
            case 'cancelled':
                $order->payment_status = 'cancelled';
                $order->status = 'cancelled';
                break;
            case 'refunded':
                $order->payment_status = 'refunded';
                $order->status = 'refunded';
                break;
            default:
                $order->payment_status = $paymentStatus;
                break;
        }
        
        $order->save();
    }

    /**
     * Retorna a mensagem correspondente ao status do pagamento
     *
     * @param string $status
     * @return string
     */
    private function getStatusMessage($status)
    {
        $messages = [
            'approved' => 'Pagamento aprovado!',
            'pending' => 'Aguardando confirmação do pagamento.',
            'in_process' => 'Pagamento em análise.',
            'rejected' => 'Pagamento recusado.',
            'cancelled' => 'Pagamento cancelado.',
            'refunded' => 'Pagamento devolvido.',
        ];
        
        return $messages[$status] ?? 'Status desconhecido: ' . $status;
    }

    public function createSubscription(SubscriptionPackage $package, $user)
    {
        try {
            $subscriptionClient = new SubscriptionClient();
            
            $subscriptionData = [
                'reason' => "Assinatura - {$package->name}",
                'auto_recurring' => [
                    "frequency" => 1,
                    "frequency_type" => "months",
                    "transaction_amount" => (float) $package->price,
                    "currency_id" => "BRL"
                ],
                'payer' => [
                    "email" => $user->email,
                    "first_name" => $user->name
                ],
                'back_url' => route('subscriptions.callback')
            ];
            
            $subscription = $subscriptionClient->createSubscription($this->accessToken, $subscriptionData);
            
            Log::info('Assinatura criada com sucesso', [
                'subscription_id' => $subscription['id'],
                'user_id' => $user->id,
                'package_id' => $package->id
            ]);
            
            return [
                'success' => true,
                'subscription_id' => $subscription['id'],
                'external_reference' => $subscription['id'],
                'init_point' => $subscription['init_point']
            ];
        } catch (MPApiException $e) {
            $errorDetails = $e->getApiResponse() ? $e->getApiResponse()->getContent() : null;
            
            Log::error('Erro na API do MercadoPago ao criar assinatura', [
                'exception' => $e->getMessage(),
                'api_response' => $errorDetails
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao criar assinatura no MercadoPago: ' . $e->getMessage(),
                'details' => $errorDetails
            ];
        } catch (\Exception $e) {
            Log::error('MercadoPago Subscription Error', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao criar assinatura no MercadoPago: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cancela uma assinatura no MercadoPago
     *
     * @param string $subscriptionId
     * @return array
     */
    public function cancelSubscription($subscriptionId)
    {
        try {
            Log::info('Cancelando assinatura no MercadoPago', ['subscription_id' => $subscriptionId]);
            
            // Usar SubscriptionClient da SDK v3
            $subscriptionClient = new SubscriptionClient();
            $result = $subscriptionClient->cancel($subscriptionId);
            
            Log::info('Assinatura cancelada com sucesso', ['subscription_id' => $subscriptionId]);
            
            return [
                'success' => true,
                'message' => 'Assinatura cancelada com sucesso',
                'data' => $result
            ];
        } catch (MPApiException $e) {
            Log::error('Erro ao cancelar assinatura via SDK', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
                'api_response' => $e->getApiResponse()
            ]);
            
            // Tentar cancelar via API HTTP direta como fallback
            return $this->cancelSubscriptionViaHttp($subscriptionId);
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar assinatura', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            
            // Tentar cancelar via API HTTP direta como fallback
            return $this->cancelSubscriptionViaHttp($subscriptionId);
        }
    }

    /**
     * Cancela uma assinatura no MercadoPago via HTTP direto (fallback)
     *
     * @param string $subscriptionId
     * @return array
     */
    private function cancelSubscriptionViaHttp($subscriptionId)
    {
        try {
            Log::info('Tentando cancelar assinatura via HTTP direto', ['subscription_id' => $subscriptionId]);
            
            // Fallback para método HTTP direto caso o cliente SDK não funcione
            $url = "preapproval/{$subscriptionId}";
            
            // Dados para cancelamento
            $data = [
                'status' => 'cancelled'
            ];
            
            // Faz a requisição para o MercadoPago
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ])->put("https://api.mercadopago.com/v1/{$url}", $data);
            
            if ($response->successful()) {
                $result = $response->json();
                
                Log::info('Assinatura cancelada com sucesso via HTTP', [
                    'subscription_id' => $subscriptionId,
                    'response' => $result
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Assinatura cancelada com sucesso (HTTP)',
                    'data' => $result
                ];
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? 'Erro desconhecido ao cancelar assinatura';
                
                Log::error('Erro ao cancelar assinatura no MercadoPago via HTTP', [
                    'subscription_id' => $subscriptionId,
                    'status_code' => $response->status(),
                    'response' => $errorData
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'details' => $errorData
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao cancelar assinatura via HTTP', [
                'subscription_id' => $subscriptionId,
                'exception' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao processar cancelamento via HTTP: ' . $e->getMessage()
            ];
        }
    }

    public function handleWebhook($data)
    {
        try {
            Log::info('Processando webhook do MercadoPago', [
                'data' => $data
            ]);
            
            if ($data['type'] === 'subscription_preapproval') {
                $subscriptionClient = new SubscriptionClient();
                $subscriptionId = $data['data']['id'];
                
                try {
                    $mpSubscription = $subscriptionClient->getSubscription($this->accessToken, $subscriptionId);
                    
                    // Procura a assinatura por external_reference ou mercadopago_subscription_id
                    $subscription = Subscription::where('external_reference', $subscriptionId)
                        ->orWhere('mercadopago_subscription_id', $subscriptionId)
                        ->first();
                    
                    if ($subscription) {
                        switch ($mpSubscription['status']) {
                            case 'authorized':
                                $subscription->update(['status' => 'active']);
                                break;
                            case 'cancelled':
                                $subscription->update([
                                    'status' => 'cancelled',
                                    'cancelled_at' => now()
                                ]);
                                break;
                            case 'paused':
                                $subscription->update(['status' => 'suspended']);
                                break;
                            default:
                                Log::info('Status de assinatura não tratado', [
                                    'subscription_id' => $subscriptionId,
                                    'status' => $mpSubscription['status']
                                ]);
                        }
                        
                        Log::info('Assinatura atualizada via webhook', [
                            'subscription_id' => $subscription->id,
                            'mercadopago_id' => $subscriptionId,
                            'status' => $subscription->status
                        ]);
                    } else {
                        Log::warning('Assinatura não encontrada no sistema', [
                            'mercadopago_id' => $subscriptionId
                        ]);
                    }
                } catch (MPApiException $e) {
                    Log::error('Erro na API do MercadoPago ao processar webhook', [
                        'subscription_id' => $subscriptionId,
                        'exception' => $e->getMessage(),
                        'api_response' => $e->getApiResponse() ? $e->getApiResponse()->getContent() : null
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => 'Erro ao processar webhook: ' . $e->getMessage()
                    ];
                }
            }

            return [
                'success' => true
            ];
        } catch (\Exception $e) {
            Log::error('MercadoPago Webhook Error', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao processar webhook do MercadoPago: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Configura o SDK do MercadoPago
     */
    private function configureSdk()
    {
        // Configuração global do SDK v3
        MercadoPagoConfig::setAccessToken($this->accessToken);
        
        // Define o ID de integrador se disponível
        $integratorId = config('services.mercadopago.integrator_id', '');
        if (!empty($integratorId)) {
            MercadoPagoConfig::setIntegratorId($integratorId);
        }
        
        // O modo sandbox é automaticamente ativado quando se usa um
        // TEST Access Token, como estamos fazendo. Não precisa chamar setSandboxMode.
        
        // Na versão 3 do SDK, os clientes específicos (PreferenceClient, SubscriptionClient, etc.)
        // são instanciados quando necessários, não precisamos de um MercadoPagoClient genérico.
        // Removendo essa inicialização que está causando o erro.
    }

    private function getItems(Order $order)
    {
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'id' => $item->id,
                'title' => mb_substr($item->product->name, 0, 250), // Limita para evitar erro de string muito longa
                'description' => mb_substr($item->product->description ?? 'Produto', 0, 250),
                'quantity' => $item->quantity,
                'currency_id' => 'BRL',
                'unit_price' => (float) $item->price
            ];
        }
        return $items;
    }

    private function getShippingAndTaxItems(Order $order)
    {
        $items = [];
        if ($order->shipping_cost > 0) {
            $items[] = [
                'id' => 'shipping',
                'title' => 'Frete',
                'description' => 'Valor do frete',
                'quantity' => 1,
                'currency_id' => 'BRL',
                'unit_price' => (float) $order->shipping_cost
            ];
        }
        if ($order->tax > 0) {
            $items[] = [
                'id' => 'tax',
                'title' => 'Impostos',
                'description' => 'Taxas',
                'quantity' => 1,
                'currency_id' => 'BRL',
                'unit_price' => (float) $order->tax
            ];
        }
        return $items;
    }
}
