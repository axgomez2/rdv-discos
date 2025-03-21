<?php

namespace App\Services;

use Artistas\PagSeguro\PagSeguro;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\SystemSettingsService;

class PagSeguroService
{
    protected $pagSeguro;
    protected $systemSettings;
    protected $enabled;

    public function __construct(SystemSettingsService $systemSettings)
    {
        $this->systemSettings = $systemSettings;
        $this->enabled = (bool)$this->systemSettings->get('payment', 'pagseguro_enabled', false);
        
        // Configurar o PagSeguro com base nas configurações do banco de dados
        $config = [
            'email' => $this->systemSettings->get('payment', 'pagseguro_email', ''),
            'token' => $this->systemSettings->get('payment', 'pagseguro_token', ''),
            'sandbox' => (bool)$this->systemSettings->get('payment', 'pagseguro_sandbox', true),
        ];
        
        if (empty($config['email']) || empty($config['token'])) {
            Log::warning('PagSeguro: Credenciais não configuradas');
        } else {
            // Inicializa o PagSeguro com as configurações do banco de dados
            $this->pagSeguro = new PagSeguro($config);
        }
    }
    
    /**
     * Verifica se o serviço está habilitado
     * 
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled && $this->pagSeguro !== null;
    }

    /**
     * Prepara os dados para a sessão do PagSeguro
     *
     * @return string
     */
    public function getSessionId()
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            return $this->pagSeguro->startSession();
        } catch (\Exception $e) {
            Log::error('Erro ao iniciar sessão do PagSeguro: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Processa o pagamento com cartão de crédito
     *
     * @param Order $order
     * @param array $cardData
     * @return array
     */
    public function processCreditCardPayment(Order $order, array $cardData)
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'PagSeguro não habilitado',
            ];
        }

        try {
            $user = $order->user;
            $address = $order->shippingAddress;

            $paymentData = [
                'items' => $this->getItemsForPayment($order),
                'shipping' => [
                    'address' => [
                        'street' => $address->street,
                        'number' => $address->number,
                        'complement' => $address->complement ?? '',
                        'district' => $address->neighborhood,
                        'city' => $address->city,
                        'state' => $address->state,
                        'postal_code' => str_replace(['-', '.'], '', $address->zip_code),
                    ],
                    'type' => 1, // Frete PAC
                    'cost' => number_format($order->shipping_cost, 2, '.', ''),
                ],
                'sender' => [
                    'email' => $user->email,
                    'name' => $user->name,
                    'documents' => [
                        [
                            'type' => 'CPF',
                            'number' => preg_replace('/[^0-9]/', '', $user->cpf),
                        ],
                    ],
                    'phone' => [
                        'area_code' => substr(preg_replace('/[^0-9]/', '', $user->phone), 0, 2),
                        'number' => substr(preg_replace('/[^0-9]/', '', $user->phone), 2),
                    ],
                ],
                'billing_address' => [
                    'street' => $address->street,
                    'number' => $address->number,
                    'complement' => $address->complement ?? '',
                    'district' => $address->neighborhood,
                    'city' => $address->city,
                    'state' => $address->state,
                    'postal_code' => str_replace(['-', '.'], '', $address->zip_code),
                ],
                'credit_card' => [
                    'token' => $cardData['token'],
                    'installment' => [
                        'quantity' => $cardData['installments'],
                        'value' => $this->calculateInstallmentValue($order->total, $cardData['installments']),
                    ],
                    'holder' => [
                        'name' => $cardData['holder'],
                        'documents' => [
                            [
                                'type' => 'CPF',
                                'number' => preg_replace('/[^0-9]/', '', $cardData['cpf']),
                            ],
                        ],
                        'birth_date' => $cardData['birth_date'],
                        'phone' => [
                            'area_code' => substr(preg_replace('/[^0-9]/', '', $user->phone), 0, 2),
                            'number' => substr(preg_replace('/[^0-9]/', '', $user->phone), 2),
                        ],
                    ],
                ],
                'reference' => $order->id,
                'notification_url' => config('pagseguro.notificationURL'),
            ];

            $result = $this->pagSeguro->creditCardCheckout($paymentData, true);

            return [
                'success' => true,
                'transaction_code' => $result['code'],
                'status' => $result['status'],
                'message' => $this->getStatusMessage($result['status']),
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento com cartão de crédito: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao processar pagamento: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Processa o pagamento com boleto
     *
     * @param Order $order
     * @return array
     */
    public function processBoletoPayment(Order $order)
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'PagSeguro não habilitado',
            ];
        }

        try {
            $user = $order->user;
            $address = $order->shippingAddress;

            $paymentData = [
                'items' => $this->getItemsForPayment($order),
                'shipping' => [
                    'address' => [
                        'street' => $address->street,
                        'number' => $address->number,
                        'complement' => $address->complement ?? '',
                        'district' => $address->neighborhood,
                        'city' => $address->city,
                        'state' => $address->state,
                        'postal_code' => str_replace(['-', '.'], '', $address->zip_code),
                    ],
                    'type' => 1, // Frete PAC
                    'cost' => number_format($order->shipping_cost, 2, '.', ''),
                ],
                'sender' => [
                    'email' => $user->email,
                    'name' => $user->name,
                    'documents' => [
                        [
                            'type' => 'CPF',
                            'number' => preg_replace('/[^0-9]/', '', $user->cpf),
                        ],
                    ],
                    'phone' => [
                        'area_code' => substr(preg_replace('/[^0-9]/', '', $user->phone), 0, 2),
                        'number' => substr(preg_replace('/[^0-9]/', '', $user->phone), 2),
                    ],
                ],
                'reference' => $order->id,
                'notification_url' => config('pagseguro.notificationURL'),
            ];

            $result = $this->pagSeguro->boletoCheckout($paymentData, true);

            return [
                'success' => true,
                'transaction_code' => $result['code'],
                'status' => $result['status'],
                'message' => $this->getStatusMessage($result['status']),
                'boleto_url' => $result['payment_link'],
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento com boleto: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao processar pagamento: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Processa o pagamento com PIX
     *
     * @param Order $order
     * @return array
     */
    public function processPixPayment(Order $order)
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'PagSeguro não habilitado',
            ];
        }

        try {
            $user = $order->user;
            $address = $order->shippingAddress;

            $paymentData = [
                'items' => $this->getItemsForPayment($order),
                'shipping' => [
                    'address' => [
                        'street' => $address->street,
                        'number' => $address->number,
                        'complement' => $address->complement ?? '',
                        'district' => $address->neighborhood,
                        'city' => $address->city,
                        'state' => $address->state,
                        'postal_code' => str_replace(['-', '.'], '', $address->zip_code),
                    ],
                    'type' => 1, // Frete PAC
                    'cost' => number_format($order->shipping_cost, 2, '.', ''),
                ],
                'sender' => [
                    'email' => $user->email,
                    'name' => $user->name,
                    'documents' => [
                        [
                            'type' => 'CPF',
                            'number' => preg_replace('/[^0-9]/', '', $user->cpf),
                        ],
                    ],
                    'phone' => [
                        'area_code' => substr(preg_replace('/[^0-9]/', '', $user->phone), 0, 2),
                        'number' => substr(preg_replace('/[^0-9]/', '', $user->phone), 2),
                    ],
                ],
                'reference' => $order->id,
                'notification_url' => config('pagseguro.notificationURL'),
            ];

            // Nota: O pacote pode não ter suporte nativo para PIX, mas adicionamos caso seja implementado no futuro
            // No momento, isso pode requerer uma implementação personalizada ou uso da API direta do PagSeguro
            $result = $this->pagSeguro->pixCheckout($paymentData, true);

            return [
                'success' => true,
                'transaction_code' => $result['code'],
                'status' => $result['status'],
                'message' => $this->getStatusMessage($result['status']),
                'qr_code' => $result['qr_code'] ?? null,
                'qr_code_url' => $result['qr_code_url'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento com PIX: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao processar pagamento: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Processa a notificação de pagamento do PagSeguro
     *
     * @param Request $request
     * @return bool
     */
    public function processNotification(Request $request)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        try {
            $response = $this->pagSeguro->notification($request->notificationCode, $request->notificationType);

            if ($response) {
                $reference = $response['reference'];
                $status = $response['status'];

                $order = Order::find($reference);
                if ($order) {
                    $order->payment_status = $this->mapPagSeguroStatus($status);
                    $order->transaction_code = $response['code'];
                    $order->payment_method = $response['paymentMethod']['type'];
                    $order->save();

                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Erro ao processar notificação do PagSeguro: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Formata os itens para o formato do PagSeguro
     *
     * @param Order $order
     * @return array
     */
    protected function getItemsForPayment(Order $order)
    {
        $items = [];

        foreach ($order->items as $index => $item) {
            $items[] = [
                'id' => $item->product_id,
                'description' => $item->product->name ?? "Produto #" . $item->product_id,
                'quantity' => $item->quantity,
                'amount' => number_format($item->price, 2, '.', ''),
                'weight' => 1000, // Peso em gramas
            ];
        }

        return $items;
    }

    /**
     * Calcula o valor da parcela
     *
     * @param float $total
     * @param int $installments
     * @return string
     */
    protected function calculateInstallmentValue($total, $installments)
    {
        return number_format($total / $installments, 2, '.', '');
    }

    /**
     * Mapeia o status do PagSeguro para o sistema
     *
     * @param int $pagSeguroStatus
     * @return string
     */
    protected function mapPagSeguroStatus($pagSeguroStatus)
    {
        $statusMap = [
            1 => 'pending',  // Aguardando pagamento
            2 => 'analyzing', // Em análise
            3 => 'paid',     // Paga
            4 => 'available', // Disponível
            5 => 'disputed',  // Em disputa
            6 => 'refunded',  // Devolvida
            7 => 'cancelled', // Cancelada
            8 => 'debited',   // Debitado
            9 => 'withheld',  // Retenção temporária
        ];

        return $statusMap[$pagSeguroStatus] ?? 'unknown';
    }

    /**
     * Retorna a mensagem de status do pagamento
     *
     * @param int $status
     * @return string
     */
    protected function getStatusMessage($status)
    {
        $messages = [
            1 => 'Aguardando pagamento',
            2 => 'Em análise',
            3 => 'Pagamento aprovado',
            4 => 'Pagamento disponível',
            5 => 'Em disputa',
            6 => 'Pagamento devolvido',
            7 => 'Pagamento cancelado',
            8 => 'Debitado',
            9 => 'Retenção temporária',
        ];

        return $messages[$status] ?? 'Status desconhecido';
    }
}
