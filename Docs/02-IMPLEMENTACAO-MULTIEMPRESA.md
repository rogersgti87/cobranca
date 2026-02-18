# Implementação do Sistema Multiempresa

## Visão Geral

O sistema foi preparado para suportar múltiplas empresas por usuário. Esta documentação descreve todas as alterações necessárias para completar a implementação.

## Estrutura Criada

### Tabelas

#### 1. companies
Armazena as empresas do sistema. Inclui todas as configurações de integrações (gateways, WhatsApp, etc).

**Campos principais:**
- id
- name, trade_name, document, type
- Endereço completo
- Configurações de gateway (PagHiper, Mercado Pago, Banco Inter, Asaas)
- Configurações de WhatsApp
- Logo

#### 2. company_user (pivot)
Relacionamento many-to-many entre users e companies.

**Campos:**
- id
- company_id
- user_id
- role (owner, admin, user)

#### 3. current_company_id em users
Campo adicionado na tabela users para armazenar a empresa atualmente selecionada.

### Migrations Criadas

✅ **Criadas** - Todas as migrations necessárias foram criadas:
- `create_suppliers_table.php`
- `create_payable_categories_table.php`
- `create_payable_reversals_table.php`
- Atualizações de tabelas existentes (users, customers, invoices, payables, etc)
- `create_companies_table.php`
- `create_company_user_table.php`
- `add_current_company_id_to_users_table.php`
- Adição de `company_id` em todas as tabelas relevantes

### Models Atualizados

✅ **Criados/Atualizados:**
- `Company` - Model principal de empresas
- `User` - Relações com companies
- `Customer`, `Service`, `CustomerService` - Relações com company
- `Invoice`, `InvoiceNotification` - Relações com company
- `Payable`, `PayableCategory`, `Supplier` - Relações com company
- `EmailEvent` - Model novo para eventos de email

### Middleware

✅ **Criado:** `CompanyContext`
- Gerencia a empresa ativa do usuário
- Disponibiliza empresa no request e nas views
- Redireciona se usuário não tiver empresa vinculada

### Helpers

✅ **Criados:** `app/Helpers/company_helpers.php`
```php
currentCompanyId()    // Retorna ID da empresa atual
currentCompany()      // Retorna objeto Company atual
hasCompanyAccess($id) // Verifica acesso à empresa
switchCompany($id)    // Troca empresa ativa
```

## Implementação Necessária

### 1. Registro do Middleware

**Arquivo:** `app/Http/Kernel.php`

Adicionar o middleware ao grupo `web`:

```php
protected $middlewareGroups = [
    'web' => [
        // ... middlewares existentes
        \App\Http\Middleware\CompanyContext::class,
    ],
];
```

Ou criar alias:

```php
protected $routeMiddleware = [
    // ... outros middlewares
    'company.context' => \App\Http\Middleware\CompanyContext::class,
];
```

E aplicar nas rotas admin:

```php
// routes/web.php
Route::middleware(['auth', 'company.context'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    // ... outras rotas
});
```

### 2. Atualização de Controllers

#### Exemplo: CustomerController

**ANTES:**
```php
public function index()
{
    $customers = Customer::where('user_id', auth()->id())
        ->where('status', 'Ativo')
        ->paginate(15);
    
    return view('admin.customers.index', compact('customers'));
}

public function store(Request $request)
{
    $customer = Customer::create([
        'user_id' => auth()->id(),
        'name' => $request->name,
        // ... outros campos
    ]);
    
    return redirect()->route('customers.index');
}
```

**DEPOIS:**
```php
public function index()
{
    $customers = Customer::forCompany(currentCompanyId())
        ->where('status', 'Ativo')
        ->paginate(15);
    
    return view('admin.customers.index', compact('customers'));
}

public function store(Request $request)
{
    $customer = Customer::create([
        'company_id' => currentCompanyId(),
        'user_id' => auth()->id(),
        'name' => $request->name,
        // ... outros campos
    ]);
    
    return redirect()->route('customers.index');
}
```

#### Exemplo: InvoiceController

**ANTES:**
```php
public function index(Request $request)
{
    $query = Invoice::where('user_id', auth()->id());
    
    if ($request->status) {
        $query->where('status', $request->status);
    }
    
    $invoices = $query->orderBy('date_due', 'desc')->paginate(20);
    
    return view('admin.invoices.index', compact('invoices'));
}
```

