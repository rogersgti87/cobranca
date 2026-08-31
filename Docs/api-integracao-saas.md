# API de integração SaaS — WhatsApp transparente

**Data:** 2026-08-27  
**Escopo:** ClinicaSync, Cobrança Segura e IntegreShop como clientes M2M da IntegreAI, sem conta do cliente final no painel.

## Escolha do caminho de integração

| Caminho | Auth | Quando usar |
|---------|------|-------------|
| **Painel `/app/api`** (Sanctum) | Token gerado em Painel → API | SaaS com conta IntegreAI; mesma empresa/instâncias do CRM; doc Swagger + download `.md` por provedor |
| **Esta API** (`/api/v1/tenants/*`) | `ik_live_*` (API Client M2M) | SaaS sem login no painel; muitos tenants externos; provisionamento idempotente |

Guia Sanctum + mapa YCloud/EVOGO: [`api-painel-app-integracao.md`](./api-painel-app-integracao.md).

## O quê / por quê

A IntegreAI expõe uma camada **API Client → Tenant externo → Company/Instance única** para que cada SaaS consuma WhatsApp via backend próprio, usando o **mesmo padrão de API key** já existente em `/api/v1` (`ik_live_*`), sem `client_id`/`client_secret` nem fluxo OAuth.

**Não há camada SaaS duplicada:** o tenant externo (`ApiTenant`) é apenas o mapeamento `external_tenant_id` → `company_id` interno (empresa CRM). A instância WhatsApp (`instances`) é **única** por número — compartilhada entre painel CRM e SaaS.

```text
SaaS Backend (INTEGREAI_API_KEY=ik_live_…)
    ↓ Bearer ou X-Api-Key
IntegreAI API /api/v1/tenants/*
    ↓
ApiTenant (external_tenant_id → company_id, opcional instance_id)
    ↓
Instance (única) → Provider (YCloud / EVOGO / Meta)
```

CRM em produção (`/api/whatsapp`, `/api/v1` Inbox) continua usando a mesma `company_id` e `instances` — sem segunda sessão EVOGO.

## Anti-duplicidade (empresa e instância)

O desenho evita **duas instâncias WhatsApp** para o mesmo número. O **`whatsapp_number` é a chave única** para descobrir a qual `company_id` a instância pertence.

### Resolução automática por número

| Etapa | Comportamento |
|-------|----------------|
| **Provision** com `whatsapp_number` | Se o número já existe em `instances`, o tenant é vinculado à **`company_id` dona da instância** (ignora `company_id` ausente; conflito com `company_id` diferente → **422**). |
| **Connect** com `whatsapp_number` | Localiza a instância pelo número, **alinha** `api_tenants.company_id` à empresa da instância e faz o link — **sem** criar nova sessão no provider. |
| **Connect** / **create** com número já cadastrado | Mesmo com `link_existing: false`, reutiliza a instância existente (número único). |

```json
POST /api/v1/tenants/provision
{
  "external_tenant_id": "cobranca:empresa:157",
  "name": "Clínica ABC",
  "whatsapp_number": "5511999999999"
}
```

Se `5511999999999` já estiver no CRM (`company_id: 42`), o tenant é criado/atualizado em `company_id: 42` — **sem** nova empresa SaaS-only.

```json
POST /api/v1/tenants/{id}/whatsapp
{
  "whatsapp_number": "5511999999999"
}
```

Vincula à instância existente e corrige `company_id` do tenant se necessário.

### O que está garantido hoje

| Regra | Comportamento |
|-------|----------------|
| **Provision idempotente** | Mesmo `external_tenant_id` + mesmo `api_client` → retorna o **mesmo** `ApiTenant` (não cria outro). |
| **`company_id` divergente** | Repetir provision com `company_id` diferente do já gravado → **422** (`Tenant já provisionado para outra empresa`). |
| **Auto-link WhatsApp** | `POST /tenants/{id}/whatsapp` **sem body** (padrão `link_existing=true`) → vincula à instância **já existente** da `company_id` (`TenantInstanceResolver::findPrimaryForCompany`). **Não** cria sessão EVOGO/YCloud nova. |
| **Instância já vinculada** | Segundo `POST whatsapp` no mesmo tenant (sem `force`) → retorna o vínculo atual (200). |
| **Número no painel** | Criar instância em **Instâncias** com `whatsapp_number` já usado em qualquer registro → **422** (bloqueio global no `InstanceController`). |
| **Nome de instância** | `instances.name` é único no sistema (`unique:instances` no painel; validação global na API Sanctum). |

### Fluxo correto (empresa já existe no CRM)

**Opção A — pelo número (recomendado):**

