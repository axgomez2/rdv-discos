<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestShippingWithRestrictions extends Command
{
    protected $signature = 'shipping:test-with-restrictions';
    protected $description = 'Test shipping calculation with carrier restrictions';

    public function handle()
    {
        $this->info('Testing shipping calculation with carrier restrictions...');

        $token = config('services.melhorenvio.token');
        $baseUrl = 'https://sandbox.melhorenvio.com.br/';

        // Test payload following Correios PAC restrictions:
        // - Max weight: 30kg
        // - Min dimensions: 11cm width, 2cm height, 16cm length
        // - Max dimensions: 100cm width, 100cm height, 100cm length
        // - Sum of dimensions must not exceed 200cm
        $payload = [
            'from' => [
                'postal_code' => '01044000'
            ],
            'to' => [
                'postal_code' => '01001000'
            ],
            'products' => [
                [
                    'id' => '1',
                    'width' => 35, // Standard LP width
                    'height' => 35, // Standard LP height
                    'length' => 3,  // Package thickness
                    'weight' => 0.3, // 300g in kg
                    'insurance_value' => 100,
                    'quantity' => 1
                ]
            ]
        ];

        try {
            $this->info('Making request to Melhor Envio API...');
            $this->info('URL: ' . $baseUrl . 'api/v2/me/shipment/calculate');

            // Validate dimensions
            $totalDimensions = $payload['products'][0]['width'] +
                             $payload['products'][0]['height'] +
                             $payload['products'][0]['length'];

            $this->info('Package dimensions:');
            $this->info("Width: {$payload['products'][0]['width']}cm");
            $this->info("Height: {$payload['products'][0]['height']}cm");
            $this->info("Length: {$payload['products'][0]['length']}cm");
            $this->info("Total dimensions: {$totalDimensions}cm (max 200cm)");
            $this->info("Weight: {$payload['products'][0]['weight']}kg (max 30kg)");

            if ($totalDimensions > 200) {
                throw new \Exception('Total dimensions exceed maximum allowed (200cm)');
            }

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
            ])->post($baseUrl . 'api/v2/me/shipment/calculate', $payload);

            $this->info('Response Status: ' . $response->status());

            if ($response->successful()) {
                $this->info('Response Body:');
                $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));
                $this->info('Test successful!');
                return 0;
            } else {
                $this->error('Test failed!');
                $this->error('Response Body:');
                $this->error($response->body());

                // Try to get more information about the error
                $error = $response->json();
                if (isset($error['message'])) {
                    $this->error('Error Message: ' . $error['message']);
                }
                if (isset($error['errors'])) {
                    $this->error('Validation Errors:');
                    foreach ($error['errors'] as $field => $messages) {
                        $this->error("  $field: " . implode(', ', (array)$messages));
                    }
                }
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Shipping test failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
