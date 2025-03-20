<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestMelhorEnvioAuth2 extends Command
{
    protected $signature = 'melhorenvio:test-auth2';
    protected $description = 'Test Melhor Envio authentication with different methods';

    public function handle()
    {
        $this->info('Testing Melhor Envio authentication...');

        $token = config('services.melhorenvio.token');
        $baseUrl = 'https://sandbox.melhorenvio.com.br/';

        // Test 1: Basic endpoint with different auth formats
        $this->info("\nTest 1: Testing companies endpoint with different auth formats");
        $authFormats = [
            'Bearer with space' => 'Bearer ' . $token,
            'Bearer without space' => 'Bearer' . $token,
            'Raw token' => $token
        ];

        foreach ($authFormats as $type => $auth) {
            $this->info("\nTrying $type...");

            try {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => $auth
                ])->get($baseUrl . 'api/v2/me/shipment/companies');

                $this->info('Response Status: ' . $response->status());
                if ($response->successful()) {
                    $this->info('Success with ' . $type);

                    // If this format works, try shipping calculation
                    $this->info("\nTrying shipping calculation with same auth format...");

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
                            'own_hand' => false,
                            'collect' => false
                        ],
                        'services' => '1,2'
                    ];

                    $calcResponse = Http::withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => $auth
                    ])->post($baseUrl . 'api/v2/me/shipment/calculate', $payload);

                    $this->info('Calculation Response Status: ' . $calcResponse->status());
                    $this->info('Response Body:');
                    $this->info($calcResponse->body());

                    if ($calcResponse->successful()) {
                        $this->info('Found working configuration!');
                        $this->info('Auth Format: ' . $type);
                        $this->info('Endpoint: api/v2/me/shipment/calculate');
                        $this->info('Method: POST');
                        $this->info('Payload Format:');
                        $this->info(json_encode($payload, JSON_PRETTY_PRINT));
                        return 0;
                    }
                }
            } catch (\Exception $e) {
                $this->error('Error with ' . $type . ': ' . $e->getMessage());
            }
        }

        // Test 2: Try with URL token
        $this->info("\nTest 2: Testing with URL token");
        try {
            $response = Http::get($baseUrl . 'api/v2/me/shipment/companies?token=' . $token);
            $this->info('Response Status: ' . $response->status());
            if ($response->successful()) {
                $this->info('Success with URL token');
            }
        } catch (\Exception $e) {
            $this->error('Error with URL token: ' . $e->getMessage());
        }

        return 1;
    }
}
