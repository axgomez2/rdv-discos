<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Services\MercadoPagoService;
use App\Services\MelhorEnvioService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $mercadoPagoService;
    protected $melhorEnvioService;

    public function __construct(
        MercadoPagoService $mercadoPagoService,
        MelhorEnvioService $melhorEnvioService
    ) {
        $this->mercadoPagoService = $mercadoPagoService;
        $this->melhorEnvioService = $melhorEnvioService;
    }

    public function index()
    {
        $packages = SubscriptionPackage::where('is_active', true)->get();
        $currentSubscription = auth()->user()->subscriptions()
            ->with('package')
            ->where('status', Subscription::STATUS_ACTIVE)
            ->first();

        return view('site.subscriptions.index', compact('packages', 'currentSubscription'));
    }

    public function subscribe(SubscriptionPackage $package)
    {
        // Check if user already has an active subscription
        $activeSubscription = auth()->user()->subscriptions()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->first();

        if ($activeSubscription) {
            return redirect()->back()
                ->with('error', 'Você já possui uma assinatura ativa.');
        }

        // Create subscription in pending status
        $subscription = Subscription::create([
            'user_id' => auth()->id(),
            'package_id' => $package->id,
            'status' => Subscription::STATUS_PENDING
        ]);

        // Create MercadoPago subscription
        $result = $this->mercadoPagoService->createSubscription($package, auth()->user());

        if (!$result['success']) {
            $subscription->update(['status' => Subscription::STATUS_FAILED]);
            return redirect()->back()
                ->with('error', $result['message']);
        }

        // Update subscription with MercadoPago ID
        $subscription->update([
            'mercadopago_subscription_id' => $result['subscription_id']
        ]);

        // Redirect to MercadoPago checkout
        return redirect($result['init_point']);
    }

    public function callback(Request $request)
    {
        $subscription = Subscription::where('mercadopago_subscription_id', $request->preapproval_id)->first();

        if (!$subscription) {
            return redirect()->route('subscriptions.index')
                ->with('error', 'Assinatura não encontrada.');
        }

        if ($request->status === 'authorized') {
            $subscription->update([
                'status' => Subscription::STATUS_ACTIVE,
                'next_billing_date' => now()->addMonth()
            ]);

            return redirect()->route('subscriptions.index')
                ->with('success', 'Assinatura ativada com sucesso!');
        }

        return redirect()->route('subscriptions.index')
            ->with('error', 'Erro ao processar assinatura.');
    }

    public function cancel()
    {
        $subscription = auth()->user()->subscriptions()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->first();

        if (!$subscription) {
            return redirect()->back()
                ->with('error', 'Nenhuma assinatura ativa encontrada.');
        }

        $result = $this->mercadoPagoService->cancelSubscription($subscription->mercadopago_subscription_id);

        if (!$result['success']) {
            return redirect()->back()
                ->with('error', $result['message']);
        }

        $subscription->cancel();

        return redirect()->route('subscriptions.index')
            ->with('success', 'Assinatura cancelada com sucesso.');
    }

    public function webhook(Request $request)
    {
        $result = $this->mercadoPagoService->handleWebhook($request->all());

        if (!$result['success']) {
            return response()->json(['error' => $result['message']], 400);
        }

        return response()->json(['message' => 'Webhook processado com sucesso']);
    }
}
