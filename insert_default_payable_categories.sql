-- Script SQL para inserir 10 categorias padrão globais de Contas a Pagar
-- Estas categorias serão compartilhadas por todas as empresas (user_id = NULL)
-- Execute este script no seu banco de dados MySQL
-- 
-- IMPORTANTE: Certifique-se de que a coluna user_id permite NULL antes de executar
-- Se necessário, execute primeiro: ALTER TABLE `payable_categories` MODIFY COLUMN `user_id` int(11) DEFAULT NULL;

INSERT IGNORE INTO `payable_categories` (`user_id`, `name`, `color`, `created_at`, `updated_at`) VALUES
(NULL, 'Alimentação', '#22C55E', NOW(), NOW()),
(NULL, 'Transporte', '#3B82F6', NOW(), NOW()),
(NULL, 'Serviços', '#FFBD59', NOW(), NOW()),
(NULL, 'Impostos', '#F87171', NOW(), NOW()),
(NULL, 'Fornecedores', '#8B5CF6', NOW(), NOW()),
(NULL, 'Salários', '#10B981', NOW(), NOW()),
(NULL, 'Aluguel', '#F59E0B', NOW(), NOW()),
(NULL, 'Energia/Água', '#06B6D4', NOW(), NOW()),
(NULL, 'Marketing', '#EC4899', NOW(), NOW()),
(NULL, 'Outros', '#9CA3AF', NOW(), NOW());

