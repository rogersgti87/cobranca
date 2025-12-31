-- Script SQL para adicionar sistema de categorias para Contas a Pagar
-- Execute este script no seu banco de dados MySQL

-- 1. Criar tabela de categorias de contas a pagar
CREATE TABLE IF NOT EXISTS `payable_categories` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'NULL para categorias globais, preenchido para categorias específicas do usuário',
  `name` varchar(255) NOT NULL,
  `color` varchar(7) DEFAULT '#FFBD59' COMMENT 'Cor em hexadecimal para exibição',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payable_categories_user_id_index` (`user_id`),
  KEY `payable_categories_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Adicionar coluna category_id na tabela payables
-- Nota: Execute estas linhas uma por vez se houver erro de coluna já existente
ALTER TABLE `payables`
  ADD COLUMN `category_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `supplier_id`;

ALTER TABLE `payables`
  ADD INDEX `payables_category_id_index` (`category_id`);

ALTER TABLE `payables`
  ADD CONSTRAINT `payables_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `payable_categories` (`id`) ON DELETE SET NULL;

-- 3. Inserir categorias padrão para cada usuário existente (opcional)
-- Você pode executar isso manualmente ou criar um seeder
-- Exemplo de categorias padrão:
-- INSERT INTO `payable_categories` (`user_id`, `name`, `color`, `created_at`, `updated_at`)
-- SELECT DISTINCT `user_id`, 'Alimentação', '#22C55E', NOW(), NOW() FROM `payables` WHERE `user_id` IS NOT NULL;
-- INSERT INTO `payable_categories` (`user_id`, `name`, `color`, `created_at`, `updated_at`)
-- SELECT DISTINCT `user_id`, 'Transporte', '#3B82F6', NOW(), NOW() FROM `payables` WHERE `user_id` IS NOT NULL;
-- INSERT INTO `payable_categories` (`user_id`, `name`, `color`, `created_at`, `updated_at`)
-- SELECT DISTINCT `user_id`, 'Serviços', '#FFBD59', NOW(), NOW() FROM `payables` WHERE `user_id` IS NOT NULL;
-- INSERT INTO `payable_categories` (`user_id`, `name`, `color`, `created_at`, `updated_at`)
-- SELECT DISTINCT `user_id`, 'Impostos', '#F87171', NOW(), NOW() FROM `payables` WHERE `user_id` IS NOT NULL;
-- INSERT INTO `payable_categories` (`user_id`, `name`, `color`, `created_at`, `updated_at`)
-- SELECT DISTINCT `user_id`, 'Outros', '#9CA3AF', NOW(), NOW() FROM `payables` WHERE `user_id` IS NOT NULL;

