<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeService implements AIServiceInterface
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://api.anthropic.com/v1';
    private int    $maxTokens;

    public function __construct()
    {
        $this->apiKey    = config('ai.claude.api_key');
        $this->model     = config('ai.claude.model', 'claude-sonnet-4-6');
        $this->maxTokens = config('ai.claude.max_tokens', 2048);
    }

    public function chat(array $messages, string $systemPrompt = ''): array
    {
        $payload = [
            'model'      => $this->model,
            'max_tokens' => $this->maxTokens,
            'messages'   => $messages,
        ];

        if ($systemPrompt) {
            $payload['system'] = $systemPrompt;
        }

        $response = Http::withHeaders($this->headers())
            ->timeout(60)
            ->post("{$this->baseUrl}/messages", $payload);

        if ($response->failed()) {
            Log::error('Claude API error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('Claude API gagal: ' . $response->status());
        }

        $data = $response->json();

        return [
            'content' => $data['content'][0]['text'] ?? '',
            'model'   => $data['model'] ?? $this->model,
            'usage'   => $data['usage'] ?? [],
        ];
    }

    public function stream(array $messages, string $systemPrompt = ''): \Generator
    {
        $payload = [
            'model'      => $this->model,
            'max_tokens' => $this->maxTokens,
            'stream'     => true,
            'messages'   => $messages,
        ];

        if ($systemPrompt) {
            $payload['system'] = $systemPrompt;
        }

        // Buka koneksi streaming
        $client   = new \GuzzleHttp\Client();
        $response = $client->post("{$this->baseUrl}/messages", [
            'headers' => $this->headers(),
            'json'    => $payload,
            'stream'  => true,
            'timeout' => 120,
        ]);

        $body = $response->getBody();

        while (! $body->eof()) {
            $line = trim($this->readLine($body));

            if (str_starts_with($line, 'data: ')) {
                $json = json_decode(substr($line, 6), true);

                if (isset($json['type']) && $json['type'] === 'content_block_delta') {
                    $chunk = $json['delta']['text'] ?? '';
                    if ($chunk !== '') {
                        yield $chunk;
                    }
                }
            }
        }
    }

    public function providerName(): string
    {
        return 'claude';
    }

    private function headers(): array
    {
        return [
            'x-api-key'         => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ];
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
