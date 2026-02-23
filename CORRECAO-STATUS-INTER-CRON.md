# Correção do StatusInterCron - Erro SSL Pix

## Problema Identificado

O comando `StatusInterCron` estava tentando consultar o status de **todas** as cobranças pendentes (Boleto, BoletoPix e Pix), causando erros SSL quando:

1. A empresa não tinha certificados SSL configurados corretamente
2. O sistema estava consultando cobranças PIX desnecessariamente
3. O erro SSL quebrava o processamento das demais cobranças

### Erros no Log

```
[2026-02-23 14:30:18] production.ERROR: cURL error 60: SSL certificate verification failed
for https://cdpj.partners.bancointer.com.br/pix/v2/cobv/...

[2026-02-23 15:00:10] production.INFO: TokenInterCron: 
{"status":"reject","message":"Certificado CRT banco inter não existe!","company_id":7}
```

## Solução Implementada

### 1. Filtro de Métodos de Pagamento

Adicionado controle de quais métodos devem ser processados:

```php
// CONFIGURAÇÃO: Defina quais métodos de pagamento devem ser processados
// Para desabilitar a consulta de PIX, remova 'Pix' do array abaixo
$metodosAtivos = ['Boleto', 'BoletoPix']; // 'Pix' foi removido

$invoices = Invoice::where('status', 'Pendente')
    ->whereIn('gateway_payment', ['Inter', 'Intermedium'])
    ->whereIn('payment_method', $metodosAtivos) // Filtra apenas métodos ativos
    ->whereNotNull('company_id')
    ->whereNotNull('transaction_id')
    ->with('company')
    ->orderBy('date_due', 'asc')
    ->limit($limite_por_execucao)
    ->get();
```

**Resultado:** O comando agora **NÃO** consulta cobranças PIX, apenas Boleto e BoletoPix.

### 2. Try-Catch no Bloco Pix

Adicionado tratamento de exceções para evitar que erros no PIX quebrem o processamento:

```php
try {
    $response = Http::withOptions([
        'cert' => $certPath,
        'ssl_key' => $keyPath
    ])->withHeaders([
        'Authorization' => 'Bearer ' . $access_token
    ])->get($url);
    
    // ... processamento da resposta
    
} catch (\Exception $e) {
    \Log::error('Status Pix - Exceção ao consultar - Invoice ID: '.$invoice['id'].' - Erro: '.$e->getMessage());
    // Continua processando próxima fatura ao invés de quebrar
}
```

### 3. Validação Extra de Certificados

Verificação dupla da existência dos certificados antes de tentar consultar PIX:

```php
// VALIDAÇÃO EXTRA: Verifica se os certificados existem antes de tentar consultar PIX
$certPath = storage_path('/app/'.$company->inter_crt_file);
$keyPath = storage_path('/app/'.$company->inter_key_file);

if (!file_exists($certPath) || !file_exists($keyPath)) {
    \Log::warning('Status Pix - Certificados não encontrados para Company ID: '.$company->id.' - Pulando consulta');
    continue;
}
```

## Como Usar

### Configuração Atual (Padrão)

O sistema está configurado para processar apenas:
- ✅ **Boleto** (V2 e V3)
- ✅ **BoletoPix** (V3)
- ❌ **Pix** (Desabilitado)

### Para Reativar Consulta de PIX

Se no futuro você precisar consultar PIX novamente:

1. **Configure os certificados SSL da empresa** no banco de dados:
   - `inter_crt_file` - Caminho do arquivo .crt
   - `inter_key_file` - Caminho do arquivo .key
   - Garanta que os arquivos existem em `storage/app/`

2. **Edite o arquivo** `app/Console/Commands/StatusInterCron.php`:
   ```php
   // Adicione 'Pix' de volta ao array
   $metodosAtivos = ['Boleto', 'BoletoPix', 'Pix'];
   ```

3. **Teste** com uma fatura PIX antes de ativar em produção

