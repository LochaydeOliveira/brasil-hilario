-- Script para adicionar a coluna 'marca' na tabela 'anuncios'
-- Execute este script no phpMyAdmin ou no seu servidor MySQL

-- Verificar se a coluna já existe
SELECT COUNT(*) as existe FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'anuncios' 
AND COLUMN_NAME = 'marca';

-- Adicionar a coluna marca se não existir
ALTER TABLE `anuncios` 
ADD COLUMN `marca` ENUM('', 'amazon', 'shopee') DEFAULT '' 
AFTER `link_compra`;

-- Verificar a estrutura atual da tabela
DESCRIBE `anuncios`;
