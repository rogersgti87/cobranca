<?php

namespace App\Services\IntegreAi;

use App\Models\Company;
use Illuminate\Http\Client\Response;

class IntegreAiWhatsAppService
{
    public function __construct(
        protected IntegreAiClient $client
    ) {}

    public function externalTenantId(Company $company): string
    {
        if (! empty($company->api_session_whatsapp)) {
            return $company->api_session_whatsapp;
        }

        return 'cobranca:empresa:' . $company->id;
    }

    public function ensureProvisioned(Company $company): array
    {
        $externalTenantId = $this->externalTenantId($company);
        $payload = [
            'external_tenant_id' => $externalTenantId,
            'name' => $company->trade_name ?: $company->name,
        ];

        $whatsappNumber = $this->companyWhatsappNumber($company);
        if ($whatsappNumber) {
            $payload['whatsapp_number'] = $whatsappNumber;
        }

        $response = $this->client->post('/api/v1/tenants/provision', $payload);
        $body = $this->client->decode($response);

        if (! $response->successful()) {
            return [
                'success' => false,
                'message' => $this->client->errorMessage($response, 'Erro ao provisionar tenant na IntegreAI'),
                'data' => $body,
            ];
        }

        if ($company->api_session_whatsapp !== $externalTenantId) {
            $company->update(['api_session_whatsapp' => $externalTenantId]);
        }

        return [
            'success' => true,
            'message' => 'Tenant provisionado com sucesso',
            'data' => $this->unwrapData($body),
        ];
    }

    public function connect(Company $company): array
    {
        $provision = $this->ensureProvisioned($company);
        if (! $provision['success']) {
            return $provision;
        }

        $tenantId = $this->externalTenantId($company);
        $payload = [];

        $whatsappNumber = $this->companyWhatsappNumber($company);
        if ($whatsappNumber) {
            $payload['whatsapp_number'] = $whatsappNumber;
        }

        $response = $this->client->post("/api/v1/tenants/{$tenantId}/whatsapp", $payload);
        $body = $this->client->decode($response);

        if (! $response->successful()) {
            return [
                'success' => false,
                'message' => $this->client->errorMessage($response, 'Erro ao conectar WhatsApp na IntegreAI'),
                'data' => $body,
            ];
        }

        $status = $this->syncStatus($company);

        return [
            'success' => true,
            'message' => 'WhatsApp vinculado com sucesso',
            'status' => $status['status'] ?? 'connecting',
            'data' => $this->unwrapData($body),
        ];
    }

    public function getStatus(Company $company): array
    {
        if (! $this->client->isConfigured()) {
            return [
                'success' => false,
                'message' => 'IntegreAI não configurada no servidor',
            ];
        }

        $provision = $this->ensureProvisioned($company);
        if (! $provision['success']) {
            return $provision;
        }

        return $this->syncStatus($company);
    }

    public function getQrCode(Company $company): array
    {
        if (! $this->client->isConfigured()) {
            return [
                'success' => false,
                'message' => 'IntegreAI não configurada no servidor',
            ];
        }

        $provision = $this->ensureProvisioned($company);
        if (! $provision['success']) {
            return $provision;
        }

        $tenantId = $this->externalTenantId($company);
        $response = $this->client->get("/api/v1/tenants/{$tenantId}/whatsapp/qrcode");
        $body = $this->client->decode($response);

        if (! $response->successful()) {
            return [
                'success' => false,
                'message' => $this->client->errorMessage($response, 'Erro ao obter QR Code'),
                'data' => $body,
            ];
        }

        $data = $this->unwrapData($body);
        $qrcode = $data['qrcode']
            ?? $data['base64']
            ?? $data['qr_code']
            ?? $data['code']
            ?? null;

        if (! $qrcode) {
            return [
                'success' => false,
                'message' => 'QR Code não disponível. A instância pode já estar conectada.',
                'data' => $data,
            ];
        }

        if (! str_starts_with($qrcode, 'data:image')) {
            $qrcode = 'data:image/png;base64,' . $qrcode;
        }

        return [
            'success' => true,
            'qrcode' => $qrcode,
            'message' => 'QR Code obtido com sucesso! Escaneie com seu WhatsApp.',
            'data' => $data,
        ];
    }

