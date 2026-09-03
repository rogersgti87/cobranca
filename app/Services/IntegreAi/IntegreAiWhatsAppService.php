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

    public function resolveProvider(Company $company): string
    {
        return IntegreAiWhatsAppProvider::normalize($company->whatsapp_provider);
    }

    public function supportsQrCode(Company $company): bool
    {
        return IntegreAiWhatsAppProvider::supportsQrCode($this->resolveProvider($company));
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

    public function connect(Company $company, ?int $instanceId = null, bool $createNew = false): array
    {
        $provision = $this->ensureProvisioned($company);
        if (! $provision['success']) {
            return $provision;
        }

        $tenantId = $this->externalTenantId($company);
        $provider = $this->resolveProvider($company);
        $selectedInstanceId = $instanceId ?: $company->integreai_instance_id;

        if ($selectedInstanceId) {
            $linkResponse = $this->client->post("/api/v1/tenants/{$tenantId}/whatsapp", [
                'instance_id' => (int) $selectedInstanceId,
            ]);

            if ($linkResponse->successful()) {
                $company->update(['integreai_instance_id' => (int) $selectedInstanceId]);

                return $this->finalizeConnect(
                    $company,
                    $this->unwrapData($this->client->decode($linkResponse)),
                    $provider
                );
            }
        }

        $autoLinkResponse = $this->client->post(
            "/api/v1/tenants/{$tenantId}/whatsapp",
            $this->buildAutoLinkPayload($company)
        );

        if ($autoLinkResponse->successful()) {
            $data = $this->unwrapData($this->client->decode($autoLinkResponse));
            $this->persistLinkedInstanceId($company, $data);

            return $this->finalizeConnect($company, $data, $provider);
        }

        if (! $createNew) {
            return [
                'success' => false,
                'message' => 'Não foi possível vincular automaticamente. Selecione uma instância existente do CRM IntegreAI na lista abaixo.',
                'provider' => $provider,
                'data' => $this->client->decode($autoLinkResponse),
            ];
        }

        $createResponse = $this->client->post(
            "/api/v1/tenants/{$tenantId}/whatsapp",
            $this->buildCreateInstancePayload($company, $provider)
        );
        $createBody = $this->client->decode($createResponse);

        if (! $createResponse->successful()) {
            return [
                'success' => false,
                'message' => $this->client->errorMessage($createResponse, 'Erro ao conectar WhatsApp na IntegreAI'),
                'provider' => $provider,
                'data' => $createBody,
            ];
        }

        $data = $this->unwrapData($createBody);
        $this->persistLinkedInstanceId($company, $data);

        return $this->finalizeConnect($company, $data, $provider);
    }

    public function lookupByWhatsapp(Company $company, string $whatsapp, bool $autoConnect = false): array
    {
        $normalized = normalizeBrazilWhatsapp($whatsapp);

        if (! $normalized) {
            return [
                'success' => false,
                'found' => false,
                'message' => 'Número inválido. Use +55, DDD (2 dígitos) e número com 8 ou 9 dígitos.',
            ];
        }

        if (! $this->client->isConfigured()) {
            return [
                'success' => false,
                'found' => false,
                'message' => 'IntegreAI não configurada no servidor',
            ];
        }

        $company->update(['whatsapp' => $normalized]);
        $company->refresh();

        $provision = $this->ensureProvisioned($company);
        if (! $provision['success']) {
            return array_merge($provision, ['found' => false, 'whatsapp' => $normalized]);
        }

        $instances = $this->findInstancesByNumber($normalized);

        $found = $instances !== [];

        if ($autoConnect) {
            if ($found && ! empty($instances[0]['id'])) {
                $connect = $this->connect($company, (int) $instances[0]['id']);

                return array_merge($connect, [
                    'found' => true,
                    'instance' => $instances[0],
                    'whatsapp' => $normalized,
                ]);
            }

            $connect = $this->connect($company);

            return array_merge($connect, [
                'found' => $found,
                'instance' => $instances[0] ?? null,
                'whatsapp' => $normalized,
            ]);
        }

        return [
            'success' => true,
            'found' => $found,
            'message' => $found
                ? 'Instância encontrada no CRM IntegreAI.'
                : 'Nenhuma instância encontrada no CRM para este número.',
            'instances' => $instances,
            'instance' => $instances[0] ?? null,
            'whatsapp' => $normalized,
        ];
    }

    public function listAvailableInstances(Company $company): array
    {
        if (! $this->client->isConfigured()) {
            return [
                'success' => false,
                'message' => 'IntegreAI não configurada no servidor',
                'instances' => [],
            ];
        }

        $this->ensureProvisioned($company);

        $instances = [];
        $number = $this->companyWhatsappNumber($company);
        $crmCompanyId = $company->integreai_company_id ?: config('services.integreai.crm_company_id');

        $querySets = array_filter([
            $number ? ['whatsapp_number' => $number] : null,
            $number ? ['search' => $number] : null,
            $crmCompanyId ? ['company_id' => $crmCompanyId] : null,
            [],
        ]);

        foreach ($querySets as $query) {
            $instances = $this->fetchInstancesFromApi($query);
            if ($instances !== []) {
                break;
            }
        }

        if ($instances === []) {
            $instances = $this->fetchInstancesFromTenant($company);
        }

        $instances = $this->deduplicateInstances($instances);

        return [
            'success' => true,
            'message' => $instances === []
                ? 'Nenhuma instância encontrada no CRM. Cadastre o WhatsApp da empresa ou informe o company_id do CRM.'
                : count($instances) . ' instância(s) encontrada(s) no CRM IntegreAI',
            'instances' => $instances,
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

        $status = $this->syncStatus($company);
        $status['provider'] = $this->resolveProvider($company);

        return $status;
    }

    public function getQrCode(Company $company): array
    {
        if (! $this->client->isConfigured()) {
            return [
                'success' => false,
                'message' => 'IntegreAI não configurada no servidor',
            ];
        }

        if (! $this->supportsQrCode($company)) {
            return [
                'success' => false,
                'message' => 'QR Code disponível apenas para o provedor EVOGO. No YCloud, a instância é configurada no painel IntegreAI (WhatsApp Oficial).',
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

    protected function finalizeConnect(Company $company, array $data, string $provider): array
    {
        $status = $this->syncStatus($company);

        $message = match ($provider) {
            IntegreAiWhatsAppProvider::YCLOUD => 'WhatsApp YCloud vinculado. Confirme a instância no painel IntegreAI se ainda não estiver ativa.',
            default => $status['status'] === 'open'
                ? 'WhatsApp EVOGO conectado com sucesso!'
                : 'WhatsApp EVOGO vinculado. Use "Obter QR Code" para parear o aparelho.',
        };

        return [
            'success' => true,
            'message' => $message,
            'status' => $status['status'] ?? 'connecting',
            'provider' => $provider,
            'supports_qrcode' => IntegreAiWhatsAppProvider::supportsQrCode($provider),
            'data' => $data,
        ];
    }

    protected function buildAutoLinkPayload(Company $company): array
    {
        $payload = [];

        if ($company->integreai_instance_id) {
            $payload['instance_id'] = (int) $company->integreai_instance_id;
        }

        $whatsappNumber = $this->companyWhatsappNumber($company);
        if ($whatsappNumber) {
            $payload['whatsapp_number'] = $whatsappNumber;
        }

        return $payload;
    }

    protected function findInstancesByNumber(string $normalizedNumber): array
    {
        $instances = $this->fetchInstancesFromApi(['whatsapp_number' => $normalizedNumber]);

        if ($instances === []) {
            $instances = $this->fetchInstancesFromApi(['search' => $normalizedNumber]);
        }

        $instances = $this->deduplicateInstances($instances);

        return array_values(array_filter(
            $instances,
            fn (array $instance) => ($instance['whatsapp_number'] ?? '') === $normalizedNumber
        ));
    }

    protected function fetchInstancesFromApi(array $query): array
    {
        $paths = [
            '/api/v1/instances',
            '/api/whatsapp/instances',
        ];

        foreach ($paths as $path) {
            $response = $this->client->get($path, $query);
            if (! $response->successful()) {
                continue;
            }

            $instances = $this->normalizeInstancesList($this->client->decode($response));
            if ($instances !== []) {
                return $instances;
            }
        }

        return [];
    }

    protected function fetchInstancesFromTenant(Company $company): array
    {
        $tenantId = $this->externalTenantId($company);
        $instances = [];

        foreach (["/api/v1/tenants/{$tenantId}/whatsapp", "/api/v1/tenants/{$tenantId}"] as $path) {
            $response = $this->client->get($path);
            if (! $response->successful()) {
                continue;
            }

            $data = $this->unwrapData($this->client->decode($response));
            $candidate = $this->normalizeInstanceRow($data['instance'] ?? $data);

            if ($candidate) {
                $instances[] = $candidate;
            }
        }

        return $instances;
    }

    protected function normalizeInstancesList(array $body): array
    {
        $rows = $body['data'] ?? $body['instances'] ?? $body;

        if (! is_array($rows)) {
            return [];
        }

        if ($this->isAssoc($rows) && isset($rows['id'])) {
            $rows = [$rows];
        }

        $instances = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $normalized = $this->normalizeInstanceRow($row);
            if ($normalized) {
                $instances[] = $normalized;
            }
        }

        return $instances;
    }

    protected function normalizeInstanceRow(array $row): ?array
    {
        $id = $row['id'] ?? $row['instance_id'] ?? null;
        $number = $row['whatsapp_number'] ?? $row['number'] ?? $row['phone'] ?? null;
        $name = $row['name'] ?? $row['instance'] ?? $row['label'] ?? null;
        $provider = $row['provider'] ?? null;
        $status = $row['status'] ?? $row['state'] ?? $row['connection_state'] ?? null;

        if (! $id && ! $number && ! $name) {
            return null;
        }

        $digits = $number ? $this->formatPhoneNumber((string) $number) : null;

        return [
            'id' => $id ? (int) $id : null,
            'name' => (string) ($name ?: 'Instância'),
            'whatsapp_number' => $digits,
            'whatsapp_number_formatted' => $digits ? formatPhone($digits) : null,
            'provider' => $provider ? strtolower((string) $provider) : null,
            'status' => $status ? strtolower((string) $status) : null,
            'label' => trim(sprintf(
                '%s%s%s%s',
                $name ?: 'Instância',
                $digits ? ' — ' . formatPhone($digits) : '',
                $provider ? ' [' . strtoupper((string) $provider) . ']' : '',
                $status ? ' (' . $status . ')' : ''
            )),
        ];
    }

    protected function deduplicateInstances(array $instances): array
    {
        $unique = [];

        foreach ($instances as $instance) {
            $key = ($instance['id'] ?? 'no-id') . '|' . ($instance['whatsapp_number'] ?? $instance['name']);
            $unique[$key] = $instance;
        }

        return array_values($unique);
    }

    protected function persistLinkedInstanceId(Company $company, array $data): void
    {
        $instanceId = data_get($data, 'instance_id')
            ?? data_get($data, 'instance.id')
            ?? data_get($data, 'id');

        if ($instanceId) {
            $company->update(['integreai_instance_id' => (int) $instanceId]);
        }
    }

    protected function isAssoc(array $array): bool
    {
        if ($array === []) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }

    protected function buildCreateInstancePayload(Company $company, string $provider): array
    {
        $payload = [
            'link_existing' => false,
            'provider' => $provider,
            'instance' => $this->instanceSlug($company),
        ];

        $whatsappNumber = $this->companyWhatsappNumber($company);
        if ($whatsappNumber) {
            $payload['whatsapp_number'] = $whatsappNumber;
        }

        return $payload;
    }

    protected function instanceSlug(Company $company): string
    {
        $base = 'cobranca-empresa-' . $company->id;

        return substr(preg_replace('/[^a-z0-9\-]/', '', strtolower($base)) ?? $base, 0, 60);
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
        return normalizeBrazilWhatsapp($company->whatsapp ?? '');
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
