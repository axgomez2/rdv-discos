<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestMelhorEnvioAuth extends Command
{
    protected $signature = 'melhorenvio:test-auth';
    protected $description = 'Test authentication with Melhor Envio API';

    public function handle()
    {
        $this->info('Testing Melhor Envio authentication...');

        $token = config('services.melhorenvio.token');
        $baseUrl = 'https://sandbox.melhorenvio.com.br/api/v2/';

        if (empty($token)) {
            $this->error('Token not configured in services.melhorenvio.token');
            return 1;
        }

        $this->info('Token length: ' . strlen($token));
        $this->info('Token starts with: ' . substr($token, 0, 20) . '...');

        try {
            // Format token
            $authToken = str_starts_with($token, 'Bearer ') ? $token : 'Bearer ' . $token;

            $this->info('Making request to Melhor Envio API...');

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => $authToken
            ])->get($baseUrl . 'me');

            $this->info('Response Status: ' . $response->status());
            $this->info('Response Headers:');
            foreach ($response->headers() as $name => $values) {
                $this->info("  $name: " . implode(', ', $values));
            }

            $this->info('Response Body:');
            $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));

            if ($response->successful()) {
                $this->info('Authentication successful!');
                return 0;
            } else {
                $this->error('Authentication failed!');
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Melhor Envio auth test failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
