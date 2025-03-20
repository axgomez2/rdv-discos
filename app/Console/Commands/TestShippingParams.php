<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestShippingParams extends Command
{
    protected $signature = 'shipping:test-params';
    protected $description = 'Test different parameter combinations for shipping calculation';

    public function handle()
    {
        $this->info('Testing shipping calculation parameters...');

        $token = config('services.melhorenvio.token');
        $baseUrl = 'https://sandbox.melhorenvio.com.br/';

        // Test case 1: Minimal parameters
        $this->info("\nTest 1: Minimal parameters");
        $params1 = [
            'from' => '01044000',
            'to' => '01001000',
            'width' => 35,
            'height' => 35,
            'length' => 3,
            'weight' => 0.3
        ];
        $this->testRequest($baseUrl, $token, $params1);

        // Test case 2: Full parameters
        $this->info("\nTest 2: Full parameters");
        $params2 = [
            'from' => '01044000',
            'to' => '01001000',
            'width' => 35,
            'height' => 35,
            'length' => 3,
            'weight' => 0.3,
            'insurance_value' => 100,
            'services' => '1,2',
            'receipt' => false,
            'own_hand' => false,
            'collect' => false
        ];
        $this->testRequest($baseUrl, $token, $params2);

        // Test case 3: Alternative parameter names
        $this->info("\nTest 3: Alternative parameter names");
        $params3 = [
            'postal_code_from' => '01044000',
            'postal_code_to' => '01001000',
            'width' => 35,
            'height' => 35,
            'length' => 3,
            'weight' => 0.3,
            'insurance_value' => 100,
            'services' => '1,2'
        ];
        $this->testRequest($baseUrl, $token, $params3);

        // Test case 4: Weight in grams
        $this->info("\nTest 4: Weight in grams");
        $params4 = [
            'postal_code_from' => '01044000',
            'postal_code_to' => '01001000',
            'width' => 35,
            'height' => 35,
            'length' => 3,
            'weight' => 300, // 300g instead of 0.3kg
            'insurance_value' => 100,
            'services' => '1,2'
        ];
        $this->testRequest($baseUrl, $token, $params4);

        return 0;
    }

    protected function testRequest($baseUrl, $token, $params)
    {
        $this->info('Parameters: ' . json_encode($params, JSON_PRETTY_PRINT));

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
            ])->get($baseUrl . 'api/v2/calculate', $params);

            $this->info('Response Status: ' . $response->status());

            if ($response->successful()) {
                $this->info('Success!');
                $this->info('Response Body:');
                $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));
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
