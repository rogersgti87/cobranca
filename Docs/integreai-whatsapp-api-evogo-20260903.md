# IntegreAI WhatsApp API — EVOGO

- **Provedor:** `evogo`
- **Base URL:** `https://integreai.com.br/api`
- **Gerado em:** 2026-09-03 10:32
- **Fonte:** OpenAPI filtrado do painel (`/app/api`)

> Este arquivo é gerado automaticamente a partir da especificação OpenAPI.
> Novos endpoints documentados no OpenAPI entram neste download sem alteração manual.

## Autenticação

1. Gere o token em **Painel → API → Tokens**.
2. Envie em todas as rotas autenticadas:

```http
Authorization: Bearer SEU_TOKEN
Content-Type: application/json
Accept: application/json
```

3. Informe `provider` = `evogo` (query ou body, conforme o endpoint).

## Visão geral

API WhatsApp via EVOGO.

Autenticação: envie o token Bearer gerado no painel em **API → Tokens**.

Todos os endpoints exigem o campo `provider` com valor `evogo`.

**Inbox v1:** endpoints `/v1/conversations…` para reply, forward, mídia, apagar mensagem e apagar conversa — usados pelo painel e disponíveis com o mesmo token Sanctum.

## Índice de endpoints

### Instâncias

- [`GET /whatsapp/instances`](#get-whatsapp-instances) — Listar instâncias do usuário
- [`POST /whatsapp/instances`](#post-whatsapp-instances) — Criar instância
- [`GET /whatsapp/instances/{instance}/status`](#get-whatsapp-instances-instance-status) — Consultar status da instância
- [`GET /whatsapp/instances/{instance}/qrcode`](#get-whatsapp-instances-instance-qrcode) — Obter QR Code (WAHA e EVOGO)
- [`POST /whatsapp/instances/{instance}/disconnect`](#post-whatsapp-instances-instance-disconnect) — Desconectar instância
- [`DELETE /whatsapp/instances/{instance}`](#delete-whatsapp-instances-instance) — Remover instância
- [`PUT /whatsapp/instances/{instance}/webhook`](#put-whatsapp-instances-instance-webhook) — Sincronizar webhook (automático)

### Mensagens

- [`POST /whatsapp/messages/text`](#post-whatsapp-messages-text) — Enviar mensagem de texto
- [`POST /whatsapp/messages/file`](#post-whatsapp-messages-file) — Enviar arquivo (documento)
- [`POST /whatsapp/messages/image`](#post-whatsapp-messages-image) — Enviar imagem
- [`POST /whatsapp/messages/media`](#post-whatsapp-messages-media) — Enviar mídia tipada (image|audio|video|document)
- [`POST /whatsapp/messages/reply`](#post-whatsapp-messages-reply) — Responder citando mensagem (quote)
- [`POST /whatsapp/messages/forward`](#post-whatsapp-messages-forward) — Encaminhar conteúdo de mensagem
- [`DELETE /whatsapp/messages`](#delete-whatsapp-messages) — Apagar mensagem no provider (best-effort)

### Webhooks

- [`POST /whatsapp/webhook/evogo`](#post-whatsapp-webhook-evogo) — Webhook EVOGO

### Contatos

- [`GET /whatsapp/contacts/profile`](#get-whatsapp-contacts-profile) — Perfil do contato (nome/about)
- [`GET /whatsapp/contacts/picture`](#get-whatsapp-contacts-picture) — Foto de perfil do contato

### Inbox v1

- [`GET /v1/conversations`](#get-v1-conversations) — Listar conversas
- [`GET /v1/conversations/agents`](#get-v1-conversations-agents) — Agentes da empresa (assignees)
- [`GET /v1/conversations/{conversation}`](#get-v1-conversations-conversation) — Detalhe da conversa
- [`PATCH /v1/conversations/{conversation}`](#patch-v1-conversations-conversation) — Atualizar status / prioridade / assignee
- [`DELETE /v1/conversations/{conversation}`](#delete-v1-conversations-conversation) — Apagar conversa inteira do Inbox
- [`POST /v1/conversations/{conversation}/read`](#post-v1-conversations-conversation-read) — Marcar como lida
- [`POST /v1/conversations/{conversation}/unread`](#post-v1-conversations-conversation-unread) — Marcar como não lida
- [`GET /v1/conversations/{conversation}/profile`](#get-v1-conversations-conversation-profile) — Perfil do contato (sync sob demanda)
- [`GET /v1/conversations/{conversation}/messages`](#get-v1-conversations-conversation-messages) — Listar mensagens
- [`POST /v1/conversations/{conversation}/messages`](#post-v1-conversations-conversation-messages) — Enviar texto (alias de reply)
- [`POST /v1/conversations/{conversation}/messages/reply`](#post-v1-conversations-conversation-messages-reply) — Responder texto via Conversation Engine
- [`POST /v1/conversations/{conversation}/messages/media`](#post-v1-conversations-conversation-messages-media) — Enviar mídia (arquivo ou URL)
- [`POST /v1/conversations/{conversation}/messages/forward`](#post-v1-conversations-conversation-messages-forward) — Encaminhar mensagem para outra conversa
- [`GET /v1/conversations/tags`](#get-v1-conversations-tags) — Listar tags da empresa
- [`POST /v1/conversations/tags`](#post-v1-conversations-tags) — Criar tag
- [`PUT /v1/conversations/{conversation}/tags`](#put-v1-conversations-conversation-tags) — Sincronizar tags da conversa
- [`GET /v1/conversations/{conversation}/notes`](#get-v1-conversations-conversation-notes) — Listar notas
- [`POST /v1/conversations/{conversation}/notes`](#post-v1-conversations-conversation-notes) — Criar nota
- [`GET /v1/internal/channels`](#get-v1-internal-channels) — Listar canais do usuário
- [`POST /v1/internal/channels`](#post-v1-internal-channels) — Criar canal (group/team/direct)
- [`POST /v1/internal/channels/direct`](#post-v1-internal-channels-direct) — Abrir ou reutilizar DM 1:1
- [`GET /v1/internal/channels/{channel}/messages`](#get-v1-internal-channels-channel-messages) — Listar mensagens do canal
- [`POST /v1/internal/channels/{channel}/messages`](#post-v1-internal-channels-channel-messages) — Enviar mensagem no canal
- [`DELETE /v1/conversations/{conversation}/messages/{message}`](#delete-v1-conversations-conversation-messages-message) — Apagar mensagem (soft-delete)

## Endpoints

### Instâncias

<a id="get-whatsapp-instances"></a>

#### `GET /whatsapp/instances`

**Listar instâncias do usuário**

Retorna todas as instâncias WhatsApp pertencentes ao usuário autenticado.

- **URL:** `https://integreai.com.br/api/whatsapp/instances`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `provider` | query | não | Filtrar por provedor (`string`: ycloud|evogo|meta) |
| `status` | query | não | Filtrar por status de conexão (`string`: open|close) |
| `search` | query | não | Buscar por nome, número ou ID da instância (string) |

**Respostas**

- `200`
- `422`

```bash
curl -X GET 'https://integreai.com.br/api/whatsapp/instances?provider=ycloud&status=open&search=VALUE' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="post-whatsapp-instances"></a>

#### `POST /whatsapp/instances`

**Criar instância**

- **URL:** `https://integreai.com.br/api/whatsapp/instances`
- **Autenticação:** Bearer token (Sanctum)

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `provider` | string | sim | Enum: `ycloud`, `evogo`, `meta` |
| `instance` | string | sim |  |
| `whatsapp_number` | string | sim | DDI + DDD + número do WhatsApp, com 12 ou 13 dígitos. Obrigatório para todos os provedores; no WAHA é salvo localmente, mas não enviado ao provedor na criação. |
| `start` | boolean | não | WAHA: iniciar sessão após criar Default: `true` |
| `phone_number_id` | string | não | Meta: ID do número WhatsApp Business |
| `access_token` | string | não | Meta: token de acesso |
| `waba_id` | string | não | Meta: ID da conta WhatsApp Business |

Exemplo (ycloud (preferir tela BYOC no painel)):

```json
{
    "provider": "ycloud",
    "instance": "minha-loja",
    "whatsapp_number": "5511999999999"
}
```

Exemplo (EVOGO):

```json
{
    "provider": "evogo",
    "instance": "loja-evogo",
    "whatsapp_number": "5511999999999"
}
```

Exemplo (Meta (WhatsApp Cloud API)):

```json
{
    "provider": "meta",
    "instance": "loja-meta",
    "whatsapp_number": "5511999999999",
    "phone_number_id": "123456789012345",
    "access_token": "EAAG...",
    "waba_id": "123456789012345"
}
```

**Respostas**

- `200`
- `422`

```bash
curl -X POST 'https://integreai.com.br/api/whatsapp/instances' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud","instance":"minha-loja","whatsapp_number":"5511999999999"}'
```

<a id="get-whatsapp-instances-instance-status"></a>

#### `GET /whatsapp/instances/{instance}/status`

**Consultar status da instância**

- **URL:** `https://integreai.com.br/api/whatsapp/instances/{instance}/status`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |
| `` |  | não |  |
| `` |  | não |  |
| `` |  | não |  |

**Respostas**

- `200`
- `422`

```bash
curl -X GET 'https://integreai.com.br/api/whatsapp/instances/{instance}/status' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="get-whatsapp-instances-instance-qrcode"></a>

#### `GET /whatsapp/instances/{instance}/qrcode`

**Obter QR Code (WAHA e EVOGO)**

Somente EVOGO. YCloud oficial não usa QR Code.

- **URL:** `https://integreai.com.br/api/whatsapp/instances/{instance}/qrcode`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |
| `` |  | não |  |

**Respostas**

- `200`
- `422`

```bash
curl -X GET 'https://integreai.com.br/api/whatsapp/instances/{instance}/qrcode' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="post-whatsapp-instances-instance-disconnect"></a>

#### `POST /whatsapp/instances/{instance}/disconnect`

**Desconectar instância**

- **URL:** `https://integreai.com.br/api/whatsapp/instances/{instance}/disconnect`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |
| `` |  | não |  |
| `` |  | não |  |
| `` |  | não |  |

**Respostas**

- `200`
- `422`

```bash
curl -X POST 'https://integreai.com.br/api/whatsapp/instances/{instance}/disconnect' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="delete-whatsapp-instances-instance"></a>

#### `DELETE /whatsapp/instances/{instance}`

**Remover instância**

- **URL:** `https://integreai.com.br/api/whatsapp/instances/{instance}`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |
| `` |  | não |  |
| `` |  | não |  |
| `` |  | não |  |

**Respostas**

- `200`
- `422`

```bash
curl -X DELETE 'https://integreai.com.br/api/whatsapp/instances/{instance}' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="put-whatsapp-instances-instance-webhook"></a>

#### `PUT /whatsapp/instances/{instance}/webhook`

**Sincronizar webhook (automático)**

Reaplica a configuração de webhook gerada pelo sistema. Para WAHA, opcionalmente informe `events`. Para Meta, inscreve a WABA e retorna `gateway_webhook_url` para configurar no painel Meta.

- **URL:** `https://integreai.com.br/api/whatsapp/instances/{instance}/webhook`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `provider` | string | sim | Enum: `ycloud`, `evogo`, `meta` |
| `events` | array<string> | não | WAHA: eventos do webhook (opcional) |
| `phone_number_id` | string | não |  |
| `access_token` | string | não |  |
| `waba_id` | string | não |  |

**Respostas**

- `200`
- `422`

```bash
curl -X PUT 'https://integreai.com.br/api/whatsapp/instances/{instance}/webhook' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

### Mensagens

<a id="post-whatsapp-messages-text"></a>

#### `POST /whatsapp/messages/text`

**Enviar mensagem de texto**

- **URL:** `https://integreai.com.br/api/whatsapp/messages/text`
- **Autenticação:** Bearer token (Sanctum)

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `provider` | string | sim | Enum: `ycloud`, `evogo`, `meta` |
| `instance` | string | sim |  |
| `number` | string | sim |  |
| `text` | string | sim |  |
| `phone_number_id` | string | não |  |
| `access_token` | string | não |  |

**Respostas**

- `200`
- `422`

```bash
curl -X POST 'https://integreai.com.br/api/whatsapp/messages/text' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

<a id="post-whatsapp-messages-file"></a>

#### `POST /whatsapp/messages/file`

**Enviar arquivo (documento)**

Envia um documento/arquivo como anexo. Para imagens exibidas inline no chat, use POST /whatsapp/messages/image.

- **URL:** `https://integreai.com.br/api/whatsapp/messages/file`
- **Autenticação:** Bearer token (Sanctum)

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `provider` | string | sim | Enum: `ycloud`, `evogo`, `meta` |
| `instance` | string | sim |  |
| `number` | string | sim |  |
| `type` | string | não | Enum: `file` Default: `file` |
| `caption` | string | não |  |
| `file` | object | sim |  |
| `phone_number_id` | string | não |  |
| `access_token` | string | não |  |

**Respostas**

- `200`
- `422`

```bash
curl -X POST 'https://integreai.com.br/api/whatsapp/messages/file' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

<a id="post-whatsapp-messages-image"></a>

#### `POST /whatsapp/messages/image`

**Enviar imagem**

Envia imagem inline (YCloud e EVOGO).

- **URL:** `https://integreai.com.br/api/whatsapp/messages/image`
- **Autenticação:** Bearer token (Sanctum)

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `provider` | string | sim | Enum: `ycloud`, `evogo`, `meta` |
| `instance` | string | sim |  |
| `number` | string | sim |  |
| `caption` | string | não |  |
| `file` | object | sim |  |
| `phone_number_id` | string | não |  |
| `access_token` | string | não |  |

Exemplo (WAHA via URL):

```json
{
    "provider": "ycloud",
    "instance": "minha-loja",
    "number": "5511999999999",
    "caption": "Confira nossa promoção!",
    "file": {
        "mimetype": "image/jpeg",
        "filename": "promo.jpg",
        "url": "https://picsum.photos/1024"
    }
}
```

**Respostas**

- `200`
- `422`

```bash
curl -X POST 'https://integreai.com.br/api/whatsapp/messages/image' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud","instance":"minha-loja","number":"5511999999999","caption":"Confira nossa promoção!","file":{"mimetype":"image/jpeg","filename":"promo.jpg","url":"https://picsum.photos/1024"}}'
```

<a id="post-whatsapp-messages-media"></a>

#### `POST /whatsapp/messages/media`

**Enviar mídia tipada (image|audio|video|document)**

Usado pelo Inbox e pelo gateway. Compatível com YCloud e EVOGO.

- **URL:** `https://integreai.com.br/api/whatsapp/messages/media`
- **Autenticação:** Bearer token (Sanctum)

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `provider` | string | sim | Enum: `ycloud`, `evogo`, `meta` |
| `instance` | string | sim |  |
| `number` | string | sim |  |
| `media_type` | string | sim | Enum: `image`, `audio`, `video`, `document` |
| `file` | object | sim |  |
| `caption` | string | não |  |
| `quoted_message_id` | string | não |  |

**Respostas**

- `200`
- `422`

```bash
curl -X POST 'https://integreai.com.br/api/whatsapp/messages/media' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

<a id="post-whatsapp-messages-reply"></a>

#### `POST /whatsapp/messages/reply`

**Responder citando mensagem (quote)**

Inbox e API externa. YCloud e EVOGO.

- **URL:** `https://integreai.com.br/api/whatsapp/messages/reply`
- **Autenticação:** Bearer token (Sanctum)

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `provider` | string | sim | Enum: `ycloud`, `evogo`, `meta` |
| `instance` | string | sim |  |
| `number` | string | sim |  |
| `text` | string | sim |  |
| `quoted_message_id` | string | sim |  |

**Respostas**

- `200`
- `422`

```bash
curl -X POST 'https://integreai.com.br/api/whatsapp/messages/reply' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

<a id="post-whatsapp-messages-forward"></a>

#### `POST /whatsapp/messages/forward`

**Encaminhar conteúdo de mensagem**

Inbox e API externa. YCloud e EVOGO.

- **URL:** `https://integreai.com.br/api/whatsapp/messages/forward`
- **Autenticação:** Bearer token (Sanctum)

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `provider` | string | sim | Enum: `ycloud`, `evogo`, `meta` |
| `instance` | string | sim |  |
| `number` | string | sim |  |
| `content` | object | sim |  |

**Respostas**

- `200`
- `422`

```bash
curl -X POST 'https://integreai.com.br/api/whatsapp/messages/forward' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

<a id="delete-whatsapp-messages"></a>

#### `DELETE /whatsapp/messages`

**Apagar mensagem no provider (best-effort)**

EVOGO tenta remoção remota. YCloud pode retornar 501 — o Inbox ainda remove localmente via API v1.

- **URL:** `https://integreai.com.br/api/whatsapp/messages`
- **Autenticação:** Bearer token (Sanctum)

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `provider` | string | sim | Enum: `ycloud`, `evogo`, `meta` |
| `instance` | string | sim |  |
| `number` | string | sim |  |
| `message_id` | string | sim |  |
| `from_me` | boolean | não | Default: `true` |

**Respostas**

- `200`
- `501`

```bash
curl -X DELETE 'https://integreai.com.br/api/whatsapp/messages' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

### Webhooks

<a id="post-whatsapp-webhook-evogo"></a>

#### `POST /whatsapp/webhook/evogo`

**Webhook EVOGO**

Endpoint recebido pelo provedor EVOGO. Eventos são normalizados e encaminhados ao webhook do cliente quando configurado.

- **URL:** `https://integreai.com.br/api/whatsapp/webhook/evogo`
- **Autenticação:** Bearer token (Sanctum)

**Respostas**

- `200`

```bash
curl -X POST 'https://integreai.com.br/api/whatsapp/webhook/evogo' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

### Contatos

<a id="get-whatsapp-contacts-profile"></a>

#### `GET /whatsapp/contacts/profile`

**Perfil do contato (nome/about)**

- **URL:** `https://integreai.com.br/api/whatsapp/contacts/profile`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `provider` | query | sim | (`string`: ycloud|evogo|meta) |
| `instance` | query | sim | (string) |
| `number` | query | sim | (string) |

**Respostas**

- `200`

```bash
curl -X GET 'https://integreai.com.br/api/whatsapp/contacts/profile?provider=ycloud&instance=VALUE&number=VALUE' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="get-whatsapp-contacts-picture"></a>

#### `GET /whatsapp/contacts/picture`

**Foto de perfil do contato**

- **URL:** `https://integreai.com.br/api/whatsapp/contacts/picture`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `provider` | query | sim | (`string`: ycloud|evogo|meta) |
| `instance` | query | sim | (string) |
| `number` | query | sim | (string) |

**Respostas**

- `200`

```bash
curl -X GET 'https://integreai.com.br/api/whatsapp/contacts/picture?provider=ycloud&instance=VALUE&number=VALUE' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

### Inbox v1

<a id="get-v1-conversations"></a>

#### `GET /v1/conversations`

**Listar conversas**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `status` | query | não | (`string`: open|pending|resolved|snoozed|closed) |
| `assignee_id` | query | não | (integer) |
| `unread` | query | não | (boolean) |
| `tag_id` | query | não | (integer) |
| `q` | query | não | Busca nome/número (string) |
| `limit` | query | não | (integer) |

**Respostas**

- `200` — Lista

```bash
curl -X GET 'https://integreai.com.br/api/v1/conversations?status=open&assignee_id=VALUE&unread=VALUE&tag_id=VALUE&q=VALUE&limit=VALUE' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="get-v1-conversations-agents"></a>

#### `GET /v1/conversations/agents`

**Agentes da empresa (assignees)**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/agents`
- **Autenticação:** Bearer token (Sanctum)

**Respostas**

- `200` — OK

```bash
curl -X GET 'https://integreai.com.br/api/v1/conversations/agents' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="get-v1-conversations-conversation"></a>

#### `GET /v1/conversations/{conversation}`

**Detalhe da conversa**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/{conversation}`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |

**Respostas**

- `200` — OK

```bash
curl -X GET 'https://integreai.com.br/api/v1/conversations/{conversation}' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="patch-v1-conversations-conversation"></a>

#### `PATCH /v1/conversations/{conversation}`

**Atualizar status / prioridade / assignee**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/{conversation}`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `status` | string | não | Enum: `open`, `pending`, `resolved`, `snoozed`, `closed` |
| `priority` | string | não | Enum: `low`, `normal`, `high`, `urgent` |
| `assignee_id` | integer | não |  |

**Respostas**

- `200` — OK

```bash
curl -X PATCH 'https://integreai.com.br/api/v1/conversations/{conversation}' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

<a id="delete-v1-conversations-conversation"></a>

#### `DELETE /v1/conversations/{conversation}`

**Apagar conversa inteira do Inbox**

Remove conversa, mensagens, notas e tags. Não depende do provider remoto.

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/{conversation}`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |

**Respostas**

- `200` — OK

```bash
curl -X DELETE 'https://integreai.com.br/api/v1/conversations/{conversation}' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="post-v1-conversations-conversation-read"></a>

#### `POST /v1/conversations/{conversation}/read`

**Marcar como lida**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/{conversation}/read`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |

**Respostas**

- `200` — OK

```bash
curl -X POST 'https://integreai.com.br/api/v1/conversations/{conversation}/read' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="post-v1-conversations-conversation-unread"></a>

#### `POST /v1/conversations/{conversation}/unread`

**Marcar como não lida**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/{conversation}/unread`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |

**Respostas**

- `200` — OK

```bash
curl -X POST 'https://integreai.com.br/api/v1/conversations/{conversation}/unread' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="get-v1-conversations-conversation-profile"></a>

#### `GET /v1/conversations/{conversation}/profile`

**Perfil do contato (sync sob demanda)**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/{conversation}/profile`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |

**Respostas**

- `200` — OK

```bash
curl -X GET 'https://integreai.com.br/api/v1/conversations/{conversation}/profile' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="get-v1-conversations-conversation-messages"></a>

#### `GET /v1/conversations/{conversation}/messages`

**Listar mensagens**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/{conversation}/messages`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |
| `after_sequence` | query | não | (integer) |
| `limit` | query | não | (integer) |

**Respostas**

- `200` — OK

```bash
curl -X GET 'https://integreai.com.br/api/v1/conversations/{conversation}/messages' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="post-v1-conversations-conversation-messages"></a>

#### `POST /v1/conversations/{conversation}/messages`

**Enviar texto (alias de reply)**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/{conversation}/messages`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `text` | string | sim |  |
| `quoted_message_id` | integer | não |  |

**Respostas**

- `201` — Criada

```bash
curl -X POST 'https://integreai.com.br/api/v1/conversations/{conversation}/messages' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

<a id="post-v1-conversations-conversation-messages-reply"></a>

#### `POST /v1/conversations/{conversation}/messages/reply`

**Responder texto via Conversation Engine**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/{conversation}/messages/reply`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `text` | string | sim |  |
| `quoted_message_id` | integer | não |  |

**Respostas**

- `201` — Criada

```bash
curl -X POST 'https://integreai.com.br/api/v1/conversations/{conversation}/messages/reply' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

<a id="post-v1-conversations-conversation-messages-media"></a>

#### `POST /v1/conversations/{conversation}/messages/media`

**Enviar mídia (arquivo ou URL)**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/{conversation}/messages/media`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |

**Respostas**

- `201` — Criada

```bash
curl -X POST 'https://integreai.com.br/api/v1/conversations/{conversation}/messages/media' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="post-v1-conversations-conversation-messages-forward"></a>

#### `POST /v1/conversations/{conversation}/messages/forward`

**Encaminhar mensagem para outra conversa**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/{conversation}/messages/forward`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `message_id` | integer | sim |  |
| `target_conversation_id` | integer | sim |  |

**Respostas**

- `201` — Encaminhada

```bash
curl -X POST 'https://integreai.com.br/api/v1/conversations/{conversation}/messages/forward' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

<a id="get-v1-conversations-tags"></a>

#### `GET /v1/conversations/tags`

**Listar tags da empresa**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/tags`
- **Autenticação:** Bearer token (Sanctum)

**Respostas**

- `200` — OK

```bash
curl -X GET 'https://integreai.com.br/api/v1/conversations/tags' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="post-v1-conversations-tags"></a>

#### `POST /v1/conversations/tags`

**Criar tag**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/tags`
- **Autenticação:** Bearer token (Sanctum)

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `name` | string | sim |  |
| `color` | string | não |  |

**Respostas**

- `201` — Criada

```bash
curl -X POST 'https://integreai.com.br/api/v1/conversations/tags' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

<a id="put-v1-conversations-conversation-tags"></a>

#### `PUT /v1/conversations/{conversation}/tags`

**Sincronizar tags da conversa**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/{conversation}/tags`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `tag_ids` | array<integer> | não |  |

**Respostas**

- `200` — OK

```bash
curl -X PUT 'https://integreai.com.br/api/v1/conversations/{conversation}/tags' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

<a id="get-v1-conversations-conversation-notes"></a>

#### `GET /v1/conversations/{conversation}/notes`

**Listar notas**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/{conversation}/notes`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |

**Respostas**

- `200` — OK

```bash
curl -X GET 'https://integreai.com.br/api/v1/conversations/{conversation}/notes' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="post-v1-conversations-conversation-notes"></a>

#### `POST /v1/conversations/{conversation}/notes`

**Criar nota**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/{conversation}/notes`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `body` | string | sim |  |

**Respostas**

- `201` — Criada

```bash
curl -X POST 'https://integreai.com.br/api/v1/conversations/{conversation}/notes' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

<a id="get-v1-internal-channels"></a>

#### `GET /v1/internal/channels`

**Listar canais do usuário**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/internal/channels`
- **Autenticação:** Bearer token (Sanctum)

**Respostas**

- `200` — OK

```bash
curl -X GET 'https://integreai.com.br/api/v1/internal/channels' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="post-v1-internal-channels"></a>

#### `POST /v1/internal/channels`

**Criar canal (group/team/direct)**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/internal/channels`
- **Autenticação:** Bearer token (Sanctum)

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `name` | string | não |  |
| `type` | string | não | Enum: `direct`, `group`, `team` Default: `group` |
| `user_ids` | array<integer> | não |  |

**Respostas**

- `201` — Criado

```bash
curl -X POST 'https://integreai.com.br/api/v1/internal/channels' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

<a id="post-v1-internal-channels-direct"></a>

#### `POST /v1/internal/channels/direct`

**Abrir ou reutilizar DM 1:1**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/internal/channels/direct`
- **Autenticação:** Bearer token (Sanctum)

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `user_id` | integer | sim | Outro membro da empresa |

**Respostas**

- `200` — DM existente
- `201` — DM criada
- `422` — user_id inválido

```bash
curl -X POST 'https://integreai.com.br/api/v1/internal/channels/direct' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

<a id="get-v1-internal-channels-channel-messages"></a>

#### `GET /v1/internal/channels/{channel}/messages`

**Listar mensagens do canal**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/internal/channels/{channel}/messages`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |
| `after_sequence` | query | não | (integer) |

**Respostas**

- `200` — OK

```bash
curl -X GET 'https://integreai.com.br/api/v1/internal/channels/{channel}/messages' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

<a id="post-v1-internal-channels-channel-messages"></a>

#### `POST /v1/internal/channels/{channel}/messages`

**Enviar mensagem no canal**

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/internal/channels/{channel}/messages`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |

**Body (JSON)**

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `body` | string | sim |  |

**Respostas**

- `201` — Criada

```bash
curl -X POST 'https://integreai.com.br/api/v1/internal/channels/{channel}/messages' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"provider":"ycloud"}'
```

<a id="delete-v1-conversations-conversation-messages-message"></a>

#### `DELETE /v1/conversations/{conversation}/messages/{message}`

**Apagar mensagem (soft-delete)**

Marca como apagada no Inbox. Em EVOGO tenta remoção remota (best-effort).

Via IntegreAI Conversation Engine — provider da instância (EVOGO).

- **URL:** `https://integreai.com.br/api/v1/conversations/{conversation}/messages/{message}`
- **Autenticação:** Bearer token (Sanctum)

**Parâmetros**

| Nome | Em | Obrigatório | Descrição |
|------|----|-------------|-----------|
| `` |  | não |  |
| `message` | path | sim | (integer) |

**Respostas**

- `200` — OK

```bash
curl -X DELETE 'https://integreai.com.br/api/v1/conversations/{conversation}/messages/{message}' \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json'
```

---

_IntegreAI — documentação exportada do OpenAPI._
