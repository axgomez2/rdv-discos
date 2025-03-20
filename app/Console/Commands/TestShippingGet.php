<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestShippingGet extends Command
{
    protected $signature = 'shipping:test-get';
    protected $description = 'Test shipping calculation with GET requests';

    public function handle()
    {
        $this->info('Testing shipping calculation with GET requests...');

        $token = config('services.melhorenvio.token');
        $baseUrl = 'https://sandbox.melhorenvio.com.br/';

        // Build query parameters
        $params = [
            'from' => '01044000',
            'to' => '01001000',
            'width' => 35,
            'height' => 35,
            'length' => 3,
            'weight' => 0.3,
            'insurance_value' => 100,
            'services' => '1,2', // PAC and SEDEX
        ];

        $endpoints = [
            'api/v2/calculate',
            'api/v2/shipping/calculate',
            'api/v2/calculator',
            'api/v2/me/shipment/calculate'
        ];

        foreach ($endpoints as $endpoint) {
            $this->info("\nTrying endpoint: " . $endpoint);

            try {
                $url = $baseUrl . $endpoint . '?' . http_build_query($params);
                $this->info('URL: ' . $url);

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
                ])->get($url);

                $this->info('Response Status: ' . $response->status());

                if ($response->successful()) {
                    $this->info('Success! Found working endpoint.');
                    $this->info('Response Body:');
                    $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));

                    // Save the working configuration
                    $this->info("\nWorking configuration found:");
                    $this->info("Endpoint: " . $endpoint);
                    $this->info("Method: GET");
                    $this->info("Parameters: " . json_encode($params, JSON_PRETTY_PRINT));

                    return 0;
                } else {
                    $this->error('Failed with status ' . $response->status());
                    $this->error('Response: ' . $response->body());
                }

            } catch (\Exception $e) {
                $this->error('Error with endpoint ' . $endpoint . ': ' . $e->getMessage());
            }
        }

        // Try alternative parameter formats
        $this->info("\nTrying with alternative parameter format...");

        $altParams = [
            'postal_code_from' => '01044000',
            'postal_code_to' => '01001000',
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

        foreach ($endpoints as $endpoint) {
            $this->info("\nTrying endpoint with alt params: " . $endpoint);

            try {
                $url = $baseUrl . $endpoint . '?' . http_build_query($altParams);
                $this->info('URL: ' . $url);

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
                ])->get($url);

                $this->info('Response Status: ' . $response->status());

                if ($response->successful()) {
                    $this->info('Success! Found working endpoint.');
                    $this->info('Response Body:');
                    $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));

                    // Save the working configuration
                    $this->info("\nWorking configuration found:");
                    $this->info("Endpoint: " . $endpoint);
                    $this->info("Method: GET");
                    $this->info("Parameters: " . json_encode($altParams, JSON_PRETTY_PRINT));

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
