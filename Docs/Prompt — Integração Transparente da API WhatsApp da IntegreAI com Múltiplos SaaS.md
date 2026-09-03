# Implementar arquitetura de integração da IntegreAI com ClinicaSync, Cobrança Segura e IntegreShop

Preciso implementar uma arquitetura para que meus três SaaS utilizem a infraestrutura de WhatsApp da **IntegreAI** de forma totalmente transparente para o cliente final:

- ClinicaSync
- Cobrança Segura
- IntegreShop

O cliente final **não deve precisar criar uma conta na IntegreAI, fazer login na IntegreAI ou copiar tokens da IntegreAI**.

A integração deve ser construída agora de forma que, futuramente, seja simples migrar para o modelo de **Tech Provider + Embedded Signup da Meta**, sem precisar refazer a arquitetura dos três SaaS.

---

# 1. Arquitetura desejada

A arquitetura deve ficar:

```text
                    INTEGREAI
                        │
          ┌─────────────┼─────────────┐
          │             │             │
     ClinicaSync   Cobrança Segura  IntegreShop
          │             │             │
          └─────────────┼─────────────┘
                        │
                  API IntegreAI
                        │
                 WhatsApp / Meta
```

Os três SaaS serão **clientes de API da IntegreAI**.

Não criar três contas de usuário comuns na IntegreAI.

Criar o conceito de:

**API Client / Application**

Exemplo:

```text
IntegreAI
│
├── API Client: ClinicaSync
├── API Client: Cobrança Segura
└── API Client: IntegreShop
```

Cada aplicação terá suas próprias credenciais.

---

# 2. Credenciais por SaaS

Cada SaaS deverá possuir sua própria credencial no `.env`.

Exemplo:

### ClinicaSync

```env
INTEGREAI_API_URL=https://api.integreai.com.br
INTEGREAI_CLIENT_ID=...
INTEGREAI_CLIENT_SECRET=...
```

### Cobrança Segura

```env
INTEGREAI_API_URL=https://api.integreai.com.br
INTEGREAI_CLIENT_ID=...
INTEGREAI_CLIENT_SECRET=...
```

### IntegreShop

```env
INTEGREAI_API_URL=https://api.integreai.com.br
INTEGREAI_CLIENT_ID=...
INTEGREAI_CLIENT_SECRET=...
```

**Não utilizar um único token global compartilhado pelos três SaaS.**

Cada aplicação deve possuir sua própria identidade.

---

# 3. Segurança das credenciais

As credenciais:

```text
CLIENT_ID
CLIENT_SECRET
```

devem existir somente no backend de cada SaaS.

Nunca:

- enviar para frontend;
- armazenar em JavaScript;
- expor em API pública;
- colocar em logs;
- retornar para o navegador;
- colocar em URLs;
- armazenar no banco do cliente final.

O frontend deve conversar apenas com o backend do respectivo SaaS.

Exemplo:

```text
Navegador
    ↓
ClinicaSync Backend
    ↓
IntegreAI API
```

Nunca:

```text
Navegador
    ↓
IntegreAI diretamente
```

---

# 4. Identificação da aplicação

A IntegreAI precisa identificar qual SaaS está consumindo a API.

Exemplo:

```text
client_id
      ↓
IntegreAI
      ↓
application_id
      ↓
ClinicaSync
```

Criar uma estrutura semelhante a:

```text
api_clients
├── id
├── name
├── slug
├── client_id
├── client_secret_hash/encrypted_secret
├── status
├── allowed_scopes
├── created_at
└── updated_at
```

Exemplo:

```text
ClinicaSync
slug = clinicasync

Cobrança Segura
slug = cobranca-segura

IntegreShop
slug = integreshop
```

---

# 5. Não confundir API Client com Tenant

Essa separação é extremamente importante.

**API Client = qual SaaS está utilizando a IntegreAI.**

**Tenant = qual cliente daquele SaaS está utilizando o WhatsApp.**

Exemplo:

```text
IntegreAI
│
├── ClinicaSync
│     │
│     ├── Clínica ABC → Tenant 101
│     ├── Clínica XYZ → Tenant 102
│     └── Clínica DEF → Tenant 103
│
├── Cobrança Segura
│     │
│     ├── Empresa A → Tenant 201
│     └── Empresa B → Tenant 202
│
└── IntegreShop
      │
      ├── Loja A → Tenant 301
      └── Loja B → Tenant 302
```

