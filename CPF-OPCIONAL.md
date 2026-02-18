# ✅ CPF Agora é Opcional

**Data:** 16 de Fevereiro de 2026

## 🎯 Mudança Aplicada

O campo **CPF** no cadastro de usuários agora é **OPCIONAL**.

## ❌ Antes
- Campo CPF era **obrigatório** (required)
- Sistema exigia CPF válido para criar/editar usuário
- Erro se CPF não fosse preenchido ou inválido

## ✅ Agora
- Campo CPF é **opcional**
- Sistema valida apenas **SE** o CPF for preenchido
- Se CPF vazio = aceito normalmente
- Se CPF preenchido = valida se é válido (11 dígitos)

## 📋 Mudanças Realizadas

### 1. **UserController** - Método `store()`
**ANTES:**
```php
// Validar apenas CPF
$cpf = preg_replace('/[^0-9]/', '', $data['document']);
if (strlen($cpf) != 11 || !validarCPF($cpf)) {
    return response()->json('CPF inválido!', 422);
}
$model->document = removeEspeciais($data['document']);
```

**DEPOIS:**
```php
// Validar CPF apenas se foi preenchido
if(isset($data['document']) && !empty($data['document'])){
    $cpf = preg_replace('/[^0-9]/', '', $data['document']);
    if (strlen($cpf) != 11 || !validarCPF($cpf)) {
        return response()->json('CPF inválido!', 422);
    }
    $model->document = removeEspeciais($data['document']);
}else{
    $model->document = null;
}
```

### 2. **UserController** - Método `update()`
Mesma lógica aplicada ao método de atualização.

### 3. **View `form.blade.php`**
**ANTES:**
```html
<input type="text" name="document" required maxlength="14" />
```

**DEPOIS:**
```html
<input type="text" name="document" maxlength="14" placeholder="Opcional" />
```

- ❌ Removido: `required`
- ✅ Adicionado: `placeholder="Opcional"`

## 📁 Arquivos Modificados

1. ✅ `app/Http/Controllers/admin/UserController.php`
   - Método `store()` - validação condicional
   - Método `update()` - validação condicional

2. ✅ `resources/views/admin/user/form.blade.php`
   - Campo `document` - removido `required`, adicionado placeholder

3. ✅ Caches limpos

## 🎯 Comportamento

### Cenário 1: CPF Vazio
- ✅ Usuário pode ser criado/editado sem CPF
- ✅ Campo `document` salvo como `NULL`
- ✅ Sem erros de validação

### Cenário 2: CPF Preenchido Válido
- ✅ CPF é validado (11 dígitos + dígitos verificadores)
- ✅ Usuário criado/editado normalmente
- ✅ CPF salvo no banco

### Cenário 3: CPF Preenchido Inválido
- ❌ Sistema retorna erro: "CPF inválido!"
- ❌ Usuário não é criado/editado
- ✅ Validação continua funcionando

## 🧪 Como Testar

1. Acesse **"Cadastros" > "Usuários" > "Novo"**
2. Preencha apenas:
   - Nome
   - Email
   - Senha
   - **Deixe CPF em branco**
3. Clique em "Salvar"
4. ✅ Usuário deve ser criado sem erros

5. Agora edite o usuário e adicione um CPF inválido (ex: 111.111.111-11)
6. ❌ Deve dar erro: "CPF inválido!"

7. Adicione um CPF válido
8. ✅ Deve salvar normalmente

## ✅ Status

**Mudança aplicada com sucesso!**

- ✅ CPF é opcional
- ✅ Validação funciona apenas quando preenchido
- ✅ Placeholder "Opcional" adicionado
- ✅ Caches limpos

---

**Agora você pode criar usuários sem CPF!** 🎉
