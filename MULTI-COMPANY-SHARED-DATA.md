# Implementação de Compartilhamento de Dados entre Empresas

## Problema Identificado

O sistema estava filtrando os dados apenas pela empresa ativa no momento (`current_company_id`), impedindo que usuários com acesso a múltiplas empresas visualizassem dados de todas as empresas às quais têm acesso.

**Exemplo do problema:**
- Usuário ID 3 tem acesso às empresas ID 3 e ID 7
- Usuário está logado com `current_company_id = 7`
- Ao buscar `customer_services` ID 408 que pertence à empresa ID 3, o sistema não retornava o registro

## Solução Implementada

### 1. Novo Helper: `userCompanyIds()`

Criada função helper que retorna todos os IDs de empresas que o usuário autenticado possui acesso:

```php
// app/Helpers/company_helpers.php

function userCompanyIds()
{
    if (auth()->check()) {
        return auth()->user()->companies()->pluck('companies.id')->toArray();
    }
    
    return [];
}
```

### 2. Novo Scope: `forUserCompanies()`

Adicionado em todos os modelos que possuem relacionamento com empresas:

**Modelos atualizados:**
- `Customer`
- `CustomerService`
- `Invoice`
- `Service`
- `Payable`
- `PayableCategory`
- `Supplier`

**Implementação:**

```php
public function scopeForUserCompanies($query)
{
    $companyIds = userCompanyIds();
    
    if (empty($companyIds)) {
        return $query->whereRaw('1 = 0');
    }
    
    return $query->whereIn('company_id', $companyIds);
}
```

Para o modelo `Invoice` e `Payable`, a coluna precisa ser especificada com o prefixo da tabela:

```php
public function scopeForUserCompanies($query)
{
    $companyIds = userCompanyIds();
    
    if (empty($companyIds)) {
        return $query->whereRaw('1 = 0');
    }
    
    return $query->whereIn('invoices.company_id', $companyIds);
    // ou payables.company_id para Payable
}
```

### 3. Atualização dos Controllers

Substituído `forCompany(currentCompanyId())` por `forUserCompanies()` em todos os controllers:

**Controllers atualizados:**
- `CustomerController`
- `CustomerServiceController`
- `InvoiceController`
- `ServiceController`
- `PayableController`
- `PayableCategoryController`
- `SupplierController`
- `AdminController` (Dashboard)

**Antes:**
```php
$data = CustomerService::forCompany(currentCompanyId())->get();
```

**Depois:**
```php
$data = CustomerService::forUserCompanies()->get();
```

## Comportamento Atual

### Cenário 1: Usuário com acesso a uma única empresa
- Filtra dados apenas dessa empresa
- Comportamento idêntico ao anterior

### Cenário 2: Usuário com acesso a múltiplas empresas
- Filtra dados de TODAS as empresas às quais o usuário tem acesso
- Permite visualizar e gerenciar dados compartilhados entre empresas
- **Exemplo:** Usuário com acesso às empresas 3 e 7 pode ver:
  - Customers de ambas as empresas
  - Customer Services de ambas as empresas
  - Invoices de ambas as empresas
  - Payables de ambas as empresas
  - Etc.

## Impacto no Sistema

### ✅ Benefícios
1. **Compartilhamento de dados:** Empresas podem compartilhar customers, services e invoices
2. **Flexibilidade:** Usuários multi-empresa podem gerenciar dados de todas as suas empresas
3. **Consistência:** Elimina o problema de "registro não encontrado" quando o usuário troca de empresa

### ⚠️ Considerações de Segurança

O sistema agora permite que usuários vejam dados de todas as empresas às quais têm acesso. É importante:

1. **Controle de acesso:** Garantir que a tabela pivot `company_user` esteja corretamente configurada
2. **Validação:** Ao criar/atualizar registros, validar se o `company_id` fornecido está entre as empresas do usuário
3. **Auditoria:** Considerar adicionar logs de auditoria para rastrear quem acessa dados de qual empresa

### Validação de Company ID

Os controllers já possuem validação adequada na criação/edição de registros:

```php
$userCompanyIds = auth()->user()->companies->pluck('id')->toArray();
$validator = Validator::make($data, [
    'company_id' => ['required', 'exists:companies,id', function ($attr, $val, $fail) use ($userCompanyIds) {
        if (!in_array((int)$val, $userCompanyIds)) { 
            $fail('Empresa inválida.'); 
        }
    }],
    // ... outros campos
]);
```

## Próximos Passos (Opcional)

### 1. Adicionar filtro por empresa na UI
Permitir que o usuário filtre os dados por empresa específica:

```php
// Adicionar campo de filtro nos índices
if ($request->has('filter_company_id')) {
    $query->where('company_id', $request->filter_company_id);
}
```

### 2. Indicador visual da empresa
Adicionar coluna indicando a qual empresa cada registro pertence:

```php
// Na view index
<td>{{ $record->company->name }}</td>
```

### 3. Scope condicional
Criar um helper que decide automaticamente entre filtrar por uma empresa ou todas:

```php
function smartCompanyFilter($query, $singleCompanyMode = false)
{
    if ($singleCompanyMode) {
        return $query->forCompany(currentCompanyId());
    }
    
    return $query->forUserCompanies();
}
```

## Testes Sugeridos

1. **Teste básico:**
   - Criar usuário com acesso a duas empresas
   - Alternar entre empresas
   - Verificar se os dados de ambas são visíveis

2. **Teste de isolamento:**
   - Criar usuário com acesso apenas a uma empresa
   - Verificar se não vê dados de outras empresas

3. **Teste de criação:**
   - Tentar criar registro com `company_id` de empresa sem acesso
   - Deve ser bloqueado pela validação

4. **Teste SQL:**
```sql
-- Verificar empresas do usuário
SELECT * FROM company_user WHERE user_id = 3;

-- Verificar customer_service 408
SELECT * FROM customer_services WHERE id = 408;

-- Deve retornar o registro agora, independente da empresa ativa
```

## Arquivos Modificados

### Helpers
- `app/Helpers/company_helpers.php` - Adicionada função `userCompanyIds()`

### Models
- `app/Models/Customer.php`
- `app/Models/CustomerService.php`
- `app/Models/Invoice.php`
- `app/Models/Service.php`
- `app/Models/Payable.php`
- `app/Models/PayableCategory.php`
- `app/Models/Supplier.php`

### Controllers
- `app/Http/Controllers/admin/CustomerController.php`
- `app/Http/Controllers/admin/CustomerServiceController.php`
- `app/Http/Controllers/admin/InvoiceController.php`
- `app/Http/Controllers/admin/ServiceController.php`
- `app/Http/Controllers/admin/PayableController.php`
- `app/Http/Controllers/admin/PayableCategoryController.php`
- `app/Http/Controllers/admin/SupplierController.php`
- `app/Http/Controllers/admin/AdminController.php`

---

**Data de Implementação:** 23 de fevereiro de 2026  
**Desenvolvedor:** Cursor AI Assistant
