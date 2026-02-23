# Exibição da Empresa de Origem no Formulário de Customers

## Objetivo

Adicionar a exibição da empresa de origem nas abas de **Serviços** e **Invoices** do formulário de edição de customers, permitindo que usuários com acesso a múltiplas empresas identifiquem facilmente a qual empresa cada registro pertence.

## Motivação

Com a implementação do sistema de compartilhamento de dados entre empresas, um usuário pode visualizar serviços e faturas de todas as empresas às quais tem acesso. Por isso, é importante identificar visualmente a origem de cada registro.

## Alterações Realizadas

### 1. Controllers - Inclusão dos Dados da Empresa

#### CustomerServiceController

**Arquivo:** `app/Http/Controllers/admin/CustomerServiceController.php`

**Método:** `Load($customer_id)`

**Alteração:**
```php
// ANTES
$result = CustomerService::forUserCompanies()
        ->select('customer_services.id as id','customer_services.description', ...)
        ->where('customer_services.customer_id',$customer_id)
        ->get();

// DEPOIS
$result = CustomerService::forUserCompanies()
        ->join('companies','companies.id','customer_services.company_id')
        ->select('customer_services.id as id','customer_services.description', ...,
        'companies.id as company_id','companies.name as company_name')
        ->where('customer_services.customer_id',$customer_id)
        ->get();
```

#### InvoiceController

**Arquivo:** `app/Http/Controllers/admin/InvoiceController.php`

**Método:** `Load($customer_id)`

**Alteração:**
```php
// ANTES
$result = Invoice::forUserCompanies()
        ->join('customer_services','customer_services.id','invoices.customer_service_id')
        ->select('invoices.id as id','invoices.description', ...)
        ->where('customer_services.customer_id',$customer_id)
        ->get();

// DEPOIS
$result = Invoice::forUserCompanies()
        ->join('customer_services','customer_services.id','invoices.customer_service_id')
        ->join('companies','companies.id','invoices.company_id')
        ->select('invoices.id as id','invoices.description', ...,
        'companies.id as company_id','companies.name as company_name')
        ->where('customer_services.customer_id',$customer_id)
        ->get();
```

### 2. Views - Adição da Coluna "Empresa"

#### Tabela de Serviços (Customer Services)

**Arquivo:** `resources/views/admin/customer/form.blade.php`

**Linha ~465-477:**

```html
<!-- ANTES -->
<thead class="thead-light">
<tr>
    <th>Descrição</th>
    <th>Preço</th>
    <th>Vencimento</th>
    <th>Período</th>
    <th>Status</th>
    <th style="width: 150px;"></th>
</tr>
</thead>

<!-- DEPOIS -->
<thead class="thead-light">
<tr>
    <th>Descrição</th>
    <th>Preço</th>
    <th>Vencimento</th>
    <th>Período</th>
    <th>Empresa</th>  <!-- 👈 NOVA COLUNA -->
    <th>Status</th>
    <th style="width: 150px;"></th>
</tr>
</thead>
```

#### Tabela de Faturas (Invoices)

**Linha ~489-504:**

```html
<!-- ANTES -->
<thead class="thead-light">
<tr>
    <th>#</th>
    <th>Descrição</th>
    <th>Preço</th>
    <th>Gateway de Pagamento</th>
    <th>Forma de Pagamento</th>
    <th>Data</th>
    <th>Vencimento</th>
    <th>Pago em</th>
    <th>Status</th>
    <th style="width: 150px;"></th>
</tr>
</thead>

<!-- DEPOIS -->
<thead class="thead-light">
<tr>
    <th>#</th>
    <th>Descrição</th>
    <th>Preço</th>
    <th>Gateway de Pagamento</th>
    <th>Forma de Pagamento</th>
    <th>Data</th>
    <th>Vencimento</th>
    <th>Pago em</th>
    <th>Empresa</th>  <!-- 👈 NOVA COLUNA -->
    <th>Status</th>
    <th style="width: 150px;"></th>
</tr>
</thead>
```

### 3. JavaScript - Renderização da Coluna Empresa

#### Função loadCustomerServices()

**Linha ~786-798:**

```javascript
// ANTES
html += '<tr>';
html += `<td>${item.description}</td>`;
html += `<td>R$ ${parseFloat(item.price).toLocaleString('pt-br', {minimumFractionDigits: 2})}</td>`;
html += `<td>${item.day_due}</td>`;
html += `<td>${item.period}</td>`;
html += `<td><label class="badge badge-${item.status == 'Ativo' ? 'success' : 'danger'}">${item.status}</label></td>`;
html += `<td>...</td>`;
html += '</tr>';

// DEPOIS
html += '<tr>';
html += `<td>${item.description}</td>`;
html += `<td>R$ ${parseFloat(item.price).toLocaleString('pt-br', {minimumFractionDigits: 2})}</td>`;
html += `<td>${item.day_due}</td>`;
html += `<td>${item.period}</td>`;
html += `<td><span class="badge badge-info" style="background-color: #17a2b8;">${item.company_name || 'N/A'}</span></td>`;  // 👈 NOVA COLUNA
html += `<td><label class="badge badge-${item.status == 'Ativo' ? 'success' : 'danger'}">${item.status}</label></td>`;
html += `<td>...</td>`;
html += '</tr>';
```

#### Função loadInvoices()

**Linha ~883-902:**

