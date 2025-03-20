<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MercadoPago Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration settings for MercadoPago integration.
    | This includes API credentials and environment settings.
    |
    */

    'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
    'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
    'sandbox' => env('MERCADOPAGO_SANDBOX', true),

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for MercadoPago webhooks.
    |
    */

    'webhook_url' => env('MERCADOPAGO_WEBHOOK_URL'),

    /*
    |--------------------------------------------------------------------------
    | Subscription Configuration
    |--------------------------------------------------------------------------
    |
    | Default settings for subscription payments.
    |
    */

    'subscription' => [
        'frequency' => 1, // Billing frequency in months
        'frequency_type' => 'months',
        'currency' => 'BRL',
        'auto_recurring' => true,

        // Transaction types allowed
        'payment_methods' => [
            'credit_card' => true,
            'debit_card' => true,
            'bank_transfer' => false,
        ],

        // Retry settings for failed payments
        'retries' => [
            'enabled' => true,
            'count' => 3, // Number of retry attempts
            'interval' => 3, // Days between retries
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for payment notifications.
    |
    */

    'notifications' => [
        'payment_success' => true,
        'payment_failure' => true,
        'subscription_cancellation' => true,
        'subscription_expiration' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Handling
    |--------------------------------------------------------------------------
    |
    | Configuration for error handling and logging.
    |
    */

    'error_handling' => [
        'log_errors' => true,
        'notify_admin' => true,
        'retry_on_error' => true,
    ],
];
