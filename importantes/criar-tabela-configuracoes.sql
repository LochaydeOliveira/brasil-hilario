-- Script para criar a tabela configuracoes
-- Execute este script se a tabela não existir ou estiver com estrutura incorreta

-- Remover tabela se existir (CUIDADO: isso apagará todos os dados)
-- DROP TABLE IF EXISTS configuracoes;

-- Criar tabela configuracoes
CREATE TABLE IF NOT EXISTS configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT,
    tipo ENUM('string', 'integer', 'boolean', 'float', 'array', 'json') DEFAULT 'string',
    grupo VARCHAR(50) DEFAULT 'geral',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_grupo (grupo),
    INDEX idx_chave (chave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verificar se a tabela foi criada
SELECT 'Tabela configuracoes criada com sucesso!' as status; 