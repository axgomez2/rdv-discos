<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestPublicCalculator extends Command
{
    protected $signature = 'shipping:test-public';
    protected $description = 'Test public shipping calculator';

    public function handle()
    {
        $this->info('Testing public shipping calculator...');

        $baseUrl = 'https://sandbox.melhorenvio.com.br/';

        // Test 1: Basic parameters
        $this->info("\nTest 1: Basic parameters");
        $params = [
            'cep_origem' => '01044000',
            'cep_destino' => '01001000',
            'largura' => 35,
            'altura' => 35,
            'comprimento' => 3,
            'peso' => 0.3,
            'valor_declarado' => 100,
            'servicos' => '1,2'
        ];

        $this->testRequest($baseUrl . 'api/v2/calculator', $params);

        // Test 2: With company filter
        $this->info("\nTest 2: With company filter");
        $params['empresa'] = 1; // Correios
        $this->testRequest($baseUrl . 'api/v2/calculator', $params);

        // Test 3: With package type
        $this->info("\nTest 3: With package type");
        $params['tipo_entrega'] = 'package';
        $this->testRequest($baseUrl . 'api/v2/calculator', $params);

        return 0;
    }

    protected function testRequest($url, $params)
    {
        try {
            $this->info('URL: ' . $url);
            $this->info('Parameters: ' . json_encode($params, JSON_PRETTY_PRINT));

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->get($url, $params);

            $this->info('Response Status: ' . $response->status());

            if ($response->successful()) {
                $this->info('Success!');
                $this->info('Response Body:');
                $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));

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