```text
1. Empresa já tem instância 5511999999999 no painel (company_id = 42)
2. Cobrança Segura: POST /tenants/provision { external_tenant_id, name, whatsapp_number: "5511999999999" }
3. IntegreAI resolve company_id = 42 automaticamente
4. POST /tenants/{id}/whatsapp { whatsapp_number: "5511999999999" } → link na instância existente
```

**Opção B — pelo company_id:**

```text
1. Empresa já tem instância no painel IntegreAI (company_id = 42)
2. Cobrança Segura: POST /tenants/provision { external_tenant_id, name, company_id: 42 }
3. Cobrança Segura: POST /tenants/{id}/whatsapp   { }   ← body vazio
4. IntegreAI vincula o tenant à instância existente — mesma sessão, mesmo número
```

Vice-versa (SaaS provisionou primeiro **com** `company_id` compartilhado): o admin abre o painel e vê a **mesma** instância; novo cadastro com o **mesmo número** é bloqueado.

### O que ainda pode duplicar (evitar)

| Uso incorreto | Risco |
|---------------|--------|
| `provision` **sem** `company_id` | Cria **nova** `Company` isolada, mesmo que a clínica já exista no CRM. |
| `POST whatsapp` com `link_existing: false` e número **novo** | Cria instância (único caso em que nova sessão é aberta). |
| `POST whatsapp` com `link_existing: false` e número **já cadastrado** | Ignorado — reutiliza instância pelo número. |
| `POST whatsapp` com `force: true` | Permite revincular; use só em migração controlada. |
| Número diferente na segunda instância | Painel e API M2M **não** impedem duas instâncias na mesma empresa com **números diferentes** — é caso de negócio, não bug. |
| API Sanctum (`/app/api`) com outro `user_id` | Instâncias Sanctum são por `user_id`; para compartilhar com o CRM, use **M2M** com `company_id` ou o mesmo usuário dono das instâncias. |

### Resumo para Cobrança Segura / ClinicaSync

- **Preferir** `whatsapp_number` no provision e no connect — a IntegreAI resolve a empresa automaticamente.
- Alternativa: `company_id` explícito + `POST whatsapp` vazio (auto-link por empresa).
- **Nunca** omitir número e `company_id` quando a empresa já existe no CRM (cria empresa SaaS-only isolada).

## Modelos e tabelas

| Tabela | Model | Função |
|--------|-------|--------|
| `api_clients` | `ApiClient` | SaaS — token `ik_live_*` em `key_hash` |
| `api_tenants` | `ApiTenant` | Mapeamento externo → `company_id`; `instance_id` opcional |
| `whatsapp_connections` | `WhatsAppConnection` | Legado/espelho; nova lógica prioriza `api_tenants.instance_id` |

### Tenant interno único (`company_id`)

- **Provision com `whatsapp_number`:** resolve `company_id` pela instância existente (número único).
- **Provision sem `company_id` e sem número conhecido:** cria `Company` isolada (SaaS-only).
- **Provision com `company_id`:** vincula tenant à empresa CRM existente (compartilha instâncias).
- **`TenantInstanceResolver`:** resolve `Instance` por `whatsapp_number`, `tenant.instance_id` ou instância primária da `company_id`.

## Autenticação (padrão `/api/v1`)

Igual à API pública do Inbox/CRM (`company_api_keys`):

```http
Authorization: Bearer ik_live_xxxxxxxx
```

ou

```http
X-Api-Key: ik_live_xxxxxxxx
```

**Sem** `POST /oauth/token`. **Sem** `client_id` / `client_secret`.

O token do SaaS é distinto dos tokens de empresa (`company_api_keys`): autentica contra `api_clients.key_hash`.

Header opcional: `X-Correlation-Id` (rastreio SaaS → IntegreAI → provider).

## Endpoints principais

| Método | Path | Descrição |
|--------|------|-----------|
| POST | `/api/v1/tenants/provision` | Provisiona tenant (idempotente) |
| GET | `/api/v1/tenants/{external_tenant_id}` | Busca por ID externo |
| POST | `/api/v1/tenants/{id}/whatsapp` | Vincula ou cria WhatsApp |
| GET | `/api/v1/tenants/{id}/whatsapp` | Status da conexão |
| GET | `/api/v1/tenants/{id}/whatsapp/status` | Sync status provider |
| GET | `/api/v1/tenants/{id}/whatsapp/qrcode` | QR (EVOGO/sessão) |
| DELETE | `/api/v1/tenants/{id}/whatsapp` | Desvincula tenant (padrão) |
| POST | `/api/v1/tenants/{id}/messages` | Enviar texto |
| GET | `/api/v1/tenants/{id}/conversations` | Listar conversas |
| POST | `/api/v1/tenants/{id}/webhook` | URL de webhook do tenant |

### Provisionamento

**SaaS-only (nova empresa):**

```json
POST /api/v1/tenants/provision
Authorization: Bearer ik_live_…

{
  "external_tenant_id": "clinicasync:company:157",
  "name": "Clínica ABC"
}
```

