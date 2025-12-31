-- Script SQL para criar as tabelas de Fornecedores e Contas a Pagar
-- Execute este script manualmente no seu banco de dados MySQL

-- Tabela de Fornecedores (Suppliers)
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('Física','Jurídica') NOT NULL DEFAULT 'Física',
  `document` varchar(20) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email2` varchar(255) DEFAULT NULL,
  `status` enum('Ativo','Inativo') NOT NULL DEFAULT 'Ativo',
  `cep` varchar(10) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `number` varchar(10) DEFAULT NULL,
  `complement` varchar(255) DEFAULT NULL,
  `district` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suppliers_user_id_index` (`user_id`),
  KEY `suppliers_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Contas a Pagar (Payables)
-- Primeiro, vamos verificar se a tabela já existe e adicionar as colunas necessárias
-- Se a tabela payables já existir, execute apenas os ALTER TABLE abaixo

-- Se a tabela não existir, crie ela completa:
CREATE TABLE IF NOT EXISTS `payables` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `type` enum('Fixa','Recorrente','Parcelada') NOT NULL DEFAULT 'Fixa',
  `payment_method` varchar(50) DEFAULT NULL,
  `date_due` date NOT NULL,
  `date_payment` date DEFAULT NULL,
  `status` enum('Pendente','Pago','Cancelado') NOT NULL DEFAULT 'Pendente',
  `recurrence_period` varchar(20) DEFAULT NULL COMMENT 'Semanal, Quinzenal, Mensal, Bimestral, Trimestral, Semestral, Anual',
  `recurrence_day` int(11) DEFAULT NULL COMMENT 'Dia do mês para recorrência',
  `recurrence_end` date DEFAULT NULL COMMENT 'Data de término da recorrência',
  `installments` int(11) DEFAULT NULL COMMENT 'Número total de parcelas',
  `installment_number` int(11) DEFAULT NULL COMMENT 'Número da parcela atual',
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID da conta pai (para parcelas)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payables_user_id_index` (`user_id`),
  KEY `payables_supplier_id_index` (`supplier_id`),
  KEY `payables_status_index` (`status`),
  KEY `payables_date_due_index` (`date_due`),
  KEY `payables_type_index` (`type`),
  KEY `payables_parent_id_index` (`parent_id`),
  CONSTRAINT `payables_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Se a tabela payables já existir, execute apenas estes ALTER TABLE:
-- ALTER TABLE `payables` 
--   ADD COLUMN IF NOT EXISTS `supplier_id` bigint(20) UNSIGNED NOT NULL AFTER `user_id`,
--   ADD COLUMN IF NOT EXISTS `type` enum('Fixa','Recorrente','Parcelada') NOT NULL DEFAULT 'Fixa' AFTER `price`,
--   ADD COLUMN IF NOT EXISTS `recurrence_period` varchar(20) DEFAULT NULL COMMENT 'Semanal, Quinzenal, Mensal, Bimestral, Trimestral, Semestral, Anual' AFTER `status`,
--   ADD COLUMN IF NOT EXISTS `recurrence_day` int(11) DEFAULT NULL COMMENT 'Dia do mês para recorrência' AFTER `recurrence_period`,
--   ADD COLUMN IF NOT EXISTS `recurrence_end` date DEFAULT NULL COMMENT 'Data de término da recorrência' AFTER `recurrence_day`,
--   ADD COLUMN IF NOT EXISTS `installments` int(11) DEFAULT NULL COMMENT 'Número total de parcelas' AFTER `recurrence_end`,
--   ADD COLUMN IF NOT EXISTS `installment_number` int(11) DEFAULT NULL COMMENT 'Número da parcela atual' AFTER `installments`,
--   ADD COLUMN IF NOT EXISTS `parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID da conta pai (para parcelas)' AFTER `installment_number`,
--   ADD INDEX IF NOT EXISTS `payables_supplier_id_index` (`supplier_id`),
--   ADD INDEX IF NOT EXISTS `payables_type_index` (`type`),
--   ADD INDEX IF NOT EXISTS `payables_parent_id_index` (`parent_id`),
--   ADD CONSTRAINT IF NOT EXISTS `payables_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

-- Nota: O MySQL não suporta IF NOT EXISTS em ALTER TABLE diretamente.
-- Se você já tem a tabela payables, remova as colunas antigas relacionadas a invoices/customers
-- e adicione as novas colunas manualmente conforme necessário.