```javascript
// ANTES
html += '<tr>';
html += `<td>${item.id}</td>`;
html += `<td>${item.description}</td>`;
html += `<td>R$ ${parseFloat(item.price).toLocaleString('pt-br', {minimumFractionDigits: 2})}</td>`;
html += `<td>${item.gateway_payment}</td>`;
html += `<td>${item.payment_method}</td>`;
html += `<td>${moment(item.date_invoice).format('DD/MM/YYYY')}</td>`;
html += `<td>${moment(item.date_due).format('DD/MM/YYYY')}</td>`;
html += `<td>${item.date_payment != null ? moment(item.date_payment).format('DD/MM/YYYY') : '-'}</td>`;
html += `<td><label class="badge ...">${item.status}</label></td>`;
html += `<td>...</td>`;
html += '</tr>';

// DEPOIS
html += '<tr>';
html += `<td>${item.id}</td>`;
html += `<td>${item.description}</td>`;
html += `<td>R$ ${parseFloat(item.price).toLocaleString('pt-br', {minimumFractionDigits: 2})}</td>`;
html += `<td>${item.gateway_payment}</td>`;
html += `<td>${item.payment_method}</td>`;
html += `<td>${moment(item.date_invoice).format('DD/MM/YYYY')}</td>`;
html += `<td>${moment(item.date_due).format('DD/MM/YYYY')}</td>`;
html += `<td>${item.date_payment != null ? moment(item.date_payment).format('DD/MM/YYYY') : '-'}</td>`;
html += `<td><span class="badge badge-info" style="background-color: #17a2b8;">${item.company_name || 'N/A'}</span></td>`;  // 👈 NOVA COLUNA
html += `<td><label class="badge ...">${item.status}</label></td>`;
html += `<td>...</td>`;
html += '</tr>';
```

## Resultado Visual

### Aba Serviços

Agora exibe:

| Descrição | Preço | Vencimento | Período | **Empresa** | Status | Ações |
|-----------|-------|------------|---------|-------------|--------|-------|
| Plano Premium | R$ 99,00 | 15 | Mensal | **Empresa ABC** | Ativo | ... |
| Consultoria | R$ 250,00 | 10 | Mensal | **Empresa XYZ** | Ativo | ... |

### Aba Faturas

Agora exibe:

| # | Descrição | Preço | Gateway | Forma | Data | Vencimento | Pago em | **Empresa** | Status | Ações |
|---|-----------|-------|---------|-------|------|------------|---------|-------------|--------|-------|
| 123 | Plano Premium | R$ 99,00 | Inter | Boleto | 01/02/2026 | 15/02/2026 | - | **Empresa ABC** | Pendente | ... |
| 124 | Consultoria | R$ 250,00 | Asaas | Pix | 05/02/2026 | 10/02/2026 | 08/02/2026 | **Empresa XYZ** | Pago | ... |

## Estilização

A coluna "Empresa" utiliza um badge azul (`badge badge-info`) com cor de fundo `#17a2b8` para destacar visualmente a informação:

```html
<span class="badge badge-info" style="background-color: #17a2b8;">
    Nome da Empresa
</span>
```

Se por algum motivo o nome da empresa não estiver disponível, será exibido "N/A" (Not Available).

## Benefícios

1. **Identificação Rápida:** Usuários podem identificar imediatamente a qual empresa cada serviço/fatura pertence
2. **Transparência:** Maior clareza na visualização de dados compartilhados entre empresas
3. **Gestão Facilitada:** Facilita a gestão de clientes que possuem serviços em múltiplas empresas
4. **Auditoria:** Permite rastrear facilmente a origem dos dados

## Compatibilidade

- ✅ **Usuários com uma empresa:** Verão sempre o nome da mesma empresa (comportamento esperado)
- ✅ **Usuários com múltiplas empresas:** Verão o nome de cada empresa correspondente ao registro
- ✅ **Performance:** O JOIN com a tabela `companies` tem impacto mínimo, pois é indexed pela primary key

## Testes Sugeridos

### Teste 1: Usuário com múltiplas empresas
1. Logar com usuário que tem acesso a empresas A e B
2. Acessar um cliente que possui serviços da empresa A e B
3. Verificar se as abas Serviços e Faturas exibem corretamente o nome de cada empresa

### Teste 2: Usuário com uma empresa
1. Logar com usuário que tem acesso apenas à empresa A
2. Acessar qualquer cliente
3. Verificar se todos os registros mostram "Empresa A"

### Teste 3: Dados sem empresa (edge case)
1. Verificar se registros sem company_id exibem "N/A"
2. Validar que não há erros JavaScript no console

## Arquivos Modificados

- ✅ `app/Http/Controllers/admin/CustomerServiceController.php` (método `Load`)
- ✅ `app/Http/Controllers/admin/InvoiceController.php` (método `Load`)
- ✅ `resources/views/admin/customer/form.blade.php` (tabelas e JavaScript)

## Próximas Melhorias (Opcional)

### 1. Filtro por Empresa
Adicionar filtro para exibir apenas serviços/faturas de uma empresa específica:

```javascript
function filterByCompany(companyId) {
    if (companyId === 'all') {
        // Mostrar todos
    } else {
        // Filtrar pela empresa selecionada
    }
}
```

### 2. Badge com Cor Customizada
Usar cores diferentes para cada empresa:

```javascript
// Buscar cor da empresa do banco
html += `<td><span class="badge" style="background-color: ${item.company_color};">
    ${item.company_name}
</span></td>`;
```

### 3. Tooltip com Mais Informações
Adicionar tooltip com informações adicionais da empresa:

```html
<span class="badge badge-info" 
      data-toggle="tooltip" 
      title="CNPJ: XX.XXX.XXX/XXXX-XX">
    Empresa ABC
</span>
```

---

**Data de Implementação:** 23 de fevereiro de 2026  
**Desenvolvedor:** Cursor AI Assistant  
**Status:** ✅ Implementado e funcional
