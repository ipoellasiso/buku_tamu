<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService implements AIServiceInterface
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    private int    $maxTokens;

    public function __construct()
    {
        $this->apiKey    = config('ai.gemini.api_key');
        $this->model     = config('ai.gemini.model', 'gemini-1.5-flash');
        $this->maxTokens = config('ai.gemini.max_tokens', 2048);
    }

    public function chat(array $messages, string $systemPrompt = ''): array
    {
        // Gemini punya format berbeda — konversi dari format OpenAI-style
        $contents = collect($messages)->map(fn($m) => [
            'role'  => $m['role'] === 'assistant' ? 'model' : 'user',
            'parts' => [['text' => $m['content']]],
        ])->toArray();

        $payload = [
            'contents'         => $contents,
            'generationConfig' => ['maxOutputTokens' => $this->maxTokens],
        ];

        if ($systemPrompt) {
            $payload['systemInstruction'] = [
                'parts' => [['text' => $systemPrompt]],
            ];
        }

        $response = Http::timeout(60)
            ->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", $payload);

        if ($response->failed()) {
            Log::error('Gemini API error', ['status' => $response->status()]);
            throw new \RuntimeException('Gemini API gagal: ' . $response->status());
        }

        $data = $response->json();

        return [
            'content' => $data['candidates'][0]['content']['parts'][0]['text'] ?? '',
            'model'   => $this->model,
            'usage'   => $data['usageMetadata'] ?? [],
        ];
    }

    public function stream(array $messages, string $systemPrompt = ''): \Generator
    {
        $contents = collect($messages)->map(fn($m) => [
            'role'  => $m['role'] === 'assistant' ? 'model' : 'user',
            'parts' => [['text' => $m['content']]],
        ])->toArray();

        $payload = [
            'contents'         => $contents,
            'generationConfig' => ['maxOutputTokens' => $this->maxTokens],
        ];

        if ($systemPrompt) {
            $payload['systemInstruction'] = ['parts' => [['text' => $systemPrompt]]];
        }

        $client   = new \GuzzleHttp\Client();
        $response = $client->post(
            "{$this->baseUrl}/models/{$this->model}:streamGenerateContent?alt=sse&key={$this->apiKey}",
            ['json' => $payload, 'stream' => true, 'timeout' => 120]
        );

        $body = $response->getBody();

        while (! $body->eof()) {
            $line = trim($this->readLine($body));

            if (str_starts_with($line, 'data: ')) {
                $json  = json_decode(substr($line, 6), true);
                $chunk = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
                if ($chunk !== '') {
                    yield $chunk;
                }
            }
        }
    }

    public function providerName(): string
    {
        return 'gemini';
    }

    private function readLine($body): string
    {
        $line = '';
        while (! $body->eof()) {
            $char = $body->read(1);
            if ($char === "\n") break;
            $line .= $char;
        }
        return $line;
    }
}
