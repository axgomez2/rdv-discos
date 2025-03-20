<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DecodeMelhorEnvioToken extends Command
{
    protected $signature = 'melhorenvio:decode-token';
    protected $description = 'Decode Melhor Envio JWT token';

    public function handle()
    {
        $this->info('Decoding Melhor Envio token...');

        $token = config('services.melhorenvio.token');

        // Split token into parts
        $tokenParts = explode('.', $token);

        if (count($tokenParts) !== 3) {
            $this->error('Invalid JWT token format');
            return 1;
        }

        // Decode header
        $this->info("\nHeader:");
        $header = json_decode($this->base64UrlDecode($tokenParts[0]), true);
        $this->info(json_encode($header, JSON_PRETTY_PRINT));

        // Decode payload
        $this->info("\nPayload:");
        $payload = json_decode($this->base64UrlDecode($tokenParts[1]), true);
        $this->info(json_encode($payload, JSON_PRETTY_PRINT));

        // Check expiration
        if (isset($payload['exp'])) {
            $expiration = \DateTime::createFromFormat('U', $payload['exp']);
            $now = new \DateTime();

            $this->info("\nToken Status:");
            $this->info("Expires: " . $expiration->format('Y-m-d H:i:s'));
            $this->info("Now: " . $now->format('Y-m-d H:i:s'));
            $this->info("Valid: " . ($expiration > $now ? 'Yes' : 'No'));
        }

        // Check scopes
        if (isset($payload['scopes'])) {
            $this->info("\nScopes:");
            foreach ($payload['scopes'] as $scope) {
                $this->info("- " . $scope);
            }
        }

        // Check if shipping-calculate scope exists
        if (isset($payload['scopes']) && in_array('shipping-calculate', $payload['scopes'])) {
            $this->info("\nShipping calculate scope is present");
        } else {
            $this->error("\nShipping calculate scope is missing!");
        }

        return 0;
    }

    protected function base64UrlDecode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }
}
