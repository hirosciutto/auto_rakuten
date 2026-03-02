<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OllamaService
{
    public function __construct(
        protected string $baseUrl,
        protected int $timeout = 120
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * プロンプトを送り、生成テキストを返す。
     */
    public function generate(string $model, string $prompt): string
    {
        $response = Http::timeout($this->timeout)
            ->post("{$this->baseUrl}/api/generate", [
                'model' => $model,
                'prompt' => $prompt,
                'stream' => false,
            ]);

        $response->throw();

        $body = $response->json();
        return (string) ($body['response'] ?? '');
    }
}
