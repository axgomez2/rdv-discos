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

class CheckoutController extends Controller
{
    protected $mercadoPagoService;
    protected $pagSeguroService;
    protected $systemSettings;

    public function __construct(
        MercadoPagoService $mercadoPagoService,
        PagSeguroService $pagSeguroService,
        SystemSettingsService $systemSettings
    ) {
        $this->mercadoPagoService = $mercadoPagoService;
        $this->pagSeguroService = $pagSeguroService;
        $this->systemSettings = $systemSettings;
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
        $tax = round($subtotal * 0.1, 2); // 10% de imposto, arredondado para 2 casas decimais
        $total = $subtotal + $shippingCost + $tax;

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
            'tax', 
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
            'shipping_address_id' => 'required|exists:addresses,id',
            'payment_gateway' => ['required', 'string', Rule::in(['mercadopago', 'pagseguro'])],
            'payment_method' => ['required', 'string', Rule::in(['credit_card', 'boleto', 'pix'])],
            'notes' => 'nullable|string|max:500',
            // Validações específicas para cartão de crédito
            'card_token' => 'required_if:payment_method,credit_card|string',
            'installments' => 'required_if:payment_method,credit_card|integer|min:1|max:12',
            'card_holder_name' => 'required_if:payment_method,credit_card|string|max:255',
            'card_holder_cpf' => [
                'required_if:payment_method,credit_card',
                'string',
                'regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$|^\d{11}$/',
            ],
        ], [
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
            $tax = round($subtotal * 0.1, 2); // 10% de imposto
            $total = $subtotal + $shippingCost + $tax;

            // Validar valor total
            if ($total <= 0) {
                throw new \Exception("Valor total do pedido inválido.");
            }

            // Criar pedido
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total' => $total,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_address_id' => $request->shipping_address_id,
                'billing_address_id' => $request->shipping_address_id, // Usando o mesmo endereço para faturamento
                'notes' => strip_tags($request->notes) ?? null // Sanitizar entrada
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
            $order->save();

            // Limpar o carrinho
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            // Redirecionar com base no método de pagamento
            if ($request->payment_method === 'boleto' && isset($paymentResult['boleto_url'])) {
                session()->flash('boleto_url', $paymentResult['boleto_url']);
                return redirect()->route('site.payments.boleto', ['order' => $order->id])
                    ->with('success', 'Pedido realizado com sucesso! Efetue o pagamento do boleto para completar a compra.');
            } else if ($request->payment_method === 'pix' && isset($paymentResult['qr_code_url'])) {
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
        if ($request->payment_method === 'credit_card') {
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
                    case 'credit_card':
                        return $this->mercadoPagoService->processCreditCardPayment($order, $cardData);
                    case 'boleto':
                        return $this->mercadoPagoService->processBoletoPayment($order);
                    case 'pix':
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
                    case 'credit_card':
                        return $this->pagSeguroService->processCreditCardPayment($order, $cardData);
                    case 'boleto':
                        return $this->pagSeguroService->processBoletoPayment($order);
                    case 'pix':
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
}
