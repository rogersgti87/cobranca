-- Script para criar tabela de histórico de estornos de contas a pagar
-- Execute este script no seu banco de dados MySQL
-- NOTA: Os campos reversed_at, reversal_reason e reversed_by na tabela payables não são mais necessários
-- pois agora usamos uma tabela separada (payable_reversals) para manter o histórico completo de todos os estornos

-- Criar tabela de histórico de estornos
CREATE TABLE IF NOT EXISTS `payable_reversals` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `payable_id` BIGINT UNSIGNED NOT NULL COMMENT 'ID da conta a pagar',
  `user_id` INT NOT NULL COMMENT 'ID do usuário que fez o estorno',
  `reversed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora do estorno',
  `reversal_reason` TEXT NULL DEFAULT NULL COMMENT 'Motivo do estorno',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payable_reversals_payable_id_index` (`payable_id`),
  KEY `payable_reversals_user_id_index` (`user_id`),
  CONSTRAINT `payable_reversals_payable_id_foreign` FOREIGN KEY (`payable_id`) REFERENCES `payables` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de estornos de contas a pagar';

