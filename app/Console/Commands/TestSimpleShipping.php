<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestSimpleShipping extends Command
{
    protected $signature = 'shipping:test-simple';
    protected $description = 'Test shipping calculation with a simple payload';

    public function handle()
    {
        $this->info('Testing simple shipping calculation...');

        $token = config('services.melhorenvio.token');
        $baseUrl = 'https://sandbox.melhorenvio.com.br/';

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
                    'width' => 35,
                    'height' => 35,
                    'length' => 3,
                    'weight' => 0.3,
                    'insurance_value' => 100,
                    'quantity' => 1
                ]
            ]
        ];

        try {
            $this->info('Making request to Melhor Envio API...');
            $this->info('URL: ' . $baseUrl . 'api/v2/me/shipment/calculate');
            $this->info('Token length: ' . strlen($token));
            $this->info('Token starts with: ' . substr($token, 0, 20) . '...');

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => $token
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
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Simple shipping test failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