**DEPOIS:**
```php
public function index(Request $request)
{
    $query = Invoice::forCompany(currentCompanyId());
    
    if ($request->status) {
        $query->where('status', $request->status);
    }
    
    $invoices = $query->orderBy('date_due', 'desc')->paginate(20);
    
    return view('admin.invoices.index', compact('invoices'));
}
```

#### Exemplo: PayableController

**ANTES:**
```php
public function store(Request $request)
{
    $data = $request->validate([
        'supplier_id' => 'required',
        'description' => 'required',
        'price' => 'required',
        // ... validações
    ]);
    
    $data['user_id'] = auth()->id();
    
    Payable::create($data);
    
    return redirect()->route('payables.index');
}
```

**DEPOIS:**
```php
public function store(Request $request)
{
    $data = $request->validate([
        'supplier_id' => 'required',
        'description' => 'required',
        'price' => 'required',
        // ... validações
    ]);
    
    $data['company_id'] = currentCompanyId();
    $data['user_id'] = auth()->id();
    
    Payable::create($data);
    
    return redirect()->route('payables.index');
}
```

### 3. Atualização de Commands

#### Exemplo: CreateInvoiceCron

**ANTES:**
```php
public function handle()
{
    $customer_services = CustomerService::where('status', 'Ativo')
        ->whereDate('start_billing', '<=', Carbon::now())
        ->get();
    
    foreach ($customer_services as $service) {
        // Lógica de criação de fatura
        Invoice::create([
            'user_id' => $service->user_id,
            'customer_id' => $service->customer_id,
            // ... outros campos
        ]);
    }
}
```

**DEPOIS:**
```php
public function handle()
{
    $customer_services = CustomerService::where('status', 'Ativo')
        ->whereDate('start_billing', '<=', Carbon::now())
        ->whereNotNull('company_id')
        ->get();
    
    foreach ($customer_services as $service) {
        // Lógica de criação de fatura
        Invoice::create([
            'company_id' => $service->company_id,
            'user_id' => $service->user_id,
            'customer_id' => $service->customer_id,
            // ... outros campos
        ]);
    }
}
```

#### Exemplo: RememberInvoiceCron

**ANTES:**
```php
public function handle()
{
    // Enviar lembretes 5 dias antes
    $invoices_5_days = Invoice::where('status', 'Pendente')
        ->whereDate('date_due', Carbon::now()->addDays(5))
        ->get();
    
    foreach ($invoices_5_days as $invoice) {
        InvoiceNotification::Email($invoice->id);
        InvoiceNotification::Whatsapp($invoice->id);
    }
}
```

**DEPOIS:**
```php
public function handle()
{
    // Enviar lembretes 5 dias antes
    $invoices_5_days = Invoice::where('status', 'Pendente')
        ->whereDate('date_due', Carbon::now()->addDays(5))
        ->whereNotNull('company_id')
        ->get();
    
    foreach ($invoices_5_days as $invoice) {
        // As notificações serão associadas à empresa
        InvoiceNotification::Email($invoice->id);
        InvoiceNotification::Whatsapp($invoice->id);
        
        // Salvar notificação com company_id
        // (já implementado nos métodos Email e Whatsapp)
    }
}
```

#### Exemplo: GenerateRecurringPayables

**ANTES:**
```php
public function handle()
{
    $payables = Payable::where('type', 'Recorrente')
        ->where('status', 'Pendente')
        ->whereDate('recurrence_end', '>=', Carbon::now())
        ->get();
    
    foreach ($payables as $payable) {
        // Criar nova conta recorrente
        Payable::create([
            'user_id' => $payable->user_id,
            'supplier_id' => $payable->supplier_id,
            // ... outros campos
        ]);
    }
}
```

**DEPOIS:**
```php
public function handle()
{
    $payables = Payable::where('type', 'Recorrente')
        ->where('status', 'Pendente')
        ->whereDate('recurrence_end', '>=', Carbon::now())
        ->whereNotNull('company_id')
        ->get();
    
    foreach ($payables as $payable) {
        // Criar nova conta recorrente
        Payable::create([
            'company_id' => $payable->company_id,
            'user_id' => $payable->user_id,
            'supplier_id' => $payable->supplier_id,
            // ... outros campos
        ]);
    }
}
```

### 4. Atualização de Notificações

Os métodos `InvoiceNotification::Email()` e `InvoiceNotification::Whatsapp()` precisam salvar `company_id`:

