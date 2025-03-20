<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;
use App\Services\ShippingService;
use Illuminate\Support\Facades\Http;

class TestShippingFinalCheck extends Command
{
    protected $signature = 'shipping:final-check';
    protected $description = 'Final check of shipping calculation';

    public function handle()
    {
        $this->info('Final check of shipping calculation...');

        // First test direct API call
        $this->testDirectApi();

        // Then test through service
        $this->testService();

        return 0;
    }

    protected function testDirectApi()
    {
        $this->info("\nTesting direct API call...");

        $params = [
            'from' => '01044000',
            'to' => '01001000',
            'width' => 35,
            'height' => 35,
            'length' => 3,
            'weight' => 0.3,
            'insurance_value' => 100,
            'services' => '1,2'
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
        ])->get('https://sandbox.melhorenvio.com.br/api/v2/calculator', $params);

        if ($response->successful()) {
            $this->info('Direct API call successful!');
            $this->info('Response:');
            $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));
        } else {
            $this->error('Direct API call failed!');
            $this->error($response->body());
        }
    }

    protected function testService()
    {
        $this->info("\nTesting through ShippingService...");

        // Get a cart with items
        $cart = Cart::with(['items.product.productable.vinylSec.dimension', 'items.product.productable.vinylSec.weight'])
            ->whereHas('items')
            ->first();

        if (!$cart) {
            $this->error('No cart with items found');
            return;
        }

        $this->info("Found cart with " . $cart->items->count() . " items");

        // Show cart items details
        foreach ($cart->items as $item) {
            $vinylSec = $item->product->productable->vinylSec;
            $this->info("\nItem: " . $item->product->productable->title);
            if ($vinylSec->dimension) {
                $this->info("  Dimensions: {$vinylSec->dimension->width}x{$vinylSec->dimension->height}x{$vinylSec->dimension->depth} {$vinylSec->dimension->unit}");
            }
            if ($vinylSec->weight) {
                $this->info("  Weight: {$vinylSec->weight->value} {$vinylSec->weight->unit}");
            }
        }

        // Test shipping calculation
        $shippingService = new ShippingService();
        $result = $shippingService->calculateShipping($cart->items, '01001000');

        if (isset($result['error']) && $result['error']) {
            $this->error("Service error: " . $result['message']);
        } else {
            $this->info("\nShipping options:");
            foreach ($result['options'] as $option) {
                $this->info("- {$option['company']['name']} {$option['name']}:");
                $this->info("  Price: R$ " . number_format($option['price'], 2, ',', '.'));
                $this->info("  Delivery time: {$option['delivery_time']} days");
            }
        }
    }
}
