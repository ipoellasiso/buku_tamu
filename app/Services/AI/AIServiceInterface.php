<?php

namespace App\Services\AI;

interface AIServiceInterface
{
    /**
     * Kirim chat dan dapat respons (non-streaming).
     *
     * @param  array  $messages  [['role' => 'user'|'assistant', 'content' => '...']]
     * @param  string $systemPrompt
     * @return array  ['content' => string, 'usage' => [...], 'model' => string]
     */
    public function chat(array $messages, string $systemPrompt = ''): array;

    /**
     * Streaming chat — yield chunk per chunk.
     * Cocok untuk real-time chat di mobile.
     *
     * @param  array    $messages
     * @param  string   $systemPrompt
     * @return \Generator  yields string chunks
     */
    public function stream(array $messages, string $systemPrompt = ''): \Generator;

    /**
     * Nama provider (claude, openai, gemini).
     */
    public function providerName(): string;
}
