<?php

namespace App\Services\IntegreAi;

use Illuminate\Http\Client\ConnectionException;
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
        return $this->send(fn (PendingRequest $request) => $request->get($this->url($path), $query));
    }

    public function post(string $path, array $data = [], ?string $token = null): Response
    {
        return $this->send(
            fn (PendingRequest $request) => $request->post($this->url($path), $data),
            $token
        );
    }

    public function delete(string $path, array $data = []): Response
    {
        return $this->send(function (PendingRequest $request) use ($path, $data) {
            if ($data !== []) {
                return $request->withBody(json_encode($data), 'application/json')->delete($this->url($path));
            }

            return $request->delete($this->url($path));
        });
    }

    public function decode(Response $response): array
    {
        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    public function errorMessage(Response $response, string $fallback = 'Erro na API IntegreAI'): string
    {
        if ($response->status() === 405) {
            return 'Envio de mensagens via API M2M não está habilitado no servidor IntegreAI. Configure INTEGREAI_PANEL_TOKEN no .env ou ative o endpoint POST /api/v1/tenants/{id}/messages.';
        }

        $json = $this->decode($response);

        return $json['error']['message']
            ?? $json['message']
            ?? $fallback;
    }

    protected function request(?string $token = null): PendingRequest
    {
        $apiKey = $token ?: $this->apiKey();

        if (! $apiKey || ! $this->baseUrl()) {
            throw new RuntimeException('IntegreAI não configurada. Defina INTEGREAI_API_URL e INTEGREAI_API_KEY no .env.');
        }

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Correlation-Id' => (string) str()->uuid(),
        ])->timeout(30);
    }

    protected function baseUrl(): string
    {
        $url = rtrim((string) config('services.integreai.url'), '/');

        if (str_ends_with($url, '/api')) {
            $url = substr($url, 0, -4);
        }

        return $url;
    }

    protected function apiKey(): ?string
    {
        return config('services.integreai.api_key');
    }

    protected function url(string $path): string
    {
        return $this->baseUrl() . '/' . ltrim($path, '/');
    }

    protected function send(callable $callback, ?string $token = null): Response
    {
        try {
            return $callback($this->request($token));
        } catch (ConnectionException $e) {
            throw new RuntimeException($this->connectionErrorMessage($e), 0, $e);
        }
    }

    protected function connectionErrorMessage(ConnectionException $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'Could not resolve host')) {
            return 'Não foi possível resolver o host da API IntegreAI. Verifique INTEGREAI_API_URL no .env (use https://integreai.com.br).';
        }

        return 'Falha de conexão com a API IntegreAI: ' . $message;
    }
}
