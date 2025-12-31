-- Script SQL para atualizar a tabela payable_categories para permitir user_id NULL
-- Execute este script se a tabela já existir e tiver user_id como NOT NULL

-- Alterar coluna user_id para permitir NULL
ALTER TABLE `payable_categories` 
  MODIFY COLUMN `user_id` int(11) DEFAULT NULL COMMENT 'NULL para categorias globais, preenchido para categorias específicas do usuário';

-- Inserir 10 categorias padrão globais (user_id = NULL)
INSERT INTO `payable_categories` (`user_id`, `name`, `color`, `created_at`, `updated_at`) VALUES
(NULL, 'Alimentação', '#22C55E', NOW(), NOW()),
(NULL, 'Transporte', '#3B82F6', NOW(), NOW()),
(NULL, 'Serviços', '#FFBD59', NOW(), NOW()),
(NULL, 'Impostos', '#F87171', NOW(), NOW()),
(NULL, 'Fornecedores', '#8B5CF6', NOW(), NOW()),
(NULL, 'Salários', '#10B981', NOW(), NOW()),
(NULL, 'Aluguel', '#F59E0B', NOW(), NOW()),
(NULL, 'Energia/Água', '#06B6D4', NOW(), NOW()),
(NULL, 'Marketing', '#EC4899', NOW(), NOW()),
(NULL, 'Outros', '#9CA3AF', NOW(), NOW())
ON DUPLICATE KEY UPDATE `name` = `name`; -- Evita duplicatas se executar novamente

