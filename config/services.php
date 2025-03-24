<?php

return [
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'discogs' => [
        'token' => env('DISCOGS_TOKEN'),
    ],

    'youtube' => [
        'api_key' => env('YOUTUBE_API_KEY'),
    ],

    'mercadopago' => [
        'public_key' => env('MERCADOPAGO_PUBLIC_KEY', ''),
        'access_token' => env('MERCADOPAGO_ACCESS_TOKEN', ''),
        'sandbox' => env('MERCADOPAGO_SANDBOX', false),
        'webhook_url' => env('MERCADOPAGO_WEBHOOK_URL', ''),
        'integrator_id' => env('MERCADOPAGO_INTEGRATOR_ID', null),
    ],

    'melhorenvio' => [
        'api_token' => env('MELHORENVIO_API_TOKEN'),
        'from' => [
            'postal_code' => env('MELHORENVIO_FROM_POSTAL_CODE'),
            'phone' => env('MELHORENVIO_FROM_PHONE'),
            'email' => env('MELHORENVIO_FROM_EMAIL'),
            'document' => env('MELHORENVIO_FROM_DOCUMENT'),
            'address' => env('MELHORENVIO_FROM_ADDRESS'),
            'number' => env('MELHORENVIO_FROM_NUMBER'),
            'complement' => env('MELHORENVIO_FROM_COMPLEMENT'),
            'district' => env('MELHORENVIO_FROM_DISTRICT'),
            'city' => env('MELHORENVIO_FROM_CITY'),
            'state' => env('MELHORENVIO_FROM_STATE'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', ''),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
        'redirect' => env('GOOGLE_REDIRECT_URI', route('auth.google.callback')),
    ],

    'pagseguro' => [
        'email' => env('PAGSEGURO_EMAIL'),
        'token' => env('PAGSEGURO_TOKEN'),
        'sandbox' => env('PAGSEGURO_SANDBOX', true),
        'notification' => env('PAGSEGURO_NOTIFICATION'),
    ],
];