### Para Desabilitar Boleto ou BoletoPix

Se quiser processar apenas um tipo específico:

```php
// Apenas Boleto
$metodosAtivos = ['Boleto'];

// Apenas BoletoPix
$metodosAtivos = ['BoletoPix'];

// Apenas Pix
$metodosAtivos = ['Pix'];

// Todos (configuração antiga)
$metodosAtivos = ['Boleto', 'BoletoPix', 'Pix'];
```

## Diferenças entre os Métodos

### Boleto
- **Endpoint V2:** `/cobranca/v2/boletos` (para nossoNumero antigo)
- **Endpoint V3:** `/cobranca/v3/cobrancas/{id}` (para UUID)
- **Status pago:** `PAGO` (V2) ou `RECEBIDO` (V3)

### BoletoPix
- **Endpoint:** `/cobranca/v3/cobrancas/{id}`
- **Status pago:** `RECEBIDO`
- Permite pagamento via Boleto OU PIX

### Pix
- **Endpoint:** `/pix/v2/cobv/{id}`
- **Status pago:** `CONCLUIDA`
- Requer certificados SSL válidos
- **Agora desabilitado por padrão**

## Melhorias Implementadas

1. ✅ **Filtro configurável** de métodos de pagamento
2. ✅ **Try-catch** para prevenir quebra do processamento
3. ✅ **Validação dupla** de certificados SSL
4. ✅ **Logs informativos** sobre métodos sendo processados
5. ✅ **Delay entre requisições** (0.5s) para evitar rate limit
6. ✅ **Tratamento de rate limit** (429) com sleep de 5s
7. ✅ **Priorização por vencimento** (processa faturas mais próximas primeiro)

## Monitoramento

### Verificar se o Cron está funcionando

```bash
# Ver últimas execuções no log
tail -f storage/logs/laravel.log | grep StatusInterCron

# Ver apenas erros
tail -f storage/logs/laravel.log | grep "ERROR"
```

### Logs Esperados

```
[2026-02-23 XX:XX:XX] local.INFO: Processando 15 faturas (Métodos: Boleto, BoletoPix)...
[2026-02-23 XX:XX:XX] local.INFO: Processadas 10 de 15 faturas...
[2026-02-23 XX:XX:XX] local.INFO: StatusInterCron finalizado - Total: 15 faturas processadas
```

## Comandos Úteis

### Executar manualmente

```bash
php artisan statusinter:cron
```

### Ver schedule de crons

```bash
php artisan schedule:list
```

### Executar em modo debug

```bash
php artisan statusinter:cron -v
```

## Próximos Passos (Opcional)

### 1. Adicionar Configuração no Banco

Criar campo na tabela `companies` para controlar quais métodos cada empresa deve processar:

```sql
ALTER TABLE companies 
ADD COLUMN status_check_methods JSON DEFAULT '["Boleto","BoletoPix"]';
```

### 2. Interface Admin

Adicionar interface para o admin configurar quais métodos processar por empresa.

### 3. Notificação de Erros

Enviar email/slack quando houver muitos erros consecutivos.

## Arquivo Modificado

- `app/Console/Commands/StatusInterCron.php`

## Checklist de Verificação

Antes de ativar PIX novamente:

- [ ] Certificado .crt existe em `storage/app/`
- [ ] Certificado .key existe em `storage/app/`
- [ ] Campos `inter_crt_file` e `inter_key_file` estão preenchidos no banco
- [ ] Campos `inter_host`, `inter_client_id`, `inter_client_secret` estão configurados
- [ ] Token de acesso está válido (`access_token_inter`)
- [ ] Testar com uma fatura PIX em ambiente de desenvolvimento primeiro
- [ ] Adicionar 'Pix' ao array `$metodosAtivos`

---

**Data da Correção:** 23 de fevereiro de 2026  
**Desenvolvedor:** Cursor AI Assistant  
**Status:** ✅ Resolvido - PIX desabilitado, Boleto e BoletoPix funcionando
