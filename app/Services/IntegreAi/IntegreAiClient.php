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

    public function post(string $path, array $data = [], ?string $token = null, ?int $timeout = null): Response
    {
        return $this->send(
            fn (PendingRequest $request) => $request->post($this->url($path), $data),
            $token,
            $timeout
        );
    }

    /**
     * @return array{response: Response|null, error: string|null}
     */
    public function tryPost(string $path, array $data = [], ?string $token = null, ?int $timeout = null): array
    {
        try {
            return [
                'response' => $this->post($path, $data, $token, $timeout),
                'error' => null,
            ];
        } catch (RuntimeException $e) {
            return [
                'response' => null,
                'error' => $e->getMessage(),
            ];
        }
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
            return 'Envio via API M2M não está habilitado no servidor IntegreAI (HTTP 405). Atualize o IntegreAI em produção.';
        }

        $json = $this->decode($response);

        if (isset($json['error']) && is_array($json['error'])) {
            return $json['error']['message'] ?? $fallback;
        }

        return $json['message'] ?? $fallback;
    }

    protected function request(?string $token = null, ?int $timeout = null): PendingRequest
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
        ])->timeout($timeout ?? 30);
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

    protected function send(callable $callback, ?string $token = null, ?int $timeout = null): Response
    {
        try {
            return $callback($this->request($token, $timeout));
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

        if (str_contains($message, 'timed out') || str_contains($message, 'Operation timed out')) {
            return 'Tempo esgotado ao aguardar resposta da API IntegreAI. Tente novamente em instantes.';
        }

        return 'Falha de conexão com a API IntegreAI: ' . $message;
    }
}
