# 📚 Documentação do Sistema Cobrança Segura - Multiempresa

## 📋 Índice da Documentação

### 1. [Estrutura Atual do Sistema](01-ESTRUTURA-ATUAL.md)
Documentação completa da estrutura original do sistema antes da implementação multiempresa:
- Tecnologias utilizadas
- Models e relacionamentos
- Estrutura de tabelas
- Controllers principais
- Commands (Cron Jobs)
- Rotas e autenticação

### 2. [Implementação Multiempresa](02-IMPLEMENTACAO-MULTIEMPRESA.md)
Guia técnico detalhado de como o sistema foi adaptado para multiempresa:
- Estrutura de banco de dados multiempresa
- Models e relacionamentos
- Middleware de contexto de empresa
- Exemplos de atualização de Controllers
- Exemplos de atualização de Commands
- Routes e views
- Script de migração de dados

### 3. [Solução de Erros de Migrations](03-SOLUCAO-ERRO-MIGRATIONS.md)
Documentação dos problemas encontrados durante a execução das migrations e suas soluções:
- Erro: Tabelas já existentes
- Erro: Doctrine DBAL
- Erro: Foreign key constraints
- Soluções aplicadas e justificativas

### 4. [Resultado da Migração de Dados](04-RESULTADO-MIGRACAO.md)
Relatório completo da migração de dados existentes para estrutura multiempresa:
- Estatísticas da migração
- Empresas criadas
- Usuários vinculados
- Dados migrados por tabela
- Verificações de integridade

### 5. [✅ Implementação Concluída](05-IMPLEMENTACAO-CONCLUIDA.md)
Resumo executivo da implementação completa:
- O que foi implementado
- Como usar o sistema multiempresa
- Dados migrados
- Observações importantes
- Arquivos modificados/criados
- Checklist final

### 6. [🔧 Correção de Erro: Loop Infinito](06-CORRECAO-ERRO-LOOP.md)
Documentação da correção do erro de loop infinito no User Model:
- Descrição do problema
- Causa raiz
- Solução aplicada
- Alternativas consideradas
- Lições aprendidas

### 7. [🔧 Correção: Ambiguidade em Queries SQL](07-CORRECAO-AMBIGUIDADE-SQL.md)
Documentação da correção de queries SQL ambíguas com JOINs:
- Erro "Column 'company_id' in WHERE is ambiguous"
- Por que aconteceu
- Correção nos scopes forCompany()
- Regras de qualificação SQL
- Padrões para evitar o problema

### 8. [🗑️ Remoção da VIEW view_invoices](08-REMOCAO-VIEW-INVOICES.md)
Substituição da VIEW por relacionamentos Eloquent:
- Problemas com a VIEW
- Solução com relacionamentos
- Arquivos modificados
- Mapeamento de campos
- Vantagens da nova abordagem
- Padrões de uso

### 9. [👥 Correção: Gestão de Usuários Multiempresa](09-CORRECAO-GESTAO-USUARIOS.md)
Adaptação da gestão de usuários para o sistema multiempresa:
- Problema: apenas superadmin via lista completa
- Solução: usuários veem usuários das suas empresas
- Vinculação automática à empresa ativa
- Isolamento de dados por empresa
- Comportamento para superadmin vs usuários normais

### 10. [🔄 Refatoração: Integrações Movidas para Empresas](10-REFATORACAO-USUARIOS-PARA-EMPRESA.md)
Grande refatoração movendo integrações de usuários para empresas:
- 26 campos removidos da tabela users
- 11 métodos removidos do UserController
- 11 rotas removidas
- Validação alterada para apenas CPF (não mais CNPJ)
- Integrações agora são gerenciadas por empresa
- Código 55% menor no UserController

---

## 🚀 Quick Start

Se você é novo no projeto ou quer entender rapidamente o sistema multiempresa:

1. **Leia primeiro**: [05-IMPLEMENTACAO-CONCLUIDA.md](05-IMPLEMENTACAO-CONCLUIDA.md)
   - Visão geral completa
   - Como usar o sistema
   - Status da implementação

2. **Para detalhes técnicos**: [02-IMPLEMENTACAO-MULTIEMPRESA.md](02-IMPLEMENTACAO-MULTIEMPRESA.md)
   - Arquitetura técnica
   - Padrões de código
   - Exemplos práticos

