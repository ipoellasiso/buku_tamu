<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService implements AIServiceInterface
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://api.openai.com/v1';
    private int    $maxTokens;

    public function __construct()
    {
        $this->apiKey    = config('ai.openai.api_key');
        $this->model     = config('ai.openai.model', 'gpt-4o-mini');
        $this->maxTokens = config('ai.openai.max_tokens', 2048);
    }

    public function chat(array $messages, string $systemPrompt = ''): array
    {
        $allMessages = [];

        if ($systemPrompt) {
            $allMessages[] = ['role' => 'system', 'content' => $systemPrompt];
        }

        $allMessages = array_merge($allMessages, $messages);

        $response = Http::withToken($this->apiKey)
            ->timeout(60)
            ->post("{$this->baseUrl}/chat/completions", [
                'model'      => $this->model,
                'max_tokens' => $this->maxTokens,
                'messages'   => $allMessages,
            ]);

        if ($response->failed()) {
            Log::error('OpenAI API error', ['status' => $response->status()]);
            throw new \RuntimeException('OpenAI API gagal: ' . $response->status());
        }

        $data = $response->json();

        return [
            'content' => $data['choices'][0]['message']['content'] ?? '',
            'model'   => $data['model'] ?? $this->model,
            'usage'   => $data['usage'] ?? [],
        ];
    }

    public function stream(array $messages, string $systemPrompt = ''): \Generator
    {
        $allMessages = $systemPrompt
            ? array_merge([['role' => 'system', 'content' => $systemPrompt]], $messages)
            : $messages;

        $client   = new \GuzzleHttp\Client();
        $response = $client->post("{$this->baseUrl}/chat/completions", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ],
            'json'   => [
                'model'      => $this->model,
                'max_tokens' => $this->maxTokens,
                'stream'     => true,
                'messages'   => $allMessages,
            ],
            'stream'  => true,
            'timeout' => 120,
        ]);

        $body = $response->getBody();

        while (! $body->eof()) {
            $line = trim($this->readLine($body));

            if ($line === 'data: [DONE]') break;

            if (str_starts_with($line, 'data: ')) {
                $json  = json_decode(substr($line, 6), true);
                $chunk = $json['choices'][0]['delta']['content'] ?? '';
                if ($chunk !== '') {
                    yield $chunk;
                }
            }
        }
    }

    public function providerName(): string
    {
        return 'openai';
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
