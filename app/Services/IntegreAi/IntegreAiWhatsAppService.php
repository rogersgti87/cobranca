<?php

namespace App\Services\IntegreAi;

use App\Models\Company;
use Illuminate\Http\Client\Response;

class IntegreAiWhatsAppService
{
    public function __construct(
        protected IntegreAiClient $client
    ) {}

    public function defaultExternalTenantId(Company $company): string
    {
        return 'cobranca:empresa:' . $company->id;
    }

    public function externalTenantId(Company $company): string
    {
        return $this->defaultExternalTenantId($company);
    }

    public function ensureProvisioned(Company $company): array
    {
        $provisionTenantId = $this->defaultExternalTenantId($company);
        $payload = [
            'external_tenant_id' => $provisionTenantId,
            'name' => $company->trade_name ?: $company->name,
        ];

        $whatsappNumber = $this->companyWhatsappNumber($company);
        if ($whatsappNumber) {
            $payload['whatsapp_number'] = $whatsappNumber;
        }

        $crmCompanyId = $company->integreai_company_id ?: config('services.integreai.crm_company_id');
        if ($crmCompanyId) {
            $payload['company_id'] = (int) $crmCompanyId;
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

        $data = $this->unwrapData($body);
        $this->applyTenantWhatsappData($company, $data);
        $this->syncCrmInstanceFromTenant($company, $data);

        return [
            'success' => true,
            'message' => 'Tenant provisionado com sucesso',
            'data' => $data,
        ];
    }

    public function resolveProvider(Company $company): string
    {
        return IntegreAiWhatsAppProvider::normalize($company->whatsapp_provider);
    }

    public function supportsQrCode(Company $company): bool
    {
        return IntegreAiWhatsAppProvider::supportsQrCode($this->resolveProvider($company));
    }

    protected array $remoteTenantCache = [];

    protected function syncCrmInstanceFromTenant(Company $company, array $tenantData): void
    {
        $number = $this->companyWhatsappNumber($company);
        $instance = $this->instanceFromTenantData($tenantData);

        if ($instance && $number && ($instance['whatsapp_number'] ?? '') === $number) {
            $this->applyCrmInstanceData($company, $instance, $tenantData);

            return;
        }

        if ($number) {
            $instances = $this->findInstancesByNumber($company, $number);
            if (! empty($instances[0])) {
                $this->applyCrmInstanceData($company, $instances[0], $tenantData);
            }
        }
    }

    protected function applyCrmInstanceData(Company $company, array $instance, array $tenantData = []): void
    {
        $updates = [];

        $sessionName = trim((string) ($instance['name'] ?? ''));
        if ($sessionName !== '') {
            $updates['api_session_whatsapp'] = $sessionName;
        }

        if (! empty($instance['id'])) {
            $updates['integreai_instance_id'] = (int) $instance['id'];
        }

        if (! empty($instance['token'])) {
            $updates['api_token_whatsapp'] = (string) $instance['token'];
        }

        $crmCompanyId = $tenantData['company_id'] ?? ($instance['company_id'] ?? null);
        if ($crmCompanyId) {
            $updates['integreai_company_id'] = (int) $crmCompanyId;
        }

        if (! empty($instance['provider'])) {
            $updates['whatsapp_provider'] = strtolower((string) $instance['provider']);
        }

        if (! empty($instance['status'])) {
            $updates['api_status_whatsapp'] = $this->normalizeStatus((string) $instance['status']);
        }

        if ($updates !== []) {
            $company->update($updates);
        }
    }

    protected function tenantExistsRemotely(string $externalTenantId): bool
    {
        if (! $this->client->isConfigured()) {
            return false;
        }

        if (array_key_exists($externalTenantId, $this->remoteTenantCache)) {
            return $this->remoteTenantCache[$externalTenantId];
        }

        $response = $this->client->get("/api/v1/tenants/{$externalTenantId}");
        $exists = $response->successful();
        $this->remoteTenantCache[$externalTenantId] = $exists;

        return $exists;
    }

    public function connect(Company $company, ?int $instanceId = null, bool $createNew = false): array
    {
        if ($createNew) {
            $this->clearLocalWhatsappLink($company);
            $company->refresh();
        } elseif ($instanceId) {
            $company->update(['integreai_instance_id' => $instanceId]);
            $company->refresh();
        }

        $number = $this->companyWhatsappNumber($company);
        if ($number) {
            $this->clearStaleInstanceLink($company, $number);
            $company->refresh();
        }

        $provision = $this->ensureProvisioned($company);
        if (! $provision['success']) {
            return $provision;
        }

        $provider = $this->resolveProvider($company);
        $tenantData = $this->refreshTenantData($company, $provision['data'] ?? []);

        if (! $createNew && $this->isTenantWhatsappLinked($tenantData, $company)) {
            $this->applyTenantWhatsappData($company, $tenantData);

            return $this->finalizeConnect($company, $tenantData, $provider);
        }

        $tenantId = $this->externalTenantId($company);
        $linkResponse = null;
        $linkBody = [];

        $payload = $createNew
            ? $this->buildCreateInstancePayload($company, $provider)
            : $this->buildAutoLinkPayload($company);

        if ($payload !== []) {
            $linkResponse = $this->client->post("/api/v1/tenants/{$tenantId}/whatsapp", $payload);
            $linkBody = $this->client->decode($linkResponse);

            if ($linkResponse->successful()) {
                $data = $this->unwrapData($linkBody);
                $this->applyTenantWhatsappData($company, $data);

                return $this->finalizeConnect($company, $data, $provider);
            }

            if ($linkResponse->status() === 405 && ! $createNew) {
                $reprovision = $this->ensureProvisioned($company->fresh());
                $tenantData = $this->refreshTenantData($company, $reprovision['data'] ?? []);

                if ($reprovision['success'] && $this->isTenantWhatsappLinked($tenantData, $company)) {
                    $this->applyTenantWhatsappData($company, $tenantData);

                    return $this->finalizeConnect($company, $tenantData, $provider);
                }
            }

            if (! $linkResponse->successful() && ! $createNew && $company->integreai_instance_id) {
                $retryPayload = ['instance_id' => (int) $company->integreai_instance_id];
                $retryResponse = $this->client->post("/api/v1/tenants/{$tenantId}/whatsapp", $retryPayload);
                $retryBody = $this->client->decode($retryResponse);

                if ($retryResponse->successful()) {
                    $data = $this->unwrapData($retryBody);
                    $this->applyTenantWhatsappData($company, $data);

                    return $this->finalizeConnect($company, $data, $provider);
                }
            }
        }

        $number = $this->companyWhatsappNumber($company);
        $instances = $number ? $this->findInstancesByNumber($company, $number) : [];

        if (! $createNew) {
            return [
                'success' => false,
                'message' => $instances !== []
                    ? 'Instância encontrada no CRM, mas ainda não vinculada. Selecione abaixo e clique em Conectar.'
                    : 'Não foi possível vincular o WhatsApp. Confirme o número no CRM IntegreAI ou use "Criar nova instância".',
                'provider' => $provider,
                'instances' => $instances,
                'data' => $linkBody,
            ];
        }

        return [
            'success' => false,
            'message' => $linkResponse
                ? $this->client->errorMessage($linkResponse, 'Erro ao criar instância WhatsApp na IntegreAI')
                : 'Não foi possível criar instância WhatsApp na IntegreAI.',
            'provider' => $provider,
            'instances' => $instances,
            'data' => $linkBody,
        ];
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

        $this->clearStaleInstanceLink($company, $normalized);

        $provision = $this->ensureProvisioned($company);
        if (! $provision['success']) {
            return array_merge($provision, ['found' => false, 'whatsapp' => $normalized]);
        }

        $tenantData = $this->refreshTenantData($company, $provision['data'] ?? []);
        $this->syncTenantFromIntegreai($company, $tenantData);
        $company->refresh();

        $instances = $this->findInstancesByNumber($company, $normalized);
        $found = $instances !== [] || $this->isTenantWhatsappLinked($tenantData, $company);

        if ($autoConnect && $this->isTenantWhatsappLinked($tenantData, $company)) {
            $this->applyTenantWhatsappData($company, $tenantData);
            $provider = $this->resolveProvider($company);

            return array_merge($this->finalizeConnect($company, $tenantData, $provider), [
                'found' => true,
                'instances' => $instances,
                'instance' => $instances[0] ?? $this->instanceFromTenantData($tenantData),
                'whatsapp' => $normalized,
            ], $this->whatsappContextPayload($company));
        }

        if ($autoConnect) {
            $connect = $this->connect($company, $instances[0]['id'] ?? null);

            return array_merge($connect, [
                'found' => $found || ($connect['success'] ?? false),
                'instances' => $instances,
                'instance' => $instances[0] ?? null,
                'whatsapp' => $normalized,
            ], $this->whatsappContextPayload($company->fresh()));
        }

        return array_merge([
            'success' => true,
            'found' => $found,
            'message' => $found
                ? 'Instância encontrada no CRM IntegreAI.'
                : 'Nenhuma instância encontrada no CRM para este número.',
            'instances' => $instances,
            'instance' => $instances[0] ?? null,
            'whatsapp' => $normalized,
        ], $this->whatsappContextPayload($company));
    }

    protected function whatsappContextPayload(Company $company): array
    {
        return [
            'external_tenant_id' => $this->externalTenantId($company),
            'session_name' => $company->api_session_whatsapp,
            'integreai_instance_id' => $company->integreai_instance_id,
            'has_token' => ! empty($company->api_token_whatsapp),
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
        if (! $company->integreai_instance_id && ! $this->companyWhatsappNumber($company)) {
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

        $this->clearLocalWhatsappLink($company);

        return [
            'success' => true,
            'message' => $disconnectProvider
                ? 'WhatsApp desconectado no provedor com sucesso!'
                : 'Instância desvinculada. Informe o novo número e clique em Conectar.',
            'data' => $this->unwrapData($body),
        ];
    }

    public function resetWhatsappLink(Company $company): array
    {
        if ($company->integreai_instance_id || $this->companyWhatsappNumber($company)) {
            $disconnect = $this->disconnect($company);
            if ($disconnect['success']) {
                return $disconnect;
            }
        }

        $this->clearLocalWhatsappLink($company);

        return [
            'success' => true,
            'message' => 'Vínculo local removido. Informe o novo número e clique em Conectar.',
            'data' => [],
        ];
    }

    public function sendText(Company $company, string $number, string $text): array
    {
        $context = $this->resolveTenantContext($company);
        if (! ($context['success'] ?? false)) {
            return [
                'success' => false,
                'message' => $context['message'] ?? 'Erro ao resolver tenant IntegreAI',
                'response' => $context['data'] ?? [],
            ];
        }

        $company->refresh();
        $tenantId = (string) $context['tenant_id'];
        $tenantData = $context['tenant_data'] ?? [];

        if (! $this->isTenantWhatsappLinked($tenantData, $company)) {
            return [
                'success' => false,
                'message' => 'Instância WhatsApp não vinculada no IntegreAI. Reconecte o WhatsApp nas integrações da empresa.',
                'response' => $tenantData,
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

        $formattedNumber = $this->formatPhoneNumber($number);
        $payload = [
            'number' => $formattedNumber,
            'text' => $text,
        ];

        $attempts = [
            [
                'path' => '/api/v1/tenants/messages',
                'data' => array_merge($payload, ['external_tenant_id' => $tenantId]),
                'timeout' => 60,
            ],
            [
                'path' => "/api/v1/tenants/{$tenantId}/messages",
                'data' => $payload,
                'timeout' => 15,
            ],
        ];

        $lastResponse = null;
        $lastError = null;
        $panelResult = null;

        foreach ($attempts as $attempt) {
            $result = $this->client->tryPost($attempt['path'], $attempt['data'], null, $attempt['timeout']);
            $response = $result['response'];

            if ($result['error']) {
                $lastError = $result['error'];
            }

            if ($response === null) {
                continue;
            }

            $lastResponse = $response;

            if ($response->successful()) {
                return $this->buildSendSuccessResponse($response);
            }

            if (! in_array($response->status(), [404, 405, 408, 502, 503, 504], true)) {
                break;
            }
        }

        if ($this->hasPanelToken()) {
            $panelResult = $this->sendTextViaWhatsappApi($company, $tenantData, $formattedNumber, $text);
            if ($panelResult['success']) {
                return $panelResult;
            }
        }

        return [
            'success' => false,
            'message' => $this->humanizeSendError(
                $panelResult['message'] ?? $this->buildM2mSendFailureMessage($lastResponse, $lastError, $tenantId)
            ),
            'response' => $lastResponse ? $this->client->decode($lastResponse) : ($panelResult['response'] ?? []),
        ];
    }

    protected function buildM2mSendFailureMessage(?\Illuminate\Http\Client\Response $response, ?string $connectionError, string $tenantId): string
    {
        if ($connectionError) {
            return $this->humanizeSendError($connectionError);
        }

        if ($response) {
            $message = $this->client->errorMessage($response, 'Erro ao enviar mensagem via API M2M IntegreAI');

            return $this->humanizeSendError($message);
        }

        return 'Não foi possível enviar via API M2M IntegreAI para o tenant ' . $tenantId . '. Verifique INTEGREAI_API_KEY e se o servidor IntegreAI está atualizado.';
    }

    protected function humanizeSendError(string $message): string
    {
        if (str_contains($message, 'timed out') || str_contains($message, 'cURL error 28')) {
            return 'O IntegreAI não respondeu a tempo ao enviar a mensagem. A instância pode estar conectada, mas o envio falhou no servidor. Tente novamente ou verifique a instância no painel IntegreAI.';
        }

        if (str_contains($message, 'evogo') || str_contains($message, 'EVOGO')) {
            return 'O IntegreAI não conseguiu enviar pela instância WhatsApp vinculada. Verifique se a instância está conectada no painel IntegreAI.';
        }

        return $message;
    }

    public function resolveTenantContext(Company $company): array
    {
        if (! $this->client->isConfigured()) {
            return [
                'success' => false,
                'message' => 'IntegreAI não configurada no servidor',
                'tenant_id' => null,
                'tenant_data' => [],
            ];
        }

        $provision = $this->ensureProvisioned($company);
        if (! $provision['success']) {
            return array_merge($provision, [
                'tenant_id' => null,
                'tenant_data' => [],
            ]);
        }

        $tenantData = $this->refreshTenantData($company, $provision['data'] ?? []);
        $this->syncTenantFromIntegreai($company, $tenantData);

        $number = $this->companyWhatsappNumber($company);
        if ($number && ! $this->isTenantWhatsappLinked($tenantData, $company)) {
            $instances = $this->findInstancesByNumber($company, $number);
            if (! empty($instances[0]['id'])) {
                $company->update(['integreai_instance_id' => (int) $instances[0]['id']]);
                $company->refresh();

                $reprovision = $this->ensureProvisioned($company);
                if ($reprovision['success']) {
                    $tenantData = $this->refreshTenantData($company, $reprovision['data'] ?? $tenantData);
                    $this->syncTenantFromIntegreai($company, $tenantData);
                }
            }
        }

        $company->refresh();
        $tenantId = $this->defaultExternalTenantId($company);
        $instance = $this->instanceFromTenantData($tenantData);

        return [
            'success' => true,
            'tenant_id' => $tenantId,
            'external_tenant_id' => $tenantId,
            'session_name' => $company->api_session_whatsapp,
            'tenant_data' => $tenantData,
            'instance' => $instance,
        ];
    }

    protected function syncTenantFromIntegreai(Company $company, array $tenantData): void
    {
        $this->applyTenantWhatsappData($company, $tenantData);
        $this->syncCrmInstanceFromTenant($company, $tenantData);
        $company->refresh();
    }

    protected function hasPanelToken(): bool
    {
        $token = config('services.integreai.panel_token');

        return is_string($token) && $token !== '';
    }

    protected function buildSendSuccessResponse(\Illuminate\Http\Client\Response $response): array
    {
        return [
            'success' => true,
            'message' => 'Enviado',
            'response' => $this->client->decode($response),
        ];
    }

    protected function fetchTenantData(Company $company): array
    {
        $tenantId = $this->externalTenantId($company);
        $response = $this->client->get("/api/v1/tenants/{$tenantId}");

        if (! $response->successful()) {
            return [];
        }

        return $this->unwrapData($this->client->decode($response));
    }

    protected function sendTextViaWhatsappApi(
        Company $company,
        array $tenantData,
        string $formattedNumber,
        string $text
    ): array {
        $token = $this->panelToken();

        if (! $token) {
            return [
                'success' => false,
                'message' => 'Fallback do painel não configurado.',
                'response' => [],
            ];
        }

        $instance = (string) (
            $company->api_session_whatsapp
            ?? data_get($tenantData, 'whatsapp.instance_name')
            ?? data_get($tenantData, 'whatsapp.instance')
            ?? ''
        );

        if ($instance === '') {
            return [
                'success' => false,
                'message' => 'Instância WhatsApp não identificada para envio. Reconecte o WhatsApp nas integrações.',
                'response' => [],
            ];
        }

        $provider = data_get($tenantData, 'whatsapp.provider')
            ?? $this->resolveProvider($company);

        $result = $this->client->tryPost('/api/whatsapp/messages/text', [
            'provider' => $provider,
            'instance' => $instance,
            'number' => $formattedNumber,
            'text' => $text,
        ], $token, 60);

        if ($result['error']) {
            return [
                'success' => false,
                'message' => $result['error'],
                'response' => [],
            ];
        }

        $response = $result['response'];
        $body = $this->client->decode($response);

        if (! $response->successful()) {
            return [
                'success' => false,
                'message' => $response->status() === 401
                    ? 'Token do painel IntegreAI inválido. Atualize INTEGREAI_PANEL_TOKEN no .env.'
                    : $this->client->errorMessage($response, 'Erro ao enviar mensagem pelo painel IntegreAI'),
                'response' => $body,
            ];
        }

        return [
            'success' => true,
            'message' => 'Enviado',
            'response' => $body,
        ];
    }

    protected function panelToken(): ?string
    {
        $token = config('services.integreai.panel_token');

        return is_string($token) && $token !== '' ? $token : null;
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

    protected function clearLocalWhatsappLink(Company $company): void
    {
        $company->update([
            'integreai_instance_id' => null,
            'api_session_whatsapp' => null,
            'api_token_whatsapp' => null,
            'api_status_whatsapp' => 'close',
        ]);
    }

    protected function clearStaleInstanceLink(Company $company, string $normalizedNumber): void
    {
        $tenantData = $this->fetchTenantData($company);
        $linkedPhone = isset($tenantData['whatsapp']['phone_number'])
            ? $this->formatPhoneNumber((string) $tenantData['whatsapp']['phone_number'])
            : null;

        if (! $linkedPhone || $linkedPhone === $normalizedNumber) {
            return;
        }

        if ($company->integreai_instance_id || $this->companyWhatsappNumber($company)) {
            $tenantId = $this->externalTenantId($company);
            $this->client->delete("/api/v1/tenants/{$tenantId}/whatsapp");
        }

        $this->clearLocalWhatsappLink($company);
    }

    protected function refreshTenantData(Company $company, array $fallback = []): array
    {
        $tenantData = $this->fetchTenantData($company);

        return $tenantData !== [] ? $tenantData : $fallback;
    }

    protected function findInstancesByNumber(Company $company, string $normalizedNumber): array
    {
        $company->update(['whatsapp' => $normalizedNumber]);
        $company->refresh();

        $provision = $this->ensureProvisioned($company);
        if ($provision['success']) {
            $instance = $this->instanceFromTenantData($provision['data'] ?? []);
            if ($instance && ($instance['whatsapp_number'] ?? '') === $normalizedNumber) {
                return [$instance];
            }
        }

        $tenantId = $this->externalTenantId($company);
        $response = $this->client->get("/api/v1/tenants/{$tenantId}");
        if ($response->successful()) {
            $instance = $this->instanceFromTenantData($this->unwrapData($this->client->decode($response)));
            if ($instance && ($instance['whatsapp_number'] ?? '') === $normalizedNumber) {
                return [$instance];
            }
        }

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
        $response = $this->client->get("/api/v1/tenants/{$tenantId}");

        if (! $response->successful()) {
            return [];
        }

        $instance = $this->instanceFromTenantData($this->unwrapData($this->client->decode($response)));

        return $instance ? [$instance] : [];
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
        if (isset($row['whatsapp']) && is_array($row['whatsapp'])) {
            $whatsapp = $row['whatsapp'];

            $row = array_merge($row, [
                'instance_id' => $row['instance_id'] ?? $whatsapp['instance_id'] ?? null,
                'whatsapp_number' => $whatsapp['phone_number'] ?? $whatsapp['whatsapp_number'] ?? null,
                'name' => $whatsapp['instance_name'] ?? $whatsapp['name'] ?? null,
                'provider' => $whatsapp['provider'] ?? null,
                'status' => $whatsapp['instance_status'] ?? $whatsapp['status'] ?? null,
            ]);
        }

        $id = $row['id'] ?? $row['instance_id'] ?? null;
        $number = $row['whatsapp_number'] ?? $row['number'] ?? $row['phone'] ?? $row['phone_number'] ?? null;
        $name = $row['name'] ?? $row['instance'] ?? $row['instance_name'] ?? $row['label'] ?? null;
        $provider = $row['provider'] ?? null;
        $status = $row['status'] ?? $row['state'] ?? $row['connection_state'] ?? $row['instance_status'] ?? null;

        if (! $id && ! $number && ! $name) {
            return null;
        }

        $digits = $number ? $this->formatPhoneNumber((string) $number) : null;
        $displayDigits = $digits && str_starts_with($digits, '55') ? substr($digits, 2) : $digits;

        return [
            'id' => $id ? (int) $id : null,
            'name' => (string) ($name ?: 'Instância'),
            'whatsapp_number' => $digits,
            'whatsapp_number_formatted' => $displayDigits ? formatPhone($displayDigits) : null,
            'provider' => $provider ? strtolower((string) $provider) : null,
            'status' => $status ? strtolower((string) $status) : null,
            'token' => $row['token'] ?? null,
            'company_id' => $row['company_id'] ?? null,
            'label' => trim(sprintf(
                '%s%s%s%s',
                $name ?: 'Instância',
                $displayDigits ? ' — ' . formatPhone($displayDigits) : '',
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
        $response = $this->client->get("/api/v1/tenants/{$tenantId}");

        if ($response->successful()) {
            return $response;
        }

        $response = $this->client->get("/api/v1/tenants/{$tenantId}/whatsapp/status");

        if ($response->successful()) {
            return $response;
        }

        return $this->client->get("/api/v1/tenants/{$tenantId}/whatsapp");
    }

    protected function instanceFromTenantData(array $data): ?array
    {
        if (isset($data['whatsapp']) && is_array($data['whatsapp'])) {
            $whatsapp = $data['whatsapp'];

            return $this->normalizeInstanceRow([
                'instance_id' => $data['instance_id'] ?? $whatsapp['instance_id'] ?? null,
                'whatsapp_number' => $whatsapp['phone_number'] ?? $whatsapp['whatsapp_number'] ?? null,
                'name' => $whatsapp['instance_name'] ?? $whatsapp['name'] ?? null,
                'provider' => $whatsapp['provider'] ?? null,
                'status' => $whatsapp['instance_status'] ?? $whatsapp['status'] ?? null,
            ]);
        }

        return $this->normalizeInstanceRow($data['instance'] ?? $data);
    }

    protected function isTenantWhatsappLinked(array $data, Company $company): bool
    {
        $instanceId = $data['instance_id'] ?? data_get($data, 'whatsapp.instance_id');
        if (! $instanceId) {
            return false;
        }

        $expected = $this->companyWhatsappNumber($company);
        $phone = data_get($data, 'whatsapp.phone_number');

        if ($expected && $phone) {
            return $this->formatPhoneNumber((string) $phone) === $expected;
        }

        if ($expected && $company->integreai_instance_id) {
            return (int) $instanceId === (int) $company->integreai_instance_id;
        }

        return true;
    }

    protected function applyTenantWhatsappData(Company $company, array $data): void
    {
        $updates = [];
        $instanceId = $data['instance_id'] ?? data_get($data, 'whatsapp.instance_id');
        $rawStatus = data_get($data, 'whatsapp.instance_status') ?? data_get($data, 'whatsapp.status');
        $phone = data_get($data, 'whatsapp.phone_number') ?? data_get($data, 'whatsapp.whatsapp_number');

        if ($instanceId) {
            $updates['integreai_instance_id'] = (int) $instanceId;
        }

        if ($phone) {
            $normalized = normalizeBrazilWhatsapp((string) $phone);
            if ($normalized) {
                $updates['whatsapp'] = $normalized;
            }
        }

        $sessionName = data_get($data, 'whatsapp.instance_name');
        if (is_string($sessionName) && $sessionName !== '') {
            $updates['api_session_whatsapp'] = $sessionName;
        }

        $token = data_get($data, 'whatsapp.token');
        if (is_string($token) && $token !== '') {
            $updates['api_token_whatsapp'] = $token;
        }

        $crmCompanyId = $data['company_id'] ?? null;
        if ($crmCompanyId) {
            $updates['integreai_company_id'] = (int) $crmCompanyId;
        }

        $provider = data_get($data, 'whatsapp.provider');
        if (is_string($provider) && $provider !== '') {
            $updates['whatsapp_provider'] = strtolower($provider);
        }

        if ($rawStatus) {
            $updates['api_status_whatsapp'] = $this->normalizeStatus((string) $rawStatus);
        }

        if ($updates !== []) {
            $company->update($updates);
        }
    }

    protected function extractRawStatus(array $data): string
    {
        $candidates = [
            data_get($data, 'whatsapp.instance_status'),
            data_get($data, 'whatsapp.status'),
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
