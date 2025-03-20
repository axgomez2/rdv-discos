<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestShippingEndpoint extends Command
{
    protected $signature = 'shipping:test-endpoint';
    protected $description = 'Test different shipping calculation endpoints';

    public function handle()
    {
        $this->info('Testing shipping calculation endpoints...');

        $token = config('services.melhorenvio.token');
        $baseUrl = 'https://sandbox.melhorenvio.com.br/';

        $payload = [
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
                'own_hand' => false
            ],
            'services' => '1,2' // PAC and SEDEX
        ];

        $endpoints = [
            'api/v2/me/shipment/calculate',
            'api/v2/calculate',
            'api/v2/shipping/calculate',
            'api/v2/calculator'
        ];

        foreach ($endpoints as $endpoint) {
            $this->info("\nTrying endpoint: " . $endpoint);

            try {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
                ])->post($baseUrl . $endpoint, $payload);

                $this->info('Response Status: ' . $response->status());

                if ($response->successful()) {
                    $this->info('Success! Found working endpoint.');
                    $this->info('Response Body:');
                    $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));
                    return 0;
                } else {
                    $this->error('Failed with status ' . $response->status());
                    $this->error('Response: ' . $response->body());
                }

            } catch (\Exception $e) {
                $this->error('Error with endpoint ' . $endpoint . ': ' . $e->getMessage());
            }
        }

        // Try the same endpoints with a different payload format
        $this->info("\nTrying with alternative payload format...");

        $altPayload = [
            'from' => [
                'postal_code' => '01044000'
            ],
            'to' => [
                'postal_code' => '01001000'
            ],
            'products' => [
                [
                    'id' => 'vinyl-1',
                    'width' => 35,
                    'height' => 35,
                    'length' => 3,
                    'weight' => 0.3,
                    'insurance_value' => 100,
                    'quantity' => 1
                ]
            ]
        ];

        foreach ($endpoints as $endpoint) {
            $this->info("\nTrying endpoint with alt payload: " . $endpoint);

            try {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
                ])->post($baseUrl . $endpoint, $altPayload);

                $this->info('Response Status: ' . $response->status());

                if ($response->successful()) {
                    $this->info('Success! Found working endpoint.');
                    $this->info('Response Body:');
                    $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));
                    return 0;
                } else {
                    $this->error('Failed with status ' . $response->status());
                    $this->error('Response: ' . $response->body());
                }

            } catch (\Exception $e) {
                $this->error('Error with endpoint ' . $endpoint . ': ' . $e->getMessage());
            }
        }

        return 1;
    }
}