Nunca usar somente:

```text
company_id
```

para identificar globalmente um tenant da IntegreAI.

O identificador deve considerar também a aplicação de origem.

---

# 6. Identificador externo do tenant

Cada SaaS deve enviar um identificador externo único para a IntegreAI.

Exemplos:

### ClinicaSync

```text
clinicasync:company:157
```

### Cobrança Segura

```text
cobrancasegura:company:42
```

### IntegreShop

```text
integreshop:store:89
```

A IntegreAI deverá armazenar:

```text
application_id
external_tenant_id
```

e garantir unicidade:

```text
UNIQUE(application_id, external_tenant_id)
```

Assim não existe risco de:

```text
ClinicaSync company 157
```

colidir com:

```text
IntegreShop company 157
```

---

# 7. Provisionamento automático

Quando o cliente ativar o WhatsApp dentro de qualquer SaaS:

```text
Cliente
 ↓
ClinicaSync
 ↓
Configurações → WhatsApp
 ↓
Conectar WhatsApp
 ↓
ClinicaSync Backend
 ↓
IntegreAI API
 ↓
Provisionar tenant/conexão
```

A IntegreAI deve criar ou localizar automaticamente o tenant correspondente.

Endpoint conceitual:

```http
POST /api/v1/tenants/provision
```

Payload:

```json
{
  "external_tenant_id": "clinicasync:company:157",
  "name": "Clínica ABC"
}
```

Se o tenant já existir, retornar o existente.

**Não criar duplicidade.**

A operação deve ser idempotente.

---

# 8. Conexão WhatsApp

Depois de provisionar o tenant:

```text
ClinicaSync
     ↓
IntegreAI
     ↓
Tenant
     ↓
WhatsApp Connection / Instance
```

A conexão deverá ficar vinculada ao tenant correto.

Exemplo:

```text
IntegreAI

Tenant:
Clínica ABC

Application:
ClinicaSync

External ID:
clinicasync:company:157

WhatsApp:
+55...
```

---

# 9. Banco da IntegreAI

Criar/adaptar uma estrutura semelhante a:

```text
api_clients
    ↓
tenants
    ↓
whatsapp_connections
```

Relacionamento:

```text
API Client
   │
   └── N Tenants
           │
           └── N WhatsApp Connections
```

Exemplo:

```text
ClinicaSync
    ↓
Clínica ABC
    ↓
WhatsApp ABC
```

---

# 10. Banco dos SaaS

Cada SaaS deve armazenar apenas a referência necessária para localizar sua conexão na IntegreAI.

Exemplo no ClinicaSync:

```text
whatsapp_integrations
├── company_id
├── integreai_tenant_id
├── integreai_connection_id
├── status
└── metadata
```

Não armazenar o token da Meta no ClinicaSync.

O token da Meta deve permanecer sob responsabilidade da IntegreAI.

---

# 11. Fluxo transparente para o cliente

O cliente deve enxergar apenas:

```text
Configurações
    ↓
WhatsApp
    ↓
[ Conectar WhatsApp ]
```

Nunca mostrar:

```text
IntegreAI
API Client
Client ID
Client Secret
Tenant ID
Access Token
```

Esses conceitos são internos.

---

# 12. API da IntegreAI

Criar uma API versionada.

Exemplo:

```text
/api/v1/
```

Endpoints conceituais:

```text
POST   /tenants/provision
GET    /tenants/{external_id}

POST   /tenants/{id}/whatsapp
GET    /tenants/{id}/whatsapp
DELETE /tenants/{id}/whatsapp

POST   /tenants/{id}/messages
GET    /tenants/{id}/conversations

POST   /tenants/{id}/webhook
```

Adaptar aos endpoints que já existem na IntegreAI.

**Não duplicar endpoints existentes.**

---

# 13. Autenticação da API

Implementar a autenticação entre SaaS e IntegreAI de forma que futuramente seja fácil migrar para:

**OAuth 2.0 Client Credentials**

ou mecanismo equivalente de autenticação machine-to-machine.

Idealmente:

```text
ClinicaSync
    ↓
Client ID + Client Secret
    ↓
IntegreAI
    ↓
Access Token
    ↓
API
```

