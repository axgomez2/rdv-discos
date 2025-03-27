<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Services\MercadoPagoService;
use App\Services\PagSeguroService;
use App\Services\SystemSettingsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\MelhorEnvioService;
use App\Models\Address;

class CheckoutController extends Controller
{
    protected $mercadoPagoService;
    protected $pagSeguroService;
    protected $systemSettings;
    protected $melhorEnvioService;

    public function __construct(
        MercadoPagoService $mercadoPagoService,
        PagSeguroService $pagSeguroService,
        SystemSettingsService $systemSettings,
        MelhorEnvioService $melhorEnvioService
    ) {
        $this->mercadoPagoService = $mercadoPagoService;
        $this->pagSeguroService = $pagSeguroService;
        $this->systemSettings = $systemSettings;
        $this->melhorEnvioService = $melhorEnvioService;
    }

    public function index(Request $request)
    {
        $cart = $request->user()->cart ?? Cart::where('session_id', session()->getId())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('site.cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        // Calcular valores com validação adicional
        $subtotal = $this->calculateSubtotal($cart);
        $shippingCost = (float)session('shipping_cost', 0);
        $total = $subtotal + $shippingCost;

        // Configurações de pagamento disponíveis
        $paymentGateways = $this->getAvailablePaymentGateways();
        
        // Dados de gateways para o frontend
        $mercadoPagoEnabled = $this->mercadoPagoService->isEnabled();
        $pagSeguroEnabled = $this->pagSeguroService->isEnabled();
        
        // Dados dos gateways para configurar o frontend
        $mercadoPagoPublicKey = $mercadoPagoEnabled ? $this->mercadoPagoService->getPublicKey() : '';
        $pagSeguroSessionId = $pagSeguroEnabled ? $this->pagSeguroService->getSessionId() : '';

        return view('site.checkout.index', compact(
            'cart', 
            'subtotal', 
            'shippingCost', 
            'total', 
            'paymentGateways',
            'mercadoPagoEnabled',
            'pagSeguroEnabled',
            'mercadoPagoPublicKey',
            'pagSeguroSessionId'
        ));
    }

    /**
     * Obtém os gateways de pagamento habilitados
     * 
     * @return array
     */
    private function getAvailablePaymentGateways()
    {
        $gateways = [];
        
        // MercadoPago
        if ($this->mercadoPagoService->isEnabled()) {
            $gateways['mercadopago'] = [
                'name' => 'MercadoPago',
                'methods' => [
                    'credit_card' => 'Cartão de Crédito',
                    'boleto' => 'Boleto',
                    'pix' => 'PIX'
                ]
            ];
        }
        
        // PagSeguro
        if ($this->pagSeguroService->isEnabled()) {
            $gateways['pagseguro'] = [
                'name' => 'PagSeguro', 
                'methods' => [
                    'credit_card' => 'Cartão de Crédito',
                    'boleto' => 'Boleto',
                    'pix' => 'PIX'
                ]
            ];
        }
        
        return $gateways;
    }

    public function process(Request $request)
    {
        // Validação rigorosa dos dados de entrada
        $validator = Validator::make($request->all(), [
            'endereco' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|size:2',
            'cep' => 'required|string|size:9',
            'shipping_option' => 'required|string',
            'payment_gateway' => ['required', 'string', Rule::in(['mercadopago', 'pagseguro'])],
            'payment_method' => ['required', 'string', Rule::in([
                'pagseguro_credit_card', 'pagseguro_boleto', 'pagseguro_pix',
                'mercadopago_credit_card', 'mercadopago_boleto', 'mercadopago_pix'
            ])],
            'notes' => 'nullable|string|max:500',
            // Validações específicas para cartão de crédito
            'card_token' => 'required_if:payment_method,pagseguro_credit_card,mercadopago_credit_card|string',
            'installments' => 'required_if:payment_method,pagseguro_credit_card,mercadopago_credit_card|integer|min:1|max:12',
            'card_holder_name' => 'required_if:payment_method,pagseguro_credit_card,mercadopago_credit_card|string|max:255',
            'card_holder_cpf' => [
                'required_if:payment_method,pagseguro_credit_card,mercadopago_credit_card',
                'string',
                'regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$|^\d{11}$/',
            ],
        ], [
            'endereco.required' => 'O endereço é obrigatório.',
            'numero.required' => 'O número é obrigatório.',
            'bairro.required' => 'O bairro é obrigatório.',
            'cidade.required' => 'A cidade é obrigatória.',
            'estado.required' => 'O estado é obrigatório.',
            'cep.required' => 'O CEP é obrigatório.',
            'shipping_option.required' => 'Selecione uma opção de envio.',
            'payment_gateway.required' => 'Selecione um gateway de pagamento.',
            'payment_gateway.in' => 'O gateway de pagamento selecionado não é válido.',
            'card_token.required_if' => 'O token do cartão é obrigatório para pagamento com cartão de crédito.',
            'installments.required_if' => 'O número de parcelas é obrigatório para pagamento com cartão de crédito.',
            'installments.min' => 'O número mínimo de parcelas é 1.',
            'installments.max' => 'O número máximo de parcelas é 12.',
            'card_holder_name.required_if' => 'O nome do titular do cartão é obrigatório.',
            'card_holder_cpf.required_if' => 'O CPF do titular do cartão é obrigatório.',
            'card_holder_cpf.regex' => 'O CPF do titular do cartão está em formato inválido.',
            'payment_method.in' => 'O método de pagamento selecionado não é válido.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('site.checkout.index')
                ->withErrors($validator)
                ->withInput();
        }

        $cart = $request->user()->cart ?? Cart::where('session_id', session()->getId())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('site.cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        DB::beginTransaction();

        try {
            // Verificar estoque final com validação adicional
            foreach ($cart->items as $item) {
                $vinylSec = $item->product->productable->vinylSec;
                
                if (!$vinylSec) {
                    throw new \Exception("Produto inválido no carrinho: {$item->product->name}");
                }
                
                if (!$vinylSec->in_stock || $vinylSec->quantity < $item->quantity) {
                    throw new \Exception("Estoque insuficiente para o produto: {$item->product->name}");
                }
            }

            // Calcular valores com validação
            $subtotal = $this->calculateSubtotal($cart);
            $shippingCost = (float)session('shipping_cost', 0);
            $total = $subtotal + $shippingCost;

            // Validar valor total
            if ($total <= 0) {
                throw new \Exception("Valor total do pedido inválido.");
            }

            // Criar endereço a partir do formulário
            $address = Address::create([
                'user_id' => $request->user()->id,
                'street' => $request->endereco,
                'number' => $request->numero,
                'complement' => $request->complemento,
                'neighborhood' => $request->bairro,
                'city' => $request->cidade,
                'state' => $request->estado,
                'zip_code' => $request->cep,
                'is_default' => false,
            ]);

            // Criar pedido
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total' => $total,
                'shipping_cost' => $shippingCost,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_address_id' => $address->id,
                'billing_address_id' => $address->id, // Usando o mesmo endereço para faturamento
                'notes' => strip_tags($request->notes) ?? null, // Sanitizar entrada
                'shipping_method' => $request->shipping_option,
                'shipping_data' => json_encode(session('shipping_data')),
            ]);

            // Criar itens do pedido e atualizar estoque
            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $vinylSec = $item->product->productable->vinylSec;
                $vinylSec->quantity -= $item->quantity;
                $vinylSec->save();
            }

            // Processar pagamento de acordo com o gateway e método selecionados
            $paymentResult = $this->processPayment($request, $order);

            if (!$paymentResult['success']) {
                throw new \Exception($paymentResult['message']);
            }

            // Atualizar pedido com informações do pagamento
            $order->transaction_code = $paymentResult['transaction_code'] ?? null;
            $order->payment_method = $request->payment_method;
            $order->payment_gateway = $request->payment_gateway;
            
            // Gerar etiqueta de envio com o Melhor Envio se o pagamento for aprovado
            // ou se a opção de envio vier do Melhor Envio
            if (($paymentResult['status'] === 'approved' || $paymentResult['status'] === 'pending') && 
                in_array($request->shipping_option, ['pac', 'sedex', 'mini', 'express']) && 
                $this->melhorEnvioService->isEnabled()) {
                
                try {
                    // Formatar dados para geração da etiqueta
                    $items = $order->items->map(function ($item) {
                        $product = $item->product;
                        return [
                            'id' => $product->id,
                            'width' => $product->width ?? 11,
                            'height' => $product->height ?? 4,
                            'length' => $product->length ?? 16,
                            'weight' => $product->weight ?? 0.3,
                            'insurance_value' => $product->price,
                            'quantity' => $item->quantity
                        ];
                    })->toArray();
                    
                    // Dados do destinatário
                    $to = [
                        'name' => $request->user()->name,
                        'phone' => $request->user()->phone ?? '11999999999',
                        'email' => $request->user()->email,
                        'address' => $request->endereco,
                        'number' => $request->numero,
                        'complement' => $request->complemento,
                        'district' => $request->bairro,
                        'city' => $request->cidade,
                        'state' => $request->estado,
                        'postal_code' => preg_replace('/[^0-9]/', '', $request->cep)
                    ];
                    
                    // Gerar etiqueta
                    $shippingLabel = $this->melhorEnvioService->generateShippingLabel(
                        $items, 
                        $to, 
                        $request->shipping_option,
                        $order->id
                    );
                    
                    if ($shippingLabel['success']) {
                        $order->shipping_label_code = $shippingLabel['tracking_code'] ?? null;
                        $order->shipping_label_url = $shippingLabel['label_url'] ?? null;
                        $order->shipping_tracking_url = $shippingLabel['tracking_url'] ?? null;
                    } else {
                        Log::warning('Falha ao gerar etiqueta de envio: ' . ($shippingLabel['message'] ?? 'Erro desconhecido'));
                    }
                } catch (\Exception $e) {
                    Log::error('Erro ao gerar etiqueta de envio: ' . $e->getMessage());
                    // Não impedir a finalização do pedido se a etiqueta falhar
                }
            }
            
            $order->save();

            // Limpar o carrinho
            $cart->items()->delete();
            $cart->delete();
            
            // Limpar dados de frete da sessão
            session()->forget(['shipping_cost', 'shipping_method', 'shipping_data']);

            DB::commit();

            // Redirecionar com base no método de pagamento
            if ($request->payment_method === 'pagseguro_boleto' && isset($paymentResult['boleto_url'])) {
                session()->flash('boleto_url', $paymentResult['boleto_url']);
                return redirect()->route('site.payments.boleto', ['order' => $order->id])
                    ->with('success', 'Pedido realizado com sucesso! Efetue o pagamento do boleto para completar a compra.');
            } else if ($request->payment_method === 'pagseguro_pix' && isset($paymentResult['qr_code_url'])) {
                session()->flash('qr_code_url', $paymentResult['qr_code_url']);
                return redirect()->route('site.payments.pix', ['order' => $order->id])
                    ->with('success', 'Pedido realizado com sucesso! Escaneie o QR Code para completar a compra.');
            } else if ($request->payment_method === 'mercadopago_boleto' && isset($paymentResult['boleto_url'])) {
                session()->flash('boleto_url', $paymentResult['boleto_url']);
                return redirect()->route('site.payments.boleto', ['order' => $order->id])
                    ->with('success', 'Pedido realizado com sucesso! Efetue o pagamento do boleto para completar a compra.');
            } else if ($request->payment_method === 'mercadopago_pix' && isset($paymentResult['qr_code_url'])) {
                session()->flash('qr_code_url', $paymentResult['qr_code_url']);
                return redirect()->route('site.payments.pix', ['order' => $order->id])
                    ->with('success', 'Pedido realizado com sucesso! Escaneie o QR Code para completar a compra.');
            } else {
                return redirect()->route('site.orders.show', $order)
                    ->with('success', 'Pedido realizado com sucesso! ' . ($paymentResult['message'] ?? ''));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro no checkout: ' . $e->getMessage());
            return redirect()->route('site.checkout.index')->with('error', 'Erro ao processar o pagamento: ' . $e->getMessage());
        }
    }
    
    /**
     * Processa o pagamento através do gateway selecionado
     *
     * @param Request $request
     * @param Order $order
     * @return array
     */
    private function processPayment(Request $request, Order $order)
    {
        // Sanitiza os dados do cartão se for pagamento por cartão de crédito
        $cardData = [];
        if ($request->payment_method === 'pagseguro_credit_card' || $request->payment_method === 'mercadopago_credit_card') {
            $cardData = [
                'token' => $request->card_token,
                'installments' => (int)$request->installments,
                'cpf' => preg_replace('/[^0-9]/', '', $request->card_holder_cpf),
                'name' => $request->card_holder_name,
            ];
        }
        
        // Processa o pagamento de acordo com o gateway selecionado
        switch ($request->payment_gateway) {
            case 'mercadopago':
                // Verifica se o gateway está habilitado
                if (!$this->mercadoPagoService->isEnabled()) {
                    return [
                        'success' => false,
                        'message' => 'O gateway MercadoPago não está habilitado.',
                    ];
                }
                
                // Processa o pagamento de acordo com o método
                switch ($request->payment_method) {
                    case 'mercadopago_credit_card':
                        return $this->mercadoPagoService->processCreditCardPayment($order, $cardData);
                    case 'mercadopago_boleto':
                        return $this->mercadoPagoService->processBoletoPayment($order);
                    case 'mercadopago_pix':
                        return $this->mercadoPagoService->processPixPayment($order);
                    default:
                        return [
                            'success' => false,
                            'message' => 'Método de pagamento não suportado pelo MercadoPago.',
                        ];
                }
                
            case 'pagseguro':
                // Verifica se o gateway está habilitado
                if (!$this->pagSeguroService->isEnabled()) {
                    return [
                        'success' => false,
                        'message' => 'O gateway PagSeguro não está habilitado.',
                    ];
                }
                
                // Processa o pagamento de acordo com o método
                switch ($request->payment_method) {
                    case 'pagseguro_credit_card':
                        return $this->pagSeguroService->processCreditCardPayment($order, $cardData);
                    case 'pagseguro_boleto':
                        return $this->pagSeguroService->processBoletoPayment($order);
                    case 'pagseguro_pix':
                        return $this->pagSeguroService->processPixPayment($order);
                    default:
                        return [
                            'success' => false,
                            'message' => 'Método de pagamento não suportado pelo PagSeguro.',
                        ];
                }
                
            default:
                return [
                    'success' => false,
                    'message' => 'Gateway de pagamento não suportado.',
                ];
        }
    }

    /**
     * Calcula o subtotal do carrinho de forma segura
     *
     * @param Cart $cart
     * @return float
     */
    private function calculateSubtotal(Cart $cart)
    {
        $subtotal = 0;
        
        foreach ($cart->items as $item) {
            $price = (float)$item->product->price;
            $quantity = (int)$item->quantity;
            
            // Validação adicional
            if ($price < 0 || $quantity <= 0) {
                continue; // Ignora itens com valores inválidos
            }
            
            $subtotal += $price * $quantity;
        }
        
        return round($subtotal, 2);
    }

    /**
     * Página de agradecimento após pagamento bem-sucedido
     */
    public function success(Request $request, Order $order)
    {
        // Verifica se o pedido pertence ao usuário atual
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado');
        }

        // Atualiza o status do pedido para processando se estiver pendente
        if ($order->status === 'pending' && $order->payment_status === 'pending') {
            $order->payment_status = 'approved';
            $order->status = 'processing';
            $order->save();
        }

        return view('site.checkout.success', compact('order'));
    }

