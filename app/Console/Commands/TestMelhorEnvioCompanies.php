<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestMelhorEnvioCompanies extends Command
{
    protected $signature = 'melhorenvio:test-companies';
    protected $description = 'Test Melhor Envio API by getting shipping companies';

    public function handle()
    {
        $this->info('Testing Melhor Envio API - Getting shipping companies...');

        $token = config('services.melhorenvio.token');
        $baseUrl = 'https://sandbox.melhorenvio.com.br/';

        try {
            // Try different API versions and endpoints
            $endpoints = [
                'api/v2/me/shipment/companies',
                'api/v2/me/companies',
                'api/v2/companies'
            ];

            foreach ($endpoints as $endpoint) {
                $this->info("\nTrying endpoint: " . $endpoint);

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
                ])->get($baseUrl . $endpoint);

                $this->info('Response Status: ' . $response->status());

                if ($response->successful()) {
                    $this->info('Success! Found working endpoint.');
                    $this->info('Response Body:');
                    $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));
                    return 0;
                } else {
                    $this->error('Failed with status ' . $response->status());
                    $this->error('Response: ' . $response->body());
                }
            }

            $this->error('All endpoints failed');
            return 1;

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Melhor Envio companies test failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