O token de acesso deve possuir validade e ser renovável.

Se a arquitetura atual utilizar API Keys, estruturar o código por meio de um serviço de autenticação abstrato para que posteriormente seja possível substituir por OAuth sem alterar toda a integração.

---

# 14. Criar SDK/Service nos SaaS

Não espalhar chamadas HTTP da IntegreAI pelo projeto.

Criar um serviço centralizado, por exemplo:

```text
IntegreAIService
```

ou:

```text
IntegreAIClient
```

Responsável por:

- autenticação;
- provisionamento;
- criação de conexão;
- envio de mensagens;
- consulta de status;
- consulta de conversas;
- tratamento de erros;
- retry;
- timeout;
- logs seguros.

Exemplo conceitual:

```php
$integreAI->tenants()->provision(...);

$integreAI->whatsapp()->connect(...);

$integreAI->messages()->send(...);
```

Cada SaaS terá sua própria configuração.

---

# 15. Retry e idempotência

Implementar proteção contra duplicidade.

Se o SaaS enviar duas vezes:

```text
provision tenant
```

não criar dois tenants.

O mesmo vale para:

- conexão WhatsApp;
- envio de mensagens;
- webhooks;
- operações de sincronização.

Utilizar:

```text
idempotency_key
```

quando aplicável.

---

# 16. Webhooks

A arquitetura deve prever que a IntegreAI receba os eventos do WhatsApp e consiga identificar:

```text
API Client
+
Tenant
+
WhatsApp Connection
```

Exemplo:

```text
Meta
 ↓
IntegreAI Webhook
 ↓
WhatsApp Connection
 ↓
Tenant
 ↓
API Client
 ↓
ClinicaSync
```

A IntegreAI então encaminha o evento ao SaaS correto.

Exemplo:

```text
Nova mensagem
      ↓
IntegreAI
      ↓
ClinicaSync
      ↓
Empresa 157
      ↓
Inbox
```

---

# 17. Não misturar dados entre SaaS

Garantir isolamento absoluto.

Uma requisição autenticada como:

```text
ClinicaSync
```

não pode acessar:

```text
IntegreShop
```

nem:

```text
Cobrança Segura
```

Da mesma forma, um tenant de um SaaS não pode acessar tenant de outro SaaS.

Todas as consultas devem validar:

```text
application_id
+
tenant_id
```

---

# 18. Futuro Tech Provider

A arquitetura deve ser preparada para futuramente alterar somente o processo de onboarding.

### Agora

```text
Cliente
 ↓
Configuração assistida
 ↓
Meta / Cloud API
 ↓
IntegreAI
```

### Futuramente

```text
Cliente
 ↓
SaaS
 ↓
IntegreAI
 ↓
Embedded Signup
 ↓
Meta
```

O restante da arquitetura deve continuar igual:

```text
SaaS
 ↓
IntegreAI API
 ↓
Tenant
 ↓
WhatsApp Connection
```

Não criar dependências no código dos SaaS que assumam que o cliente criou manualmente um App Meta.

---

# 19. Provider/Connection Mode

Preparar o modelo de conexão para suportar diferentes formas de onboarding.

Por exemplo:

```text
connection_mode

customer_cloud_api
embedded_signup
```

Hoje:

```text
customer_cloud_api
```

Futuramente:

```text
embedded_signup
```

Isso deve ser interno.

O SaaS não deve precisar conhecer os detalhes da implementação da Meta.

---

# 20. Abstração importante

Criar uma camada:

```text
SaaS
 ↓
IntegreAI API
 ↓
WhatsApp Provider
 ↓
Meta
```

O SaaS nunca deve conversar diretamente com:

```text
Meta Graph API
```

O objetivo é que a IntegreAI seja a camada responsável pelo WhatsApp.

---

# 21. Benefício esperado

No futuro, se eu trocar:

```text
Meta Cloud API
```

por:

```text
Outro BSP
```

ou:

```text
Embedded Signup
```

os meus SaaS não precisam mudar.

Somente a IntegreAI altera a implementação interna.

---

# 22. Provisionamento da empresa

Quando uma nova empresa for criada em qualquer SaaS, **não criar automaticamente uma conta de usuário na IntegreAI**.

Criar somente o tenant técnico quando for necessário, preferencialmente quando o cliente iniciar a configuração do WhatsApp.

