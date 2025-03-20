<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentNotificationController extends Controller
{
    protected $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    /**
     * Processa a notificação do Mercado Pago
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleMercadoPagoWebhook(Request $request)
    {
        Log::info('Notificação Mercado Pago recebida', $request->all());

        // Validação da origem da notificação (implementação de segurança)
        $ipAddress = $request->ip();
        $allowedIPs = config('services.mercadopago.webhook_ips', []);
        
        // Se houver IPs permitidos configurados, verifica se o IP está na lista
        if (!empty($allowedIPs) && !in_array($ipAddress, $allowedIPs)) {
            Log::warning('Tentativa de notificação de IP não autorizado', [
                'ip' => $ipAddress,
                'data' => $request->all()
            ]);
            return response('Unauthorized', 403);
        }

        // Valida a assinatura webhook se estiver configurada
        $webhookSecret = config('services.mercadopago.webhook_secret');
        if ($webhookSecret) {
            $signature = $request->header('X-Signature');
            $payload = $request->getContent();
            $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
            
            if ($signature !== $expectedSignature) {
                Log::warning('Assinatura webhook inválida', [
                    'received' => $signature,
                    'expected' => $expectedSignature
                ]);
                return response('Invalid signature', 403);
            }
        }

        $result = $this->mercadoPagoService->processNotification($request->all());

        if ($result) {
            return response('Webhook processado com sucesso', 200);
        }

        return response('Erro ao processar webhook', 400);
    }
}