    public function disconnect(Company $company, bool $disconnectProvider = false): array
    {
        if (empty($company->api_session_whatsapp)) {
            return [
                'success' => false,
                'message' => 'WhatsApp não está configurado para esta empresa',
            ];
        }

        $tenantId = $this->externalTenantId($company);
        $payload = $disconnectProvider ? ['disconnect_provider' => true] : [];

        $response = $this->client->delete("/api/v1/tenants/{$tenantId}/whatsapp", $payload);
        $body = $this->client->decode($response);

        if (! $response->successful()) {
            return [
                'success' => false,
                'message' => $this->client->errorMessage($response, 'Erro ao desconectar WhatsApp'),
                'data' => $body,
            ];
        }

        $company->update(['api_status_whatsapp' => 'close']);

        return [
            'success' => true,
            'message' => $disconnectProvider
                ? 'WhatsApp desconectado no provedor com sucesso!'
                : 'WhatsApp desvinculado com sucesso!',
            'data' => $this->unwrapData($body),
        ];
    }

    public function sendText(Company $company, string $number, string $text): array
    {
        if (! $this->client->isConfigured()) {
            return [
                'success' => false,
                'message' => 'IntegreAI não configurada no servidor',
                'response' => [],
            ];
        }

        $provision = $this->ensureProvisioned($company);
        if (! $provision['success']) {
            return [
                'success' => false,
                'message' => $provision['message'],
                'response' => $provision['data'] ?? [],
            ];
        }

        $status = $this->syncStatus($company);
        if (($status['status'] ?? 'close') !== 'open') {
            return [
                'success' => false,
                'message' => 'Whatsapp desconectado! Fatura não enviada para whatsapp.',
                'response' => $status,
            ];
        }

        $tenantId = $this->externalTenantId($company);
        $response = $this->client->post("/api/v1/tenants/{$tenantId}/messages", [
            'number' => $this->formatPhoneNumber($number),
            'text' => $text,
        ]);

        $body = $this->client->decode($response);

        if (! $response->successful()) {
            return [
                'success' => false,
                'message' => $this->client->errorMessage($response, 'Erro ao enviar mensagem'),
                'response' => $body,
            ];
        }

        return [
            'success' => true,
            'message' => 'Enviado',
            'response' => $body,
        ];
    }

    protected function syncStatus(Company $company): array
    {
        $tenantId = $this->externalTenantId($company);
        $response = $this->fetchStatusResponse($tenantId);
        $body = $this->client->decode($response);

        if (! $response->successful()) {
            return [
                'success' => false,
                'status' => $company->api_status_whatsapp ?: 'close',
                'message' => $this->client->errorMessage($response, 'Erro ao verificar status'),
                'data' => $body,
            ];
        }

        $data = $this->unwrapData($body);
        $rawStatus = $this->extractRawStatus($data);
        $normalizedStatus = $this->normalizeStatus($rawStatus);

        $company->update(['api_status_whatsapp' => $normalizedStatus]);

        return [
            'success' => true,
            'status' => $normalizedStatus,
            'raw_status' => $rawStatus,
            'message' => $this->statusMessage($normalizedStatus),
            'data' => $data,
        ];
    }

    protected function fetchStatusResponse(string $tenantId): Response
    {
        $response = $this->client->get("/api/v1/tenants/{$tenantId}/whatsapp/status");

        if ($response->successful()) {
            return $response;
        }

        return $this->client->get("/api/v1/tenants/{$tenantId}/whatsapp");
    }

    protected function extractRawStatus(array $data): string
    {
        $candidates = [
            data_get($data, 'status'),
            data_get($data, 'connection.status'),
            data_get($data, 'connection_state'),
            data_get($data, 'instance.status'),
            data_get($data, 'instance.state'),
            data_get($data, 'state'),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                return strtolower($candidate);
            }
        }

        return 'unknown';
    }

    protected function normalizeStatus(?string $status): string
    {
        $status = strtolower((string) $status);

        return match (true) {
            in_array($status, ['open', 'connected', 'authenticated', 'online', 'ready'], true) => 'open',
            in_array($status, ['connecting', 'qr', 'pairing', 'pending'], true) => 'connecting',
            in_array($status, ['close', 'closed', 'disconnected', 'offline', 'logout'], true) => 'close',
            default => 'close',
        };
    }

    protected function statusMessage(string $status): string
    {
        return match ($status) {
            'open' => 'WhatsApp Conectado! ✓',
            'connecting' => 'WhatsApp está conectando...',
            default => 'WhatsApp Desconectado',
        };
    }

    protected function companyWhatsappNumber(Company $company): ?string
    {
        $number = removeEspeciais($company->whatsapp ?? '');

        return $number ? $this->formatPhoneNumber($number) : null;
    }

    protected function formatPhoneNumber(string $number): string
    {
        $digits = preg_replace('/\D/', '', $number) ?? '';

        if ($digits === '') {
            return '';
        }

        if (! str_starts_with($digits, '55')) {
            $digits = '55' . $digits;
        }

        return $digits;
    }

    protected function unwrapData(array $body): array
    {
        $data = $body['data'] ?? $body;

        return is_array($data) ? $data : [];
    }
}