3. **Para histórico**: [01-ESTRUTURA-ATUAL.md](01-ESTRUTURA-ATUAL.md)
   - Como era o sistema antes
   - Estrutura original

---

## 🎯 Objetivo do Sistema Multiempresa

O sistema foi transformado para permitir que:
- ✅ Um usuário possa gerenciar **múltiplas empresas**
- ✅ Cada empresa tenha suas **próprias integrações** (gateways de pagamento, WhatsApp, etc.)
- ✅ Cada empresa tenha seus **próprios clientes, serviços, faturas e contas a pagar**
- ✅ Dashboard e relatórios sejam **filtrados por empresa**
- ✅ Comandos automáticos (cron) respeitem a **empresa selecionada**
- ✅ **Isolamento total** de dados entre empresas

---

## 📊 Status Atual

### ✅ Implementação: 100% CONCLUÍDA

- [x] Banco de dados reestruturado
- [x] Models atualizados
- [x] Controllers adaptados
- [x] Commands (Cron Jobs) ajustados
- [x] Middleware implementado
- [x] Views criadas
- [x] Rotas configuradas
- [x] Dados migrados
- [x] Documentação completa

**O sistema está pronto para uso em produção!** 🎉

---

## 🏗️ Arquitetura Multiempresa

```
┌─────────────┐
│   Usuário   │
└──────┬──────┘
       │ Pode gerenciar
       │
       ├─────┬─────────┬─────────┐
       │     │         │         │
   ┌───▼───┐ ┌───▼───┐ ┌───▼───┐ │
   │Empresa│ │Empresa│ │Empresa│ ...
   │   1   │ │   2   │ │   3   │
   └───┬───┘ └───┬───┘ └───┬───┘
       │         │         │
  ┌────┴───┐ ┌──┴───┐ ┌───┴────┐
  │Clientes│ │Faturas│ │Contas  │
  │Serviços│ │Notif. │ │a Pagar │
  │Config. │ │...    │ │...     │
  └────────┘ └───────┘ └────────┘
```

---

## 🔑 Conceitos-Chave

### Company (Empresa)
- Entidade central do sistema
- Armazena todas as configurações de integrações
- Relaciona-se com todos os dados de negócio

### Company Context (Contexto de Empresa)
- Empresa ativa do usuário logado
- Determinada por `current_company_id` na tabela `users`
- Aplicada automaticamente via Middleware

### Segregação de Dados
- Todos os queries filtram por `company_id`
- Uso do scope `forCompany()` nos Models
- Helpers: `currentCompanyId()`, `currentCompany()`

---

## 📁 Estrutura de Arquivos

```
cobrancasegura/
├── app/
│   ├── Models/
│   │   ├── Company.php ⭐ NOVO
│   │   ├── User.php (atualizado)
│   │   ├── Customer.php (atualizado)
│   │   └── ... (todos atualizados)
│   ├── Http/
│   │   ├── Controllers/admin/
│   │   │   ├── CompanyController.php ⭐ NOVO
│   │   │   └── ... (todos atualizados)
│   │   └── Middleware/
│   │       └── CompanyContext.php ⭐ NOVO
│   ├── Console/Commands/
│   │   ├── MigrateToMulticompany.php ⭐ NOVO
│   │   └── ... (todos atualizados)
│   └── Helpers/
│       └── company_helpers.php ⭐ NOVO
├── database/
│   └── migrations/
│       ├── 2024_02_16_000001_create_companies_table.php ⭐
│       ├── 2024_02_16_000002_create_company_user_table.php ⭐
│       ├── 2024_02_16_000003_add_current_company_id_to_users_table.php ⭐
│       └── ... (40+ migrations)
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── admin.blade.php (atualizado)
│       └── admin/
│           └── companies/ ⭐ NOVO
│               ├── index.blade.php
│               ├── create.blade.php
│               ├── edit.blade.php
│               └── integrations.blade.php
├── routes/
│   └── web.php (atualizado)
└── Docs/ ⭐ NOVO
    ├── README.md (este arquivo)
    ├── 01-ESTRUTURA-ATUAL.md
    ├── 02-IMPLEMENTACAO-MULTIEMPRESA.md
    ├── 03-SOLUCAO-ERRO-MIGRATIONS.md
    ├── 04-RESULTADO-MIGRACAO.md
    └── 05-IMPLEMENTACAO-CONCLUIDA.md
```

---

## 🛠️ Comandos Úteis

### Migrations
```bash
# Executar migrations
php artisan migrate

# Migrar dados para multiempresa
php artisan migrate:multicompany

# Reverter última migration
php artisan migrate:rollback

# Status das migrations
php artisan migrate:status
```

### Cache e Otimização
```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recarregar autoload
composer dump-autoload
```

### Cron Jobs
```bash
# Criar faturas
php artisan create:invoice

# Gerar PIX/Boletos
php artisan generate:invoice

# Enviar lembretes
php artisan remember:invoice

# Gerar contas recorrentes
php artisan generate:recurring-payables

# Renovar tokens Inter
php artisan token:inter

# Verificar status Inter
php artisan status:inter
```

---

## 🔍 Solução de Problemas

### Erro: "No company selected"
**Causa:** Usuário não tem empresa selecionada
**Solução:** Acesse `/admin/companies` e selecione uma empresa

### Erro: "You don't have access to this company"
**Causa:** Tentativa de acessar empresa não vinculada ao usuário
**Solução:** Verifique se o usuário está vinculado à empresa na tabela `company_user`

### Erro: "Class 'App\Helpers\...' not found"
**Causa:** Autoload não foi atualizado
**Solução:** Execute `composer dump-autoload`

### Dados não aparecem no dashboard
**Causa:** Dados não têm `company_id` ou empresa errada está selecionada
**Solução:** 
1. Verifique se a empresa correta está selecionada
2. Execute `php artisan migrate:multicompany` se ainda não executou

---

## 📞 Perguntas Frequentes (FAQ)

### 1. Posso ter dados sem empresa?
Não. Todos os dados devem estar vinculados a uma empresa. O middleware garante isso.

### 2. Como adicionar um usuário a uma empresa?
```sql
INSERT INTO company_user (user_id, company_id, role) 
VALUES (1, 1, 'admin');
```
Ou implemente uma tela de gestão de usuários por empresa (recomendado).

### 3. Posso migrar dados antigos?
Sim! Use o comando `php artisan migrate:multicompany`

### 4. Como desabilitar o middleware temporariamente?
Remova `\App\Http\Middleware\CompanyContext::class` do `app/Http/Kernel.php`
**Atenção:** Não recomendado em produção!

### 5. Posso ter usuários sem empresa?
Tecnicamente sim, mas o middleware redirecionará para a página de empresas. Recomendamos sempre vincular usuários a pelo menos uma empresa.

---

## 📝 Notas de Versão

### Versão 2.0 - Multiempresa (16/02/2026)
- ✨ Implementação completa de multiempresa
- ✨ Migração de dados existentes
- ✨ CRUD completo de empresas
- ✨ Configurações de integrações por empresa
- ✨ Seletor de empresa ativa
- ✨ Filtros automáticos em todos os módulos
- ✨ Commands atualizados para multiempresa
- 📚 Documentação completa

---

## 🎓 Para Desenvolvedores

### Ao Criar Novos Resources
Sempre que criar um novo Model/Controller para dados de negócio:

1. **No Model:**
   ```php
   protected $fillable = [..., 'company_id'];
   
   public function company() {
       return $this->belongsTo(Company::class);
   }
   
   public function scopeForCompany($query, $companyId) {
       return $query->where('company_id', $companyId);
   }
   ```

2. **No Controller:**
   ```php
   // Listar
   Model::forCompany(currentCompanyId())->get();
   
   // Criar
   Model::create([
       'company_id' => currentCompanyId(),
       // ... outros campos
   ]);
   ```

3. **Na Migration:**
   ```php
   $table->unsignedBigInteger('company_id');
   $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
   ```

### Padrões de Código
- Use sempre `currentCompanyId()` helper ao invés de acessar diretamente
- Adicione scope `forCompany()` em todos os models
- Filtre queries com `->forCompany(currentCompanyId())`
- Nunca permita acesso cross-company sem verificação explícita

---

## 🙏 Créditos

**Desenvolvido por:** Cursor AI Agent
**Data:** 16 de Fevereiro de 2026
**Versão:** 2.0 - Multiempresa

---

## 📄 Licença

Propriedade de Roger Guimarães / Cobrança Segura.
Todos os direitos reservados.
