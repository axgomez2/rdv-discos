<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestShippingV4 extends Command
{
    protected $signature = 'shipping:test-v4';
    protected $description = 'Test shipping calculation with correct parameters';

    public function handle()
    {
        $this->info('Testing shipping calculation with correct parameters...');

        $baseUrl = 'https://sandbox.melhorenvio.com.br/';

        // Simple flat parameters
        $params = [
            'from' => '01044000',
            'to' => '01001000',
            'width' => 35,
            'height' => 35,
            'length' => 3,
            'weight' => 0.3,
            'insurance_value' => 100,
            'services' => '1,2'
        ];

        try {
            $this->info('Making request...');
            $this->info('URL: ' . $baseUrl . 'api/v2/calculator');
            $this->info('Parameters: ' . json_encode($params, JSON_PRETTY_PRINT));

            $queryString = http_build_query($params);
            $fullUrl = $baseUrl . 'api/v2/calculator?' . $queryString;

            $this->info('Full URL: ' . $fullUrl);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
            ])->get($fullUrl);

            $this->info('Response Status: ' . $response->status());

            if ($response->successful()) {
                $this->info('Success!');
                $this->info('Response Body:');
                $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));
            } else {
                $this->error('Failed!');
                $this->error('Response Body:');
                $this->error($response->body());

                // Try with postal_code parameters
                $this->info("\nTrying with postal_code parameters...");

                $params2 = [
                    'from' => [
                        'postal_code' => '01044000'
                    ],
                    'to' => [
                        'postal_code' => '01001000'
                    ],
                    'width' => 35,
                    'height' => 35,
                    'length' => 3,
                    'weight' => 0.3,
                    'insurance_value' => 100,
                    'services' => '1,2'
                ];

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
                ])->get($baseUrl . 'api/v2/calculator', $params2);

                $this->info('Response Status: ' . $response->status());
                if ($response->successful()) {
                    $this->info('Success with postal_code parameters!');
                    $this->info('Response Body:');
                    $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));
                } else {
                    $this->error('postal_code parameters failed!');
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