Exemplo:

```text
Nova empresa
 ↓
Não precisa criar usuário IntegreAI
 ↓
Cliente acessa WhatsApp
 ↓
Provisionar tenant IntegreAI
```

Isso evita criar tenants desnecessários.

---

# 23. Exemplo completo

### Cliente do ClinicaSync

```text
Clínica ABC
company_id = 157
```

Ao conectar WhatsApp:

```text
ClinicaSync
client_id = xxx
external_tenant_id = clinicasync:company:157
```

IntegreAI cria:

```text
Application:
ClinicaSync

Tenant:
Clínica ABC

External ID:
clinicasync:company:157
```

Depois:

```text
Tenant
 ↓
WhatsApp Connection
 ↓
phone_number_id
 ↓
Meta
```

---

# 24. Tratamento de erros

Criar respostas padronizadas na API.

Exemplos:

```json
{
  "success": false,
  "error": {
    "code": "TENANT_NOT_FOUND",
    "message": "Tenant não encontrado"
  }
}
```

Outros códigos:

```text
INVALID_CLIENT
INVALID_TENANT
UNAUTHORIZED
FORBIDDEN
CONNECTION_NOT_FOUND
WHATSAPP_NOT_CONNECTED
RATE_LIMITED
PROVIDER_ERROR
```

Não retornar informações internas ou tokens.

---

# 25. Logs

Registrar:

- API Client;
- endpoint;
- tenant;
- operação;
- status;
- duração;
- correlation ID.

Nunca registrar:

- client secret;
- access token;
- token da Meta;
- dados sensíveis.

Criar um `correlation_id` para rastrear uma requisição entre:

```text
ClinicaSync
 ↓
IntegreAI
 ↓
Meta
```

---

# 26. Rate limiting

Implementar rate limit por:

```text
API Client
+
Tenant
```

para evitar que um SaaS ou cliente consuma recursos excessivos.

Exemplo:

```text
ClinicaSync
   ↓
Tenant 157
   ↓
Rate limit
```

---

# 27. Critérios de aceitação

- [ ] Existem 3 API Clients independentes.
- [ ] ClinicaSync possui credencial própria.
- [ ] Cobrança Segura possui credencial própria.
- [ ] IntegreShop possui credencial própria.
- [ ] Credenciais ficam somente no backend.
- [ ] Cliente final não precisa criar conta na IntegreAI.
- [ ] Cliente final não vê tokens.
- [ ] Cliente final não vê Client ID/Secret.
- [ ] É possível provisionar tenant automaticamente.
- [ ] Provisionamento é idempotente.
- [ ] Cada tenant possui um `external_tenant_id`.
- [ ] `application_id + external_tenant_id` é único.
- [ ] Cada tenant pode possuir sua conexão WhatsApp.
- [ ] Dados dos SaaS ficam isolados.
- [ ] Dados dos tenants ficam isolados.
- [ ] Webhooks conseguem identificar o SaaS correto.
- [ ] Webhooks conseguem identificar o tenant correto.
- [ ] Jobs preservam o contexto do SaaS e tenant.
- [ ] Tokens não aparecem em logs.
- [ ] Existe tratamento de erros.
- [ ] Existe retry quando apropriado.
- [ ] Existe idempotência.
- [ ] Existe versionamento da API.
- [ ] A arquitetura não depende diretamente do onboarding manual da Meta.
- [ ] A arquitetura permite futuramente implementar Embedded Signup.
- [ ] Os SaaS não precisam ser modificados quando a IntegreAI migrar para Tech Provider.

---

# 28. Regra arquitetural definitiva

A regra deve ser:

```text
API Client
    ↓
identifica o SaaS

Tenant
    ↓
identifica o cliente daquele SaaS

WhatsApp Connection
    ↓
identifica o número/conexão

Provider
    ↓
identifica como o WhatsApp é conectado

Meta/BSP
    ↓
infraestrutura externa
```

Separar completamente:

**SaaS → Tenant → WhatsApp → Provider**

para que futuramente eu possa transformar a IntegreAI em Tech Provider sem precisar alterar a arquitetura dos meus três SaaS.

Antes de implementar, faça uma análise da arquitetura atual da IntegreAI e dos três SaaS, identifique o que já existe e adapte o código existente em vez de criar sistemas duplicados.