**Vincular empresa CRM existente (por `company_id`):**

```json
{
  "external_tenant_id": "clinicasync:company:157",
  "name": "Clínica ABC",
  "company_id": 42
}
```

**Vincular empresa CRM existente (por `whatsapp_number` — recomendado):**

```json
{
  "external_tenant_id": "cobranca:empresa:157",
  "name": "Clínica ABC",
  "whatsapp_number": "5511999999999"
}
```

A IntegreAI localiza a instância pelo número e define `company_id` automaticamente. Se informar `company_id` diferente do dono do número → **422**.

Idempotente: repetir com o mesmo `external_tenant_id` retorna o mesmo tenant. `company_id` divergente → erro 422.

### Conectar WhatsApp

**Auto-link** (instância já existente na `company_id`):

```json
POST /api/v1/tenants/{id}/whatsapp
Authorization: Bearer ik_live_…

{}
```

**Auto-link pelo número** (recomendado quando o SaaS conhece o WhatsApp):

```json
POST /api/v1/tenants/{id}/whatsapp
Authorization: Bearer ik_live_…

{
  "whatsapp_number": "5511999999999"
}
```

Ou explícito:

```json
{
  "instance_id": 15
}
```

**Criar nova instância** (somente se não há instância na empresa):

```json
{
  "link_existing": false,
  "provider": "evogo",
  "instance": "clinica-abc",
  "whatsapp_number": "5511999999999"
}
```

`force: true` recria vínculo se já linkado.

### Desconectar (`DELETE`)

Por padrão **desvincula** o tenant (`instance_id = null`). A instância no CRM/provider **permanece ativa**.

Para desconectar no provider também (destrutivo):

```json
DELETE /api/v1/tenants/{id}/whatsapp
Content-Type: application/json

{
  "disconnect_provider": true
}
```

## Webhooks (IntegreAI → SaaS)

Inbound → `SaaSWebhookResolver` resolve tenants por `instance_id` ou `company_id` → `tenant.webhook_url` ou `api_client.webhook_url`.

Query params adicionados: `tenant_id`, `external_tenant_id`, `application`.

## Erros padronizados

```json
{
  "success": false,
  "error": {
    "code": "TENANT_NOT_FOUND",
    "message": "Tenant não encontrado"
  }
}
```

## Rate limit

`120 req/min` por `api_client + tenant`.

## Setup local

```bash
php artisan migrate
php artisan api:seed-clients
```

Configurar no `.env` de cada SaaS:

```env
INTEGREAI_API_URL=https://api.integreai.com.br
INTEGREAI_API_KEY=ik_live_xxxxxxxx
```

**Nunca** colocar o token no frontend.

## Arquivos principais

| Camada | Path |
|--------|------|
| API keys | `app/Services/Api/Integration/ApiClientKeyService.php` |
| Auth middleware | `app/Http/Middleware/AuthenticateApiClient.php` |
| Provision | `app/Services/Api/Integration/TenantProvisioningService.php` |
| Instance link | `app/Services/Api/Integration/TenantInstanceResolver.php` |
| WhatsApp | `app/Services/Api/Integration/TenantWhatsAppService.php` |
| Webhook SaaS | `app/Services/Api/Integration/SaaSWebhookResolver.php` |
| Rotas | `routes/api.php` (middleware `auth.api_client`) |
| Seeder | `database/seeders/ApiClientSeeder.php` |

## Validação

1. `api:seed-clients` → copiar `ik_live_*`
2. `POST /api/v1/tenants/provision` com Bearer → tenant criado
3. Repetir provision → mesmo `id` (idempotente)
4. Instância no CRM (`company_id` X) → `provision` com `company_id: X` → `POST whatsapp` sem body → link sem nova sessão
5. `DELETE whatsapp` → tenant desvinculado; CRM continua enviando

## Histórico

| Data | Mudança |
|------|---------|
| 2026-08-27 | API Client M2M, tenants externos, whatsapp_connections, endpoints `/api/v1/tenants/*` |
| 2026-08-27 | Auth alinhada ao padrão `ik_live_*` (remove OAuth client_credentials) |
| 2026-08-27 | Tenant único: `company_id` no provision, `instance_id` no tenant, link sem duplicar Instance/EVOGO; DELETE desvincula por padrão |
| 2026-08-31 | Tabela de escolha Sanctum (`/app/api`) vs M2M (`ik_live_*`); link para [`api-painel-app-integracao.md`](./api-painel-app-integracao.md). |
| 2026-08-31 | Seção anti-duplicidade: auto-link, `company_id`, riscos de `link_existing: false` e provision sem `company_id`. |
| 2026-08-31 | Resolução de `company_id` e link de instância por `whatsapp_number` (número único). |