```php
// Dentro do método Email ou Whatsapp
DB::table('invoice_notifications')->insert([
    'company_id' => $invoice['company_id'], // ADICIONAR ESTE CAMPO
    'user_id' => $data['user_id'],
    'invoice_id' => $data['invoice'],
    'type_send' => 'email', // ou 'whatsapp'
    'date' => Carbon::now(),
    // ... outros campos
]);
```

### 5. Controller de Empresas

Criar `CompanyController` para gerenciamento de empresas:

```php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = auth()->user()->companies;
        
        return view('admin.companies.index', compact('companies'));
    }
    
    public function create()
    {
        return view('admin.companies.create');
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'nullable|string|max:20',
            'type' => 'required|in:Física,Jurídica',
            // ... outras validações
        ]);
        
        $company = Company::create($data);
        
        // Vincular usuário atual como owner
        $company->users()->attach(auth()->id(), ['role' => 'owner']);
        
        // Definir como empresa ativa se for a primeira
        if (!auth()->user()->current_company_id) {
            auth()->user()->update(['current_company_id' => $company->id]);
        }
        
        return redirect()->route('companies.index')
            ->with('success', 'Empresa criada com sucesso!');
    }
    
    public function edit(Company $company)
    {
        // Verificar se usuário tem acesso
        if (!$company->hasUser(auth()->id())) {
            abort(403, 'Você não tem acesso a esta empresa');
        }
        
        return view('admin.companies.edit', compact('company'));
    }
    
    public function update(Request $request, Company $company)
    {
        // Verificar se usuário é admin ou owner
        if (!$company->isAdminOrOwner(auth()->id())) {
            abort(403, 'Você não tem permissão para editar esta empresa');
        }
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            // ... outras validações
        ]);
        
        $company->update($data);
        
        return redirect()->route('companies.index')
            ->with('success', 'Empresa atualizada com sucesso!');
    }
    
    public function switch(Company $company)
    {
        if (!$company->hasUser(auth()->id())) {
            abort(403, 'Você não tem acesso a esta empresa');
        }
        
        auth()->user()->update(['current_company_id' => $company->id]);
        
        return redirect()->back()
            ->with('success', 'Empresa alterada com sucesso!');
    }
}
```

### 6. Rotas Necessárias

```php
// routes/web.php
Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Gerenciamento de empresas
    Route::resource('companies', CompanyController::class);
    Route::post('companies/{company}/switch', [CompanyController::class, 'switch'])
        ->name('companies.switch');
    
    // Rotas existentes com middleware company.context
    Route::middleware(['company.context'])->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::resource('services', ServiceController::class);
        Route::resource('invoices', InvoiceController::class);
        Route::resource('payables', PayableController::class);
        // ... outras rotas
    });
});
```

### 7. Views - Seletor de Empresa

Adicionar seletor de empresa no layout admin:

```blade
<!-- resources/views/layouts/admin.blade.php -->
<div class="company-selector">
    <select id="company-select" class="form-control" onchange="switchCompany(this.value)">
        @foreach(auth()->user()->companies as $company)
            <option value="{{ $company->id }}" 
                {{ auth()->user()->current_company_id == $company->id ? 'selected' : '' }}>
                {{ $company->name }}
            </option>
        @endforeach
    </select>
</div>

<script>
function switchCompany(companyId) {
    const url = "{{ route('companies.switch', ':id') }}".replace(':id', companyId);
    
    // Criar formulário e enviar POST
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    
    form.appendChild(csrf);
    document.body.appendChild(form);
    form.submit();
}
</script>
```

## Migração de Dados Existentes

### Script de Migração

Criar um Command para migrar dados existentes:

