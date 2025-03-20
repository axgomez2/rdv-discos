<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestShippingSimple extends Command
{
    protected $signature = 'shipping:test-simple';
    protected $description = 'Simple test for shipping calculation';

    public function handle()
    {
        $this->info('Testing shipping calculation with simple parameters...');

        $token = config('services.melhorenvio.token');
        $baseUrl = 'https://sandbox.melhorenvio.com.br/';

        // Simple parameters as query string
        $params = [
            'cep_origem' => '01044000',
            'cep_destino' => '01001000',
            'produtos' => [
                [
                    'largura' => 35,
                    'altura' => 35,
                    'comprimento' => 3,
                    'peso' => 0.3,
                    'valor' => 100,
                    'quantidade' => 1
                ]
            ]
        ];

        try {
            $this->info('Making request...');
            $this->info('URL: ' . $baseUrl . 'api/v2/calculate');
            $this->info('Parameters: ' . json_encode($params, JSON_PRETTY_PRINT));

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'X-Requested-With' => 'XMLHttpRequest'
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

                // Try with flattened parameters
                $this->info("\nTrying with flattened parameters...");

                $flatParams = [
                    'cep_origem' => '01044000',
                    'cep_destino' => '01001000',
                    'largura' => 35,
                    'altura' => 35,
                    'comprimento' => 3,
                    'peso' => 0.3,
                    'valor' => 100,
                    'quantidade' => 1,
                    'servicos' => '1,2'
                ];

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'X-Requested-With' => 'XMLHttpRequest'
                ])->get($baseUrl . 'api/v2/calculate', $flatParams);

                $this->info('Response Status: ' . $response->status());
                if ($response->successful()) {
                    $this->info('Success with flattened parameters!');
                    $this->info('Response Body:');
                    $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));
                } else {
                    $this->error('Flattened parameters failed!');
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
