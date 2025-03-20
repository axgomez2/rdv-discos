<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ShippingService;
use App\Models\Cart;
use Illuminate\Support\Facades\Log;

class TestShippingCalculation extends Command
{
    protected $signature = 'shipping:test {postal_code=01044000}';
    protected $description = 'Test shipping calculation with a specific postal code';

    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        parent::__construct();
        $this->shippingService = $shippingService;
    }

    public function handle()
    {
        $postalCode = $this->argument('postal_code');
        $this->info("Testing shipping calculation for postal code: {$postalCode}");

        // Get the first cart with items
        $cart = Cart::with(['items.product.productable.vinylSec.dimension', 'items.product.productable.vinylSec.weight'])
            ->whereHas('items')
            ->first();

        if (!$cart) {
            $this->error('No cart with items found');
            return 1;
        }

        $this->info("Found cart with " . $cart->items->count() . " items");

        // Log cart items details
        foreach ($cart->items as $item) {
            $vinylSec = $item->product->productable->vinylSec;
            $this->info("Item: " . $item->product->productable->title);
            $this->info("- Dimension: " . ($vinylSec->dimension ? "Yes (ID: {$vinylSec->dimension_id})" : "No"));
            $this->info("- Weight: " . ($vinylSec->weight ? "Yes (ID: {$vinylSec->weight_id})" : "No"));

            if ($vinylSec->dimension) {
                $this->info("  Dimensions: {$vinylSec->dimension->width}x{$vinylSec->dimension->height}x{$vinylSec->dimension->depth} {$vinylSec->dimension->unit}");
            }
            if ($vinylSec->weight) {
                $this->info("  Weight: {$vinylSec->weight->value} {$vinylSec->weight->unit}");
            }
        }

        try {
            $result = $this->shippingService->calculateShipping($cart->items, $postalCode);

            if (!empty($result['error'])) {
                $this->error("Error calculating shipping: " . $result['message']);
                return 1;
            }

            $this->info("Shipping calculation successful!");
            $this->table(
                ['Company', 'Service', 'Price', 'Delivery Time'],
                collect($result['options'])->map(function ($option) {
                    return [
                        $option['company']['name'],
                        $option['name'],
                        'R$ ' . number_format($option['price'], 2, ',', '.'),
                        $option['delivery_time'] . ' dias'
                    ];
                })
            );

        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
