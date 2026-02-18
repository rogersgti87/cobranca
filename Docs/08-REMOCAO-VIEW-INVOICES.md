# Remoção da VIEW view_invoices

**Data:** 16 de Fevereiro de 2024

## Problema Identificado

A VIEW `view_invoices` estava causando problemas de manutenção:

1. **Complexidade**: A VIEW juntava dados de múltiplas tabelas (invoices, customers, users, companies)
2. **Erros de ambiguidade**: Causava erros SQL quando usada em queries com JOINs
3. **Falta de coluna company_id**: Inicialmente não tinha a coluna, causando erros de filtro
4. **Difícil manutenção**: Cada mudança na estrutura exigia recriar a VIEW

## Solução Implementada

Substituímos a VIEW por **relacionamentos Eloquent normais**, usando eager loading para otimização.

### Arquivos Modificados

#### 1. `app/Models/Invoice.php`

**ANTES:**
```php
use App\Models\ViewInvoice;

$invoice = ViewInvoice::where('id', $invoice_id)->first();
$chave_pix = $invoice['chave_pix'];
```

**DEPOIS:**
```php
$invoice = Invoice::with(['customerService.customer', 'company'])
    ->find($invoice_id);
$chave_pix = $invoice->company->chave_pix;
```

#### 2. `app/Http/Controllers/admin/InvoiceController.php`

**ANTES:**
```php
$data = ViewInvoice::forCompany(currentCompanyId())
    ->where('status','Erro')
    ->get();
```

**DEPOIS:**
```php
$data = Invoice::with(['customerService.customer', 'company'])
    ->forCompany(currentCompanyId())
    ->where('status','Erro')
    ->get();
```

#### 3. `app/Models/InvoiceNotification.php`

**ANTES:**
```php
$invoice = ViewInvoice::where('id', $invoice_id)->first();
$email = $invoice['email'];
$whatsapp = $invoice['whatsapp'];
```

**DEPOIS:**
```php
$invoice = Invoice::with(['customerService.customer', 'company'])
    ->find($invoice_id);
$email = $invoice->customerService->customer->email;
$whatsapp = $invoice->customerService->customer->whatsapp;
```

#### 4. `app/Console/Commands/GenerateInvoiceCron.php`

**ANTES:**
```php
$invoices = ViewInvoice::where('status', 'Gerando')
    ->whereNotNull('company_id')
    ->get();
```

**DEPOIS:**
```php
$invoices = Invoice::with(['customerService.customer', 'company'])
    ->where('status', 'Gerando')
    ->whereNotNull('company_id')
    ->get();
```

#### 5. `app/Http/Controllers/API/InvoiceController.php`

**ANTES:**
```php
$invoice = ViewInvoice::where('id', $request->input('invoice_id'))->first();
```

**DEPOIS:**
```php
$invoice = Invoice::with(['customerService.customer', 'company'])
    ->find($request->input('invoice_id'));
```

### Mapeamento de Campos

| Campo VIEW (array) | Novo Acesso (objeto) |
|-------------------|---------------------|
| `$invoice['chave_pix']` | `$invoice->company->chave_pix` |
| `$invoice['access_token_mp']` | `$invoice->company->access_token_mp` |
| `$invoice['inter_*']` | `$invoice->company->inter_*` |
| `$invoice['key_paghiper']` | `$invoice->company->key_paghiper` |
| `$invoice['token_paghiper']` | `$invoice->company->token_paghiper` |
| `$invoice['environment_asaas']` | `$invoice->company->environment_asaas` |
| `$invoice['at_asaas_*']` | `$invoice->company->at_asaas_*` |
| `$invoice['asaas_url_*']` | `$invoice->company->asaas_url_*` |
| `$invoice['api_session_whatsapp']` | `$invoice->company->api_session_whatsapp` |
| `$invoice['api_token_whatsapp']` | `$invoice->company->api_token_whatsapp` |
| `$invoice['name']` | `$invoice->customerService->customer->name` |
| `$invoice['email']` | `$invoice->customerService->customer->email` |
| `$invoice['phone']` | `$invoice->customerService->customer->phone` |
| `$invoice['whatsapp']` | `$invoice->customerService->customer->whatsapp` |
| `$invoice['notification_whatsapp']` | `$invoice->customerService->customer->notification_whatsapp` |
| `$invoice['notification_email']` | `$invoice->customerService->customer->notification_email` |

