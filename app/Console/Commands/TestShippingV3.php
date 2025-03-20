<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestShippingV3 extends Command
{
    protected $signature = 'shipping:test-v3';
    protected $description = 'Test shipping calculation with v3 approach';

    public function handle()
    {
        $this->info('Testing shipping calculation with v3 approach...');

        $token = config('services.melhorenvio.token');
        $baseUrl = 'https://sandbox.melhorenvio.com.br/';

        // Test 1: Simple format
        $this->info("\nTest 1: Simple format");
        $params = [
            'from' => '01044000',
            'to' => '01001000',
            'width' => 35,
            'height' => 35,
            'length' => 3,
            'weight' => 0.3,
            'insurance_value' => 100
        ];

        $this->testRequest($baseUrl . 'api/v2/calculate', $token, $params, 'GET');

        // Test 2: Nested format with GET
        $this->info("\nTest 2: Nested format with GET");
        $params2 = [
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
                'insurance_value' => 100
            ]
        ];

        $this->testRequest($baseUrl . 'api/v2/calculate', $token, $params2, 'GET');

        // Test 3: Simple format with POST
        $this->info("\nTest 3: Simple format with POST");
        $this->testRequest($baseUrl . 'api/v2/calculate', $token, $params, 'POST');

        // Test 4: Nested format with POST
        $this->info("\nTest 4: Nested format with POST");
        $this->testRequest($baseUrl . 'api/v2/calculate', $token, $params2, 'POST');

        // Test 5: Alternative endpoint
        $this->info("\nTest 5: Alternative endpoint");
        $this->testRequest($baseUrl . 'api/v2/shipping/calculate', $token, $params, 'GET');

        return 0;
    }

    protected function testRequest($url, $token, $params, $method)
    {
        $this->info("URL: $url");
        $this->info("Method: $method");
        $this->info("Parameters: " . json_encode($params, JSON_PRETTY_PRINT));

        try {
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
            ];

            if ($method === 'GET') {
                $response = Http::withHeaders($headers)->get($url, $params);
            } else {
                $response = Http::withHeaders($headers)->post($url, $params);
            }

            $this->info('Response Status: ' . $response->status());

            if ($response->successful()) {
                $this->info('Success!');
                $this->info('Response Body:');
                $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));

                // If we got a successful response, save the working configuration
                $this->info("\nWorking configuration found!");
                $this->info("URL: $url");
                $this->info("Method: $method");
                $this->info("Parameters format:");
                $this->info(json_encode($params, JSON_PRETTY_PRINT));
            } else {
                $this->error('Failed!');
                $this->error('Response Body:');
                $this->error($response->body());
            }

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
