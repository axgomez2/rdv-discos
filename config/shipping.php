<?php

return [
    'provider' => env('SHIPPING_PROVIDER', 'melhorenvio'),
    
    'from_zip' => env('SHIPPING_FROM_ZIP'),
    'from_street' => env('SHIPPING_FROM_STREET'),
    'from_number' => env('SHIPPING_FROM_NUMBER'),
    'from_complement' => env('SHIPPING_FROM_COMPLEMENT'),
    'from_district' => env('SHIPPING_FROM_DISTRICT'),
    'from_city' => env('SHIPPING_FROM_CITY'),
    'from_state' => env('SHIPPING_FROM_STATE'),
    
    'defaults' => [
        'weight' => 0.5, // em kg
        'height' => 15,  // em cm
        'width' => 20,   // em cm
        'length' => 30,  // em cm
        'insurance' => true,
        'receipt' => false,
        'own_hand' => false,
        'collect' => false,
        'non_commercial' => true,
    ],
    
    // Configurações específicas para envio de assinaturas
    'subscription_package' => [
        'name' => 'Pacote de Assinatura Mensal',
        'weight' => 0.5, // kg
        'dimensions' => [
            'height' => 15, // cm
            'width' => 20,  // cm
            'length' => 30  // cm
        ]
    ],
    
    // Tempo de cache para cálculos de frete (em minutos)
    'cache_time' => 30,
];
