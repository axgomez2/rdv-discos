<?php

return [
    'token' => env('MELHORENVIO_API_TOKEN'),
    'sandbox' => env('MELHORENVIO_SANDBOX', true),
    'cache_time' => 30, // minutes

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

    'defaults' => [
        'receipt' => false,
        'own_hand' => false,
        'collect' => false,
        'dimensions' => [
            'width' => 32, // Standard vinyl width
            'height' => 32, // Standard vinyl height
            'length' => 1,  // Thickness per vinyl
            'weight' => 0.5, // Average weight per vinyl in kg
        ],
    ],

    'services' => [
        '1' => [
            'name' => 'PAC',
            'company' => 'Correios',
        ],
        '2' => [
            'name' => 'SEDEX',
            'company' => 'Correios',
        ],
        '3' => [
            'name' => 'JADLOG Package',
            'company' => 'Jadlog',
        ],
    ],
];
