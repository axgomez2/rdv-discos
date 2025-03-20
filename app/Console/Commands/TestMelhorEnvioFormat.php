<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestMelhorEnvioFormat extends Command
{
    protected $signature = 'melhorenvio:test-format';
    protected $description = 'Test Melhor Envio API with documented format';

    public function handle()
    {
        $this->info('Testing Melhor Envio API with documented format...');

        $token = config('services.melhorenvio.token');
        $baseUrl = 'https://sandbox.melhorenvio.com.br/';

        // Payload exactly as documented
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

        $this->info('Making request to Melhor Envio API...');
        $this->info('URL: ' . $baseUrl . 'api/v2/me/shipment/calculate');
        $this->info('Payload: ' . json_encode($payload, JSON_PRETTY_PRINT));

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'User-Agent' => 'Embaixada da Dance Music (embaixadadancemusic@gmail.com)'
            ])->post($baseUrl . 'api/v2/me/shipment/calculate', $payload);

            $this->info('Response Status: ' . $response->status());
            $this->info('Response Headers:');
            foreach ($response->headers() as $name => $values) {
                $this->info("$name: " . implode(', ', $values));
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

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Melhor Envio test failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return 0;
    }
}
