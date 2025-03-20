<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestShippingFinalV2 extends Command
{
    protected $signature = 'shipping:test-final-v2';
    protected $description = 'Final test for shipping calculation with AJAX headers';

    public function handle()
    {
        $this->info('Final shipping calculation test with AJAX headers...');

        $token = config('services.melhorenvio.token');
        $baseUrl = 'https://sandbox.melhorenvio.com.br/';

        $params = [
            'from' => [
                'postal_code' => '01044000'
            ],
            'to' => [
                'postal_code' => '01001000'
            ],
            'package' => [
                'width' => 35,
                'height' => 35,
                'length' => 3,
                'weight' => 0.3
            ],
            'options' => [
                'insurance_value' => 100,
                'receipt' => false,
                'own_hand' => false,
                'collect' => false
            ],
            'services' => '1,2'
        ];

        $this->info('Testing with AJAX headers...');

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'X-Requested-With' => 'XMLHttpRequest',
                'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
            ])->post($baseUrl . 'api/v2/me/shipment/calculate', $params);

            $this->info('Response Status: ' . $response->status());
            $this->info('Response Headers:');
            foreach ($response->headers() as $name => $values) {
                $this->info("$name: " . implode(', ', $values));
            }

            if ($response->successful()) {
                $this->info('Success!');
                $this->info('Response Body:');
                $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));
            } else {
                $this->error('Failed!');
                $this->error('Response Body:');
                $this->error($response->body());

                // Try alternative endpoint
                $this->info("\nTrying alternative endpoint...");

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'X-Requested-With' => 'XMLHttpRequest',
                    'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
                ])->post($baseUrl . 'api/v2/shipping/calculate', $params);

                $this->info('Response Status: ' . $response->status());
                if ($response->successful()) {
                    $this->info('Success with alternative endpoint!');
                    $this->info('Response Body:');
                    $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));
                } else {
                    $this->error('Alternative endpoint failed!');
                    $this->error('Response Body:');
                    $this->error($response->body());
                }
            }

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }

        return 0;
    }
}
