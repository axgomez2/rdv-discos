<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestMelhorEnvioToken extends Command
{
    protected $signature = 'melhorenvio:test-token';
    protected $description = 'Test Melhor Envio token and show its information';

    public function handle()
    {
        $this->info('Testing Melhor Envio token...');

        $token = config('services.melhorenvio.token');
        $baseUrl = 'https://sandbox.melhorenvio.com.br/';

        try {
            // First, decode the JWT token to check its contents
            $tokenParts = explode('.', $token);
            if (count($tokenParts) === 3) {
                $payload = json_decode(base64_decode($tokenParts[1]), true);

                $this->info("\nToken information:");
                $this->info('Scopes: ' . implode(', ', $payload['scopes'] ?? []));
                $this->info('Expires: ' . date('Y-m-d H:i:s', $payload['exp']));
                $this->info('Subject: ' . ($payload['sub'] ?? 'Not found'));
            }

            // Test token with different endpoints
            $endpoints = [
                'api/v2/me' => 'GET',
                'api/v2/me/shipment/companies' => 'GET',
                'api/v2/me/shipment/calculate' => 'POST'
            ];

            foreach ($endpoints as $endpoint => $method) {
                $this->info("\nTesting endpoint: " . $endpoint);

                $headers = [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
                ];

                if ($method === 'GET') {
                    $response = Http::withHeaders($headers)->get($baseUrl . $endpoint);
                } else {
                    // For POST endpoints, send a minimal test payload
                    $payload = [
                        'from' => ['postal_code' => '01044000'],
                        'to' => ['postal_code' => '01001000'],
                        'products' => [[
                            'id' => '1',
                            'width' => 35,
                            'height' => 35,
                            'length' => 3,
                            'weight' => 0.3,
                            'insurance_value' => 100,
                            'quantity' => 1
                        ]]
                    ];
                    $response = Http::withHeaders($headers)->post($baseUrl . $endpoint, $payload);
                }

                $this->info('Response Status: ' . $response->status());
                $this->info('Response Headers:');
                foreach ($response->headers() as $name => $values) {
                    $this->info("  $name: " . implode(', ', $values));
                }

                if ($response->successful()) {
                    $this->info('Success!');
                    $this->info('Response Body:');
                    $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));
                } else {
                    $this->error('Failed!');
                    $this->error('Response Body:');
                    $this->error($response->body());
                }
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Token test failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
