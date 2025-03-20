<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestShippingFinal extends Command
{
    protected $signature = 'shipping:test-final';
    protected $description = 'Final test for shipping calculation';

    public function handle()
    {
        $this->info('Final shipping calculation test...');

        $token = config('services.melhorenvio.token');
        $baseUrl = 'https://sandbox.melhorenvio.com.br/';

        // Test 1: With company and service IDs
        $this->info("\nTest 1: With company and service IDs");
        $params1 = [
            'postal_code_from' => '01044000',
            'postal_code_to' => '01001000',
            'width' => 35,
            'height' => 35,
            'length' => 3,
            'weight' => 0.3,
            'insurance_value' => 100,
            'company' => '1', // Correios
            'service' => '1,2' // PAC and SEDEX
        ];

        $this->testRequest($baseUrl . 'api/v2/calculate', $token, $params1);

        // Test 2: With dimensions in millimeters and weight in grams
        $this->info("\nTest 2: With dimensions in millimeters and weight in grams");
        $params2 = [
            'postal_code_from' => '01044000',
            'postal_code_to' => '01001000',
            'width' => 350,
            'height' => 350,
            'length' => 30,
            'weight' => 300,
            'insurance_value' => 100,
            'company' => '1',
            'service' => '1,2'
        ];

        $this->testRequest($baseUrl . 'api/v2/calculate', $token, $params2);

        // Test 3: With all possible parameters
        $this->info("\nTest 3: With all possible parameters");
        $params3 = [
            'postal_code_from' => '01044000',
            'postal_code_to' => '01001000',
            'width' => 35,
            'height' => 35,
            'length' => 3,
            'weight' => 0.3,
            'insurance_value' => 100,
            'company' => '1',
            'service' => '1,2',
            'own_hand' => false,
            'receipt' => false,
            'collect' => false,
            'invoice' => [
                'key' => '31190307586261000184550010000092481404848162'
            ],
            'platform' => 'Embaixada da Dance Music',
            'volumes' => 1,
            'units' => 'cm'
        ];

        $this->testRequest($baseUrl . 'api/v2/calculate', $token, $params3);

        // Test 4: With query string parameters
        $this->info("\nTest 4: With query string parameters");
        $queryString = http_build_query([
            'postal_code_from' => '01044000',
            'postal_code_to' => '01001000',
            'width' => 35,
            'height' => 35,
            'length' => 3,
            'weight' => 0.3,
            'company' => '1',
            'service' => '1,2'
        ]);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->get($baseUrl . 'api/v2/calculate?' . $queryString);

        $this->info('Response Status: ' . $response->status());
        $this->info('Response Body:');
        $this->info($response->body());

        return 0;
    }

    protected function testRequest($url, $token, $params)
    {
        $this->info('Parameters: ' . json_encode($params, JSON_PRETTY_PRINT));

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
            ])->get($url, $params);

            $this->info('Response Status: ' . $response->status());
            $this->info('Response Headers:');
            foreach ($response->headers() as $name => $values) {
                $this->info("$name: " . implode(', ', $values));
            }

            if ($response->successful()) {
                $this->info('Success!');
                $this->info('Response Body:');
                $this->info($response->body());

                if ($response->json() === null) {
                    $this->warn('Response is null, trying to debug request...');
                    $this->info('Full URL with query string:');
                    $this->info($url . '?' . http_build_query($params));
                }
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