```php
// app/Console/Commands/MigrateToMulticompany.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\Payable;
use App\Models\Supplier;
use App\Models\PayableCategory;
use DB;

class MigrateToMulticompany extends Command
{
    protected $signature = 'migrate:multicompany';
    protected $description = 'Migra dados existentes para o sistema multiempresa';

    public function handle()
    {
        DB::beginTransaction();
        
        try {
            $users = User::all();
            
            foreach ($users as $user) {
                $this->info("Processando usuário: {$user->name}");
                
                // Criar empresa para o usuário baseado nos dados dele
                $company = Company::create([
                    'name' => $user->company ?? $user->name,
                    'document' => $user->document,
                    'type' => 'Jurídica',
                    'email' => $user->email,
                    'phone' => $user->telephone,
                    'whatsapp' => $user->whatsapp,
                    'cep' => $user->cep,
                    'address' => $user->address,
                    'number' => $user->number,
                    'complement' => $user->complement,
                    'district' => $user->district,
                    'city' => $user->city,
                    'state' => $user->state,
                    'chave_pix' => $user->chave_pix,
                    'token_paghiper' => $user->token_paghiper,
                    'key_paghiper' => $user->key_paghiper,
                    'access_token_paghiper' => $user->access_token_paghiper,
                    'access_token_mp' => $user->access_token_mp,
                    'inter_host' => $user->inter_host,
                    'inter_client_id' => $user->inter_client_id,
                    'inter_client_secret' => $user->inter_client_secret,
                    'inter_scope' => $user->inter_scope,
                    'inter_crt_file' => $user->inter_crt_file,
                    'inter_key_file' => $user->inter_key_file,
                    'inter_crt_file_webhook' => $user->inter_crt_file_webhook,
                    'inter_webhook_url_billet' => $user->inter_webhook_url_billet,
                    'inter_webhook_url_pix' => $user->inter_webhook_url_pix,
                    'inter_chave_pix' => $user->inter_chave_pix,
                    'access_token_inter' => $user->access_token_inter,
                    'use_intermedium' => $user->use_intermedium,
                    'environment_asaas' => $user->environment_asaas,
                    'at_asaas_prod' => $user->at_asaas_prod,
                    'at_asaas_test' => $user->at_asaas_test,
                    'asaas_url_test' => $user->asaas_url_test,
                    'asaas_url_prod' => $user->asaas_url_prod,
                    'api_session_whatsapp' => $user->api_session_whatsapp,
                    'api_token_whatsapp' => $user->api_token_whatsapp,
                    'api_status_whatsapp' => $user->api_status_whatsapp,
                    'day_generate_invoice' => $user->day_generate_invoice,
                    'send_generate_invoice' => $user->send_generate_invoice,
                    'typebot_id' => $user->typebot_id,
                    'typebot_enable' => $user->typebot_enable,
                    'logo' => $user->image,
                    'status' => 'Ativo',
                ]);
                
                // Vincular usuário à empresa como owner
                $company->users()->attach($user->id, ['role' => 'owner']);
                
                // Definir como empresa atual
                $user->update(['current_company_id' => $company->id]);
                
                // Atualizar todos os registros do usuário com company_id
                Customer::where('user_id', $user->id)->update(['company_id' => $company->id]);
                Service::where('user_id', $user->id)->update(['company_id' => $company->id]);
                DB::table('customer_services')->where('user_id', $user->id)->update(['company_id' => $company->id]);
                Invoice::where('user_id', $user->id)->update(['company_id' => $company->id]);
                DB::table('invoice_notifications')->where('user_id', $user->id)->update(['company_id' => $company->id]);
                Payable::where('user_id', $user->id)->update(['company_id' => $company->id]);
                Supplier::where('user_id', $user->id)->update(['company_id' => $company->id]);
                PayableCategory::where('user_id', $user->id)->update(['company_id' => $company->id]);
                DB::table('email_events')->where('user_id', $user->id)->update(['company_id' => $company->id]);
                
                $this->info("✓ Empresa criada e dados migrados para: {$company->name}");
            }
            
            DB::commit();
            $this->info("\n✓ Migração concluída com sucesso!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Erro na migração: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
```

## Checklist de Implementação

### Banco de Dados
- [x] Criar migrations
- [ ] Executar migrations: `php artisan migrate`
- [ ] Executar migração de dados: `php artisan migrate:multicompany`

### Backend
- [x] Criar Models
- [x] Criar Middleware
- [x] Criar Helpers
- [ ] Registrar Middleware no Kernel
- [ ] Atualizar Controllers
- [ ] Atualizar Commands
- [ ] Criar CompanyController
- [ ] Criar rotas de empresas
- [ ] Atualizar métodos de notificação

### Frontend
- [ ] Criar views de empresas (index, create, edit)
- [ ] Adicionar seletor de empresas no layout admin
- [ ] Atualizar formulários com validações de empresa

### Testes
- [ ] Testar criação de empresa
- [ ] Testar troca de empresa
- [ ] Testar criação de clientes/serviços/faturas por empresa
- [ ] Testar filtros por empresa
- [ ] Testar commands com múltiplas empresas
- [ ] Testar permissões de acesso

## Importante!

1. **Backup do banco de dados** antes de executar as migrations
2. **Testar em ambiente de desenvolvimento** antes de produção
3. **Validar integrações** (gateways, WhatsApp) por empresa
4. **Configurar permissões** adequadas (owner, admin, user)
5. **Documentar para equipe** as mudanças no fluxo
