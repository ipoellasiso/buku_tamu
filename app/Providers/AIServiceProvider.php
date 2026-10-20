<?php

namespace App\Providers;

use App\Services\AI\AIManager;
use App\Services\AI\ClaudeService;
use App\Services\AI\GeminiService;
use App\Services\AI\OpenAIService;
use Illuminate\Support\ServiceProvider;

class AIServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind masing-masing service sebagai singleton
        $this->app->singleton(ClaudeService::class);
        $this->app->singleton(OpenAIService::class);
        $this->app->singleton(GeminiService::class);
        $this->app->singleton(AIManager::class);
    }
}
