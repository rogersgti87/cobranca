<?php

namespace App\Services\IntegreAi;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class IntegreAiClient
{
    public function isConfigured(): bool
    {
        return ! empty($this->apiKey()) && ! empty($this->baseUrl());
    }

    public function get(string $path, array $query = []): Response
    {
        return $this->request()->get($this->url($path), $query);
    }

    public function post(string $path, array $data = []): Response
    {
        return $this->request()->post($this->url($path), $data);
    }

    public function delete(string $path, array $data = []): Response
    {
        $request = $this->request();

        if ($data !== []) {
            return $request->withBody(json_encode($data), 'application/json')->delete($this->url($path));
        }

        return $request->delete($this->url($path));
    }

    public function decode(Response $response): array
    {
        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    public function errorMessage(Response $response, string $fallback = 'Erro na API IntegreAI'): string
    {
        $json = $this->decode($response);

        return $json['error']['message']
            ?? $json['message']
            ?? $fallback;
    }

    protected function request(): PendingRequest
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('IntegreAI não configurada. Defina INTEGREAI_API_URL e INTEGREAI_API_KEY no .env.');
        }

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Correlation-Id' => (string) str()->uuid(),
        ])->timeout(30);
    }

    protected function baseUrl(): string
    {
        return rtrim((string) config('services.integreai.url'), '/');
    }

    protected function apiKey(): ?string
    {
        return config('services.integreai.api_key');
    }

    protected function url(string $path): string
    {
        return $this->baseUrl() . '/' . ltrim($path, '/');
    }
}
