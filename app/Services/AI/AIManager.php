<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Factory + fallback manager untuk AI providers.
 * Urutan fallback: claude → openai → gemini
 */
class AIManager
{
    private array $providers = [];

    public function __construct(
        private ClaudeService $claude,
        private OpenAIService $openai,
        private GeminiService $gemini,
    ) {
        $this->providers = [
            'claude' => $claude,
            'openai' => $openai,
            'gemini' => $gemini,
        ];
    }

    /**
     * Ambil provider berdasarkan nama, atau default dari config.
     */
    public function provider(string $name = ''): AIServiceInterface
    {
        $name = $name ?: config('ai.default', 'claude');
        return $this->providers[$name] ?? $this->claude;
    }

    /**
     * Chat dengan fallback otomatis jika provider utama gagal.
     */
    public function chatWithFallback(
        array $messages,
        string $systemPrompt = '',
        string $preferredProvider = '',
    ): array {
        $order = $this->buildOrder($preferredProvider);

        foreach ($order as $name) {
            try {
                $result = $this->providers[$name]->chat($messages, $systemPrompt);
                $result['provider'] = $name;
                return $result;
            } catch (\Throwable $e) {
                Log::warning("AI provider [{$name}] gagal, coba berikutnya", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        throw new \RuntimeException('Semua AI provider tidak tersedia saat ini.');
    }

    /**
     * Stream dengan fallback ke non-streaming jika provider gagal.
     */
    public function stream(
        array $messages,
        string $systemPrompt = '',
        string $preferredProvider = '',
    ): \Generator {
        $name     = $preferredProvider ?: config('ai.default', 'claude');
        $provider = $this->providers[$name] ?? $this->claude;

        try {
            yield from $provider->stream($messages, $systemPrompt);
        } catch (\Throwable $e) {
            Log::warning("Streaming gagal untuk [{$name}], fallback non-stream", [
                'error' => $e->getMessage(),
            ]);
            // Fallback: kirim sekaligus
            $result = $this->chatWithFallback($messages, $systemPrompt);
            yield $result['content'];
        }
    }

    private function buildOrder(string $preferred): array
    {
        $all   = ['claude', 'openai', 'gemini'];
        $first = $preferred ?: config('ai.default', 'claude');
        return array_unique(array_merge([$first], $all));
    }
}
