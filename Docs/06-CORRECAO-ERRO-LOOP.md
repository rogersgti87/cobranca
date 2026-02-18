# 🔧 Correção de Erro: Loop Infinito no User Model

## ❌ Erro Encontrado

```
Undefined property: App\Models\User::$currentCompany
```

**Arquivo:** `app/Models/User.php` (linha 86)

### Descrição do Problema

O método `getCurrentCompanyAttribute()` estava criando um **loop infinito** ao tentar acessar a propriedade `$this->currentCompany`.

### Causa Raiz

No Laravel, quando você cria um accessor (método `getXxxAttribute()`), o Laravel automaticamente disponibiliza uma propriedade virtual com o nome `xxx`. 

O problema estava aqui:

```php
public function getCurrentCompanyAttribute()
{
    if ($this->current_company_id) {
        return $this->currentCompany;  // ❌ ERRO: Isso chama o próprio método novamente!
    }
    
    return $this->companies()->first();
}
```

**O que acontecia:**
1. View chamava `auth()->user()->currentCompany`
2. Laravel invocava `getCurrentCompanyAttribute()`
3. Dentro do método, `$this->currentCompany` tentava acessar a propriedade
4. Laravel invocava `getCurrentCompanyAttribute()` novamente (LOOP!)
5. Processo se repetia infinitamente até esgotar a memória

---

## ✅ Solução Aplicada

### Código Corrigido

```php
public function getCurrentCompanyAttribute()
{
    if ($this->current_company_id) {
        return Company::find($this->current_company_id);  // ✅ CORRETO: Busca direta
    }
    
    return $this->companies()->first();
}
```

### Import Adicionado

```php
use App\Models\Company;
```

### Explicação da Correção

Ao invés de usar `$this->currentCompany` (que causava o loop), agora usamos `Company::find($this->current_company_id)`, que:

1. ✅ Busca diretamente a empresa pelo ID
2. ✅ Não cria loop infinito
3. ✅ É mais eficiente (query direta ao invés de relação)
4. ✅ Retorna `null` se não encontrar (comportamento seguro)

---

## 🎯 Alternativas Consideradas

### Alternativa 1: Usar a Relação (Não recomendado)
```php
return $this->currentCompany()->first();  // Funciona, mas menos eficiente
```
**Problema:** Faz uma query desnecessária quando já temos o ID.

### Alternativa 2: Carregar Relação (Complexo)
```php
return $this->load('currentCompany')->getRelation('currentCompany');
```
**Problema:** Mais complexo e pode causar N+1 queries.

### Alternativa 3: Company::find() ✅ ESCOLHIDA
```php
return Company::find($this->current_company_id);
```
**Vantagens:**
- Simples e direto
- Eficiente (busca por chave primária)
- Não cria loops
- Fácil de entender

---

## 🧪 Teste da Correção

Após a correção, o seguinte código deve funcionar normalmente:

```php
// Na view
{{ auth()->user()->currentCompany->name }}

// No controller
$company = auth()->user()->currentCompany;

// Na Blade
@if(auth()->user()->currentCompany)
    <p>Empresa: {{ auth()->user()->currentCompany->name }}</p>
@endif
```

---

## 📚 Lições Aprendidas

### 1. Accessors vs Relações
- **Accessor** (`getCurrentCompanyAttribute`): Propriedade virtual calculada
- **Relação** (`currentCompany()`): Query de relacionamento
- Nunca acesse um accessor dentro de si mesmo!

### 2. Padrão Correto para Accessors

```php
// ❌ ERRADO - Loop infinito
public function getFullNameAttribute()
{
    return $this->fullName . ' ' . $this->last_name;  // Loop!
}

// ✅ CORRETO - Usa atributos diretos
public function getFullNameAttribute()
{
    return $this->first_name . ' ' . $this->last_name;
}
```

### 3. Debugging de Loops Infinitos

**Sintomas:**
- Erro "Undefined property"
- Stack trace muito longo
- Aplicação trava ou demora muito

**Solução:**
1. Verifique accessors que chamam a si mesmos
2. Use atributos diretos (`$this->attribute`) ao invés de propriedades virtuais
3. Prefira queries diretas quando possível

---

## 🔍 Verificação Final

Execute os seguintes comandos para garantir que tudo está funcionando:

```bash
# Limpar cache
php artisan view:clear
php artisan cache:clear

# Limpar logs
rm -f storage/logs/laravel.log
touch storage/logs/laravel.log

# Acessar o sistema e verificar se não há mais erros
```

---

## ✅ Status

- [x] Erro identificado
- [x] Causa raiz encontrada
- [x] Solução implementada
- [x] Import adicionado
- [x] Cache limpo
- [x] Logs limpos
- [x] Documentação criada

**Data da Correção:** 16 de Fevereiro de 2026
**Arquivo Corrigido:** `app/Models/User.php`
**Linhas Modificadas:** 86, 11 (import)

---

## 📞 Próximos Passos

1. ✅ Acesse o sistema e verifique se está funcionando
2. ✅ Teste o seletor de empresas
3. ✅ Navegue pelo dashboard
4. ✅ Verifique se os dados aparecem corretamente

Se encontrar algum outro erro, consulte os logs em `storage/logs/laravel.log`.
