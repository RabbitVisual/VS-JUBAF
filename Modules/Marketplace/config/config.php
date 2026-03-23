<?php

return [
    'name' => 'Marketplace',

    'freight' => [
        'cep_origin' => env('MARKETPLACE_CEP_ORIGIN', ''),
        'cache_ttl_seconds' => (int) env('MARKETPLACE_FREIGHT_CACHE_TTL', 3600),
        'correios' => [
            'enabled' => env('MARKETPLACE_CORREIOS_ENABLED', false),
            'api_url' => env('MARKETPLACE_CORREIOS_API_URL', ''),
            'contract' => env('MARKETPLACE_CORREIOS_CONTRACT', ''),
            'password' => env('MARKETPLACE_CORREIOS_PASSWORD', ''),
        ],
    ],

    'low_stock_threshold' => (int) env('MARKETPLACE_LOW_STOCK_THRESHOLD', 5),
];
