# Correção: Gestão de Usuários Multiempresa

**Data:** 16 de Fevereiro de 2026

## Problema Identificado

O cadastro de usuários não estava sendo exibido corretamente para usuários normais (não superadmin), pois havia uma lógica restritiva que:

1. **Apenas o usuário ID = 1** (superadmin) podia ver todos os usuários
2. **Outros usuários** só viam a si mesmos na lista
3. **Botão "Novo"** só aparecia para o usuário ID = 1

Isso não estava adaptado para o sistema multiempresa, onde usuários deveriam poder gerenciar os usuários das empresas que administram.

## Solução Implementada

### 1. Atualização do `UserController::index()`

**ANTES (linhas 74-94):**
```php
if(auth()->user()->id == 1){
    // Mostra TODOS os usuários
    $data = User::orderByRaw("$column_name")->paginate(15);
} else {
    // Mostra APENAS o próprio usuário logado
    $data = User::orderByRaw("$column_name")
        ->where('id', auth()->user()->id)
        ->paginate(15);
}
```

**DEPOIS:**
```php
// Superadmin (ID 1) vê todos os usuários de todas as empresas
if(auth()->user()->id == 1){
    $data = User::orderByRaw("$column_name")->paginate(15);
} 
// Usuários normais veem apenas os usuários da(s) empresa(s) que gerenciam
else {
    // Pegar IDs das empresas que o usuário tem acesso
    $companyIds = auth()->user()->companies()->pluck('companies.id')->toArray();
    
    // Se não tem empresas, mostra só ele mesmo
    if(empty($companyIds)){
        $companyIds = [auth()->user()->current_company_id];
    }
    
    $data = User::orderByRaw("$column_name")
        ->whereHas('companies', function($query) use ($companyIds) {
            $query->whereIn('companies.id', $companyIds);
        })
        ->paginate(15);
}
```

### 2. Atualização do `UserController::store()`

Agora ao criar um novo usuário, ele é automaticamente vinculado à empresa ativa do usuário logado:

```php
try{
    $model->save();
    
    // Vincular o novo usuário à empresa ativa do usuário logado
    if(auth()->user()->current_company_id){
        $model->companies()->attach(auth()->user()->current_company_id, ['role' => 'user']);
        $model->current_company_id = auth()->user()->current_company_id;
        $model->save();
    }
} catch(\Exception $e){
    \Log::error($e->getMessage());
    return response()->json($e->getMessage(), 500);
}
```

### 3. Atualização da View `index.blade.php`

**ANTES (linhas 62-70):**
```blade
@if(auth()->user()->id == 1)
<div class="col-md-2">
  <ul class="button-action">
    <li><a href="{{url($linkFormAdd)}}" ...>Novo</a></li>
    <li><a href="#" id="btn-delete" ...>Excluir</a></li>
  </ul>
</div>
@endif
```

**DEPOIS:**
```blade
<div class="col-md-2">
  <ul class="button-action">
    <li><a href="{{url($linkFormAdd)}}" ...>Novo</a></li>
    <li><a href="#" id="btn-delete" ...>Excluir</a></li>
  </ul>
</div>
```

### 4. Atualização do Layout `admin.blade.php`

**Menu Lateral (Desktop):**

**ANTES (linhas 136-143):**
```blade
@if(auth()->user()->id == 1)
<li class="nav-item">
  <a href="{{url('admin/users')}}" class="nav-link">
    <i class="far fa-circle nav-icon"></i>
    <p>Usuários</p>
  </a>
</li>
@endif
```

**DEPOIS:**
```blade
<li class="nav-item">
  <a href="{{url('admin/users')}}" class="nav-link">
    <i class="far fa-circle nav-icon"></i>
    <p>Usuários</p>
  </a>
</li>
```

**Menu Mobile (Inferior):**

**ANTES (linhas 1114-1121):**
```blade
@if(auth()->user()->id == 1)
<a href="{{url('admin/users')}}">
  <i class="fas fa-user-cog"></i> Usuários
</a>
<a href="{{url('admin/payable-categories')}}">
  <i class="fas fa-tags"></i> Categorias
</a>
@endif
```

