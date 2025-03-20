<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;
use App\Services\ShippingService;

class VerifyShippingImplementation extends Command
{
    protected $signature = 'shipping:verify';
    protected $description = 'Verify the final shipping implementation';

    public function handle()
    {
        $this->info('Verifying shipping implementation...');

        // Get a cart with items
        $cart = Cart::with(['items.product.productable.vinylSec.dimension', 'items.product.productable.vinylSec.weight'])
            ->whereHas('items')
            ->first();

        if (!$cart) {
            $this->error('No cart with items found');
            return 1;
        }

        $this->info("Found cart with " . $cart->items->count() . " items");

        // Show cart items details
        foreach ($cart->items as $item) {
            $vinylSec = $item->product->productable->vinylSec;
            $this->info("\nItem: " . $item->product->productable->title);
            $this->info("- Dimension: " . ($vinylSec->dimension ? "Yes (ID: {$vinylSec->dimension_id})" : "No"));
            $this->info("- Weight: " . ($vinylSec->weight ? "Yes (ID: {$vinylSec->weight_id})" : "No"));

            if ($vinylSec->dimension) {
                $this->info("  Dimensions: {$vinylSec->dimension->width}x{$vinylSec->dimension->height}x{$vinylSec->dimension->depth} {$vinylSec->dimension->unit}");
            }
            if ($vinylSec->weight) {
                $this->info("  Weight: {$vinylSec->weight->value} {$vinylSec->weight->unit}");
            }
        }

        // Test shipping calculation
        $this->info("\nTesting shipping calculation...");
        $shippingService = new ShippingService();
        $result = $shippingService->calculateShipping($cart->items, '01001000');

        if ($result['error']) {
            $this->error("Error: " . $result['message']);
            return 1;
        }

        $this->info("\nShipping options:");
        foreach ($result['options'] as $option) {
            $this->info("- {$option['company']['name']} {$option['name']}:");
            $this->info("  Price: R$ " . number_format($option['price'], 2, ',', '.'));
            $this->info("  Delivery time: {$option['delivery_time']} days");
        }

        return 0;
    }
}