    /**
     * Página para pagamentos pendentes
     */
    public function pending(Request $request, Order $order)
    {
        // Verifica se o pedido pertence ao usuário atual
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado');
        }

        return view('site.checkout.pending', compact('order'));
    }

    /**
     * Página para pagamentos que falharam
     */
    public function failure(Request $request, Order $order)
    {
        // Verifica se o pedido pertence ao usuário atual
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado');
        }

        // Atualiza o status do pedido para falha se estiver pendente
        if ($order->status === 'pending' && $order->payment_status === 'pending') {
            $order->payment_status = 'rejected';
            $order->status = 'failed';
            $order->save();
        }

        return view('site.checkout.failure', compact('order'));
    }

    /**
     * Calcula as opções de frete disponíveis com base no CEP e nos itens do carrinho
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calcularFrete(Request $request)
    {
        // Validar os dados recebidos
        $validator = Validator::make($request->all(), [
            'cep' => 'required|string|size:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Obter o carrinho do usuário ou da sessão
            $cart = $request->user() ? $request->user()->cart : Cart::where('session_id', session()->getId())->first();
            
            if (!$cart || $cart->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Carrinho vazio',
                ], 400);
            }
            
            // Formatar os itens do carrinho para o cálculo
            $items = $cart->items->map(function ($item) {
                $product = $item->product;
                return [
                    'id' => $product->id,
                    'width' => $product->width ?? 11,
                    'height' => $product->height ?? 4,
                    'length' => $product->length ?? 16,
                    'weight' => $product->weight ?? 0.3,
                    'insurance_value' => $product->price,
                    'quantity' => $item->quantity
                ];
            })->toArray();
            
            // Calcular frete com o Melhor Envio
            $shippingOptions = $this->melhorEnvioService->calculateShipping($items, $request->cep);
            
            // Se não houver opções de frete do Melhor Envio, verificar outras opções
            if (empty($shippingOptions)) {
                // Tentar serviço dos Correios diretamente se estiver disponível
                $correiosEnabled = $this->systemSettings->get('shipping', 'correios_enabled', 'false') === 'true';
                
                if ($correiosEnabled) {
                    // Chamar serviço dos Correios diretamente (implementação simplificada)
                    // Na implementação real, você usaria um CorreiosService similar ao MelhorEnvioService
                    $shippingOptions = $this->getDefaultCorreiosShippingOptions();
                } else {
                    // Retornar opções de frete padrão caso nenhum serviço esteja disponível
                    $shippingOptions = $this->getDefaultShippingOptions();
                }
            }
            
            // Caso estejamos no carrinho, salvar a opção selecionada na sessão
            if ($request->has('selected_method') && isset($shippingOptions[$request->selected_method])) {
                session(['shipping_cost' => $shippingOptions[$request->selected_method]['price']]);
                session(['shipping_method' => $request->selected_method]);
                session(['shipping_data' => [
                    'method' => $request->selected_method,
                    'name' => $shippingOptions[$request->selected_method]['name'],
                    'price' => $shippingOptions[$request->selected_method]['price'],
                    'delivery_time' => $shippingOptions[$request->selected_method]['delivery_time'],
                ]]);
            }
            
            return response()->json([
                'success' => true,
                'options' => $shippingOptions
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao calcular o frete. Tente novamente mais tarde.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Retorna opções padrão de frete dos Correios quando o cálculo online falha
     * 
     * @return array
     */
    private function getDefaultCorreiosShippingOptions()
    {
        return [
            'pac' => [
                'name' => 'PAC',
                'price' => 25.90,
                'delivery_time' => 10,
                'company' => 'Correios',
                'id' => 'pac'
            ],
            'sedex' => [
                'name' => 'SEDEX',
                'price' => 45.90,
                'delivery_time' => 3,
                'company' => 'Correios',
                'id' => 'sedex'
            ]
        ];
    }
    
    /**
     * Retorna opções padrão de frete quando nenhum serviço está disponível
     * 
     * @return array
     */
    private function getDefaultShippingOptions()
    {
        return [
            'standard' => [
                'name' => 'Entrega Padrão',
                'price' => 19.90,
                'delivery_time' => 7,
                'company' => 'Loja',
                'id' => 'standard'
            ],
            'express' => [
                'name' => 'Entrega Expressa',
                'price' => 34.90,
                'delivery_time' => 2,
                'company' => 'Loja',
                'id' => 'express'
            ]
        ];
    }
}