### Arquivos Removidos

1. **Model:** `app/Models/ViewInvoice.php` (deletado)
2. **Migration:** `database/migrations/2024_02_16_150001_recreate_view_invoices_with_company.php` (deletado)
3. **VIEW do banco:** `view_invoices` (dropada com `DROP VIEW IF EXISTS view_invoices`)

## Vantagens da Nova Abordagem

### 1. **Mais Manutenível**
- Não precisa recriar VIEWs quando a estrutura muda
- Usa relacionamentos nativos do Eloquent
- Código mais limpo e orientado a objetos

### 2. **Melhor Performance**
- Eager loading evita N+1 queries
- Cache de relacionamentos do Eloquent
- Queries mais otimizadas

### 3. **Sem Erros de Ambiguidade**
- Não há mais conflitos de nomes de colunas em JOINs
- Queries mais claras e específicas

### 4. **Melhor Suporte a IDE**
- Autocompletar funciona com objetos
- Type hints e PHPDoc funcionam corretamente
- Refatoração mais fácil

### 5. **Segurança**
- Validações do Eloquent são aplicadas
- Mutators e accessors funcionam
- Scopes globais são respeitados

## Padrão de Uso

### Para Consultas Simples

```php
$invoice = Invoice::with(['customerService.customer', 'company'])
    ->find($id);
```

### Para Consultas com Filtros

```php
$invoices = Invoice::with(['customerService.customer', 'company'])
    ->forCompany(currentCompanyId())
    ->where('status', 'Pendente')
    ->get();
```

### Para Consultas Complexas

```php
$invoices = Invoice::with([
    'customerService.customer',
    'customerService.service',
    'company',
    'user'
])
    ->forCompany(currentCompanyId())
    ->whereBetween('date_invoice', [$start, $end])
    ->orderBy('date_due')
    ->get();
```

## Relacionamentos Disponíveis em Invoice

```php
// Em app/Models/Invoice.php

// Relacionamento com CustomerService
public function customerService()
{
    return $this->belongsTo(CustomerService::class);
}

// Relacionamento com Company
public function company()
{
    return $this->belongsTo(Company::class);
}

// Relacionamento com User
public function user()
{
    return $this->belongsTo(User::class);
}

// Scope para filtrar por empresa
public function scopeForCompany($query, $companyId)
{
    return $query->where('invoices.company_id', $companyId);
}
```

## Relacionamentos em CustomerService

```php
// Em app/Models/CustomerService.php

// Relacionamento com Customer
public function customer()
{
    return $this->belongsTo(Customer::class);
}

// Relacionamento com Service
public function service()
{
    return $this->belongsTo(Service::class);
}
```

## Comandos Executados

```bash
# 1. Limpar logs
rm -f storage/logs/laravel.log
touch storage/logs/laravel.log

# 2. Dropar VIEW do banco
mysql -h127.0.0.1 -P3306 -uroot -proot cobrancasegura \
  -e "DROP VIEW IF EXISTS view_invoices;"

# 3. Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Checklist de Testes

Após a implementação, testar:

- [ ] Listagem de faturas no admin
- [ ] Detalhes de fatura
- [ ] Geração de PIX (PagHiper, Mercado Pago, Inter, Asaas)
- [ ] Geração de Boleto (PagHiper, Inter, Asaas)
- [ ] Envio de notificações (Email, WhatsApp)
- [ ] Dashboard com gráficos e estatísticas
- [ ] Cron jobs (CreateInvoiceCron, GenerateInvoiceCron, RememberInvoiceCron)
- [ ] API de notificações
- [ ] Filtros por empresa (company_id)
- [ ] Relatórios de faturas

## Conclusão

A remoção da VIEW `view_invoices` torna o sistema mais robusto, manutenível e alinhado com as melhores práticas do Laravel. O uso de relacionamentos Eloquent é a abordagem recomendada pela documentação oficial e pela comunidade.
