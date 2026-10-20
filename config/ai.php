<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    | Pilihan: claude | openai | gemini
    | Ganti via .env: AI_DEFAULT_PROVIDER=claude
    */
    'default' => env('AI_DEFAULT_PROVIDER', 'claude'),

    'claude' => [
        'api_key'    => env('CLAUDE_API_KEY'),
        'model'      => env('CLAUDE_MODEL', 'claude-sonnet-4-6'),
        'max_tokens' => (int) env('CLAUDE_MAX_TOKENS', 2048),
    ],

    'openai' => [
        'api_key'    => env('OPENAI_API_KEY'),
        'model'      => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'max_tokens' => (int) env('OPENAI_MAX_TOKENS', 2048),
    ],

    'gemini' => [
        'api_key'    => env('GEMINI_API_KEY'),
        'model'      => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'max_tokens' => (int) env('GEMINI_MAX_TOKENS', 2048),
    ],
];
