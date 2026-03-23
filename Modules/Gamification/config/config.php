<?php

return [
    'name' => 'Gamification',

    'bot' => [
        'ai_enabled' => env('CBAV_BOT_AI_ENABLED', false),
        'ai_provider' => env('CBAV_BOT_AI_PROVIDER', null), // e.g. openai
        'ai_api_key' => env('CBAV_BOT_AI_API_KEY', null),
        'max_ai_tokens' => env('CBAV_BOT_AI_MAX_TOKENS', 300),
    ],
];