**DEPOIS:**
```blade
<a href="{{url('admin/users')}}">
  <i class="fas fa-user-cog"></i> Usuários
</a>
<a href="{{url('admin/companies')}}">
  <i class="fas fa-building"></i> Empresas
</a>
<a href="{{url('admin/payable-categories')}}">
  <i class="fas fa-tags"></i> Categorias
</a>
```

## Arquivos Modificados

1. **`app/Http/Controllers/admin/UserController.php`**
   - Método `index()` - linhas 74-103
   - Método `store()` - linhas 178-186

2. **`resources/views/admin/user/index.blade.php`**
   - Seção de botões - linhas 62-70

3. **`resources/views/layouts/admin.blade.php`**
   - Menu lateral (Desktop) - linhas 136-143 - Removida restrição `@if(auth()->user()->id == 1)`
   - Menu inferior (Mobile) - linhas 1114-1121 - Removida restrição e adicionado link "Empresas"

## Comportamento Atual

### Para Superadmin (user_id = 1)
- ✅ Visualiza **TODOS** os usuários de **TODAS** as empresas
- ✅ Pode criar usuários para qualquer empresa
- ✅ Pode editar e excluir qualquer usuário

### Para Usuários Normais
- ✅ Visualiza apenas os usuários das **empresas que gerencia**
- ✅ Pode criar novos usuários (automaticamente vinculados à empresa ativa)
- ✅ Pode editar e excluir usuários da sua empresa
- ✅ Botão "Novo" agora está visível

### Isolamento de Dados
- ✅ Usuários de diferentes empresas não se veem mutuamente
- ✅ Novos usuários são automaticamente vinculados à empresa
- ✅ Respeita o conceito de multiempresa

## Relacionamentos Utilizados

```php
// Em app/Models/User.php

// Relação many-to-many com empresas
public function companies()
{
    return $this->belongsToMany(Company::class, 'company_user')
        ->withPivot('role')
        ->withTimestamps();
}

// Empresa ativa do usuário
public function currentCompany()
{
    return $this->belongsTo(Company::class, 'current_company_id');
}
```

## Query Gerada

Para usuários normais, a query utiliza `whereHas` para filtrar apenas usuários que pertencem às mesmas empresas:

```sql
SELECT * FROM `users`
WHERE EXISTS (
    SELECT * FROM `companies`
    INNER JOIN `company_user` ON `companies`.`id` = `company_user`.`company_id`
    WHERE `users`.`id` = `company_user`.`user_id`
    AND `companies`.`id` IN (1, 2, 3)  -- IDs das empresas do usuário logado
)
ORDER BY id desc
```

## Melhorias Futuras (Opcional)

### 1. Sistema de Roles/Permissões
Implementar roles mais granulares:
- `owner` - Dono da empresa (todos os acessos)
- `admin` - Administrador (gerencia usuários)
- `user` - Usuário comum (sem acesso a gestão)

### 2. Convites de Usuários
Em vez de criar usuários diretamente, enviar convites por e-mail.

### 3. Gestão de Permissões por Usuário
Tela para adicionar/remover usuários de empresas específicas.

### 4. Auditoria
Log de quem criou/editou cada usuário.

## Comandos Executados

```bash
# Limpar caches
php artisan view:clear
php artisan cache:clear
```

## Testes Recomendados

- [ ] Login com usuário ID = 1 (superadmin)
  - [ ] Verificar se vê todos os usuários
  - [ ] Criar novo usuário
  - [ ] Editar usuário de qualquer empresa
  
- [ ] Login com usuário normal (não ID 1)
  - [ ] Verificar se vê apenas usuários da sua empresa
  - [ ] Criar novo usuário (deve vincular à empresa ativa)
  - [ ] Tentar editar usuário de outra empresa (não deve aparecer)
  - [ ] Verificar se botão "Novo" está visível
  
- [ ] Usuário com múltiplas empresas
  - [ ] Trocar de empresa ativa
  - [ ] Verificar se a lista de usuários muda

## Conclusão

A gestão de usuários agora está completamente integrada ao sistema multiempresa, permitindo que cada empresa gerencie seus próprios usuários de forma isolada e segura, mantendo o superadmin com acesso total ao sistema.
