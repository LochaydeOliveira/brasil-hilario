-- Adicionar funcionalidade de posts específicos aos grupos de anúncios
-- Criado para o projeto Brasil Hilário

-- Tabela para associar grupos de anúncios a posts específicos
CREATE TABLE IF NOT EXISTS grupos_anuncios_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grupo_id INT NOT NULL,
    post_id INT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grupo_id) REFERENCES grupos_anuncios(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_grupo_post (grupo_id, post_id),
    INDEX idx_grupos_anuncios_posts_grupo (grupo_id),
    INDEX idx_grupos_anuncios_posts_post (post_id)
);

-- Adicionar campos apenas se não existirem (usando procedimento)
DELIMITER $$

DROP PROCEDURE IF EXISTS AdicionarCamposGruposAnuncios$$

CREATE PROCEDURE AdicionarCamposGruposAnuncios()
BEGIN
    -- Adicionar campo posts_especificos se não existir
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'grupos_anuncios' 
        AND COLUMN_NAME = 'posts_especificos'
    ) THEN
        ALTER TABLE grupos_anuncios 
        ADD COLUMN posts_especificos BOOLEAN DEFAULT FALSE 
        COMMENT 'TRUE = apenas posts selecionados, FALSE = todos os posts';
    END IF;
    
    -- Adicionar campo aparecer_inicio se não existir
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'grupos_anuncios' 
        AND COLUMN_NAME = 'aparecer_inicio'
    ) THEN
        ALTER TABLE grupos_anuncios 
        ADD COLUMN aparecer_inicio BOOLEAN DEFAULT TRUE 
        COMMENT 'TRUE = aparece na página inicial, FALSE = não aparece';
    END IF;
    
    -- Atualizar grupos existentes
    UPDATE grupos_anuncios 
    SET posts_especificos = FALSE, aparecer_inicio = TRUE 
    WHERE posts_especificos IS NULL OR aparecer_inicio IS NULL;
END$$

DELIMITER ;

-- Executar o procedimento
CALL AdicionarCamposGruposAnuncios();

-- Remover o procedimento após uso
DROP PROCEDURE IF EXISTS AdicionarCamposGruposAnuncios; 