# Solução para Erro de Migrations

## Problema

As tabelas já existem no banco de dados, mas o Laravel está tentando criá-las novamente através das migrations, resultando no erro:

```
SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'payables' already exists
```

## Solução

Como as tabelas já existem, precisamos marcar as migrations antigas como já executadas e então executar apenas as novas migrations.

### Opção 1: Usando a Migration Especial (RECOMENDADO)

Criei uma migration especial que marca as migrations antigas como executadas. Execute os seguintes comandos:

```bash
# 1. Executar APENAS a migration especial primeiro
php artisan migrate --path=database/migrations/2024_02_16_999999_mark_old_migrations_as_run.php

# 2. Agora executar todas as outras novas migrations
php artisan migrate
```

### Opção 2: Manualmente via SQL

Se preferir fazer manualmente, execute este SQL no seu banco de dados:

```sql
-- Marcar migrations antigas como executadas
INSERT INTO migrations (migration, batch) VALUES
('2014_10_12_000000_create_users_table', 1),
('2014_10_12_100000_create_password_resets_table', 1),
('2019_08_19_000000_create_failed_jobs_table', 1),
('2019_12_14_000001_create_personal_access_tokens_table', 1),
('2019_12_14_000002_customers_table', 1),
('2020_07_20_071395_create_services_table', 1),
('2020_07_20_100412_create_customer_services_table', 1),
('2020_07_20_111455_create_invoices_table', 1),
('2020_07_20_111456_create_invoice_notifications_table', 1),
('2020_07_20_111457_create_email_events_table', 1),
('2020_07_20_111458_create_gateway_payments_table', 1),
('2023_09_14_155141_create_jobs_table', 1),
('2023_09_14_155142_create_payables_table', 1)
ON DUPLICATE KEY UPDATE migration=migration;
```

Depois execute:
```bash
php artisan migrate
```

### Opção 3: Usar Migrate Fresh (CUIDADO - APAGA TODOS OS DADOS)

**⚠️ ATENÇÃO: Esta opção apaga TODOS os dados do banco!**

Apenas use se for ambiente de desenvolvimento e não tiver dados importantes:

```bash
php artisan migrate:fresh
```

## Ordem Recomendada

1. **Fazer backup do banco de dados**
   ```bash
   mysqldump -u root -proot cobrancasegura > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. **Marcar migrations antigas como executadas**
   ```bash
   php artisan migrate --path=database/migrations/2024_02_16_999999_mark_old_migrations_as_run.php
   ```

3. **Verificar migrations pendentes**
   ```bash
   php artisan migrate:status
   ```

4. **Executar novas migrations**
   ```bash
   php artisan migrate
   ```

5. **Executar migração de dados para multiempresa**
   ```bash
   # Testar primeiro
   php artisan migrate:multicompany --dry-run
   
   # Executar de verdade
   php artisan migrate:multicompany
   ```

## Verificação

Após executar as migrations, verifique se todas foram executadas:

```bash
php artisan migrate:status
```

Você deve ver algo como:

```
Migration name ......................................................... Batch / Status
2014_10_12_000000_create_users_table ................................... [1] Ran
2014_10_12_100000_create_password_resets_table ......................... [1] Ran
...
2024_02_16_000001_create_companies_table ............................... [2] Ran
2024_02_16_000002_create_company_user_table ............................ [2] Ran
...
```

## Troubleshooting

### Se ainda der erro após marcar migrations antigas

Verifique quais migrations já estão registradas:

```sql
SELECT * FROM migrations ORDER BY id;
```

Se alguma migration necessária não estiver registrada, adicione manualmente:

```sql
INSERT INTO migrations (migration, batch) VALUES
('nome_da_migration', 1);
```

### Se alguma coluna já existir

Algumas migrations de update podem dar erro se a coluna já existir. Neste caso, você pode:

1. Verificar no banco se a coluna existe
2. Se existir, marcar a migration como executada sem rodar
3. Se não existir, executar a migration normalmente

Exemplo:
```sql
-- Verificar se coluna existe
DESCRIBE customers;

-- Se company_id já existe, marcar migration como executada
INSERT INTO migrations (migration, batch) VALUES
('2024_02_16_100001_add_company_id_to_customers_table', 2);
```

## Notas Importantes

- As migrations de **criação de tabelas** (2014-2023) não devem ser executadas pois as tabelas já existem
- As migrations de **atualização** (2024_01_15_100xxx) adicionam campos que podem não existir
- As migrations **multiempresa** (2024_02_16_xxx) são todas novas e devem ser executadas

## Próximos Passos

Depois de resolver as migrations, continue com:
1. Verificar se todas as colunas foram adicionadas corretamente
2. Executar o comando de migração de dados (`migrate:multicompany`)
3. Implementar as atualizações nos Controllers e Commands conforme documentação
