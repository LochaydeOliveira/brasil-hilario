-- =====================================================
-- OTIMIZAÇÃO COMPLETA DO SISTEMA DE ANÚNCIOS NATIVOS
-- Brasil Hilário - Melhorias e Correções
-- =====================================================

-- Configurações iniciais
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- =====================================================
-- 1. CORREÇÃO DA TABELA cliques_anuncios
-- =====================================================

-- Corrigir a constraint de FOREIGN KEY para permitir NULL em post_id
ALTER TABLE `cliques_anuncios` 
DROP FOREIGN KEY IF EXISTS `cliques_anuncios_ibfk_2`;

-- Modificar a coluna post_id para permitir NULL
ALTER TABLE `cliques_anuncios` 
MODIFY COLUMN `post_id` int(11) NULL;

-- Adicionar nova constraint que permite NULL
ALTER TABLE `cliques_anuncios` 
ADD CONSTRAINT `fk_cliques_anuncios_post` 
FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE;

-- =====================================================
-- 2. OTIMIZAÇÃO DE ÍNDICES PARA PERFORMANCE
-- =====================================================

-- Índices para tabela anuncios
CREATE INDEX IF NOT EXISTS `idx_anuncios_localizacao_ativo` ON `anuncios`(`localizacao`, `ativo`);
CREATE INDEX IF NOT EXISTS `idx_anuncios_criado_em` ON `anuncios`(`criado_em`);
CREATE INDEX IF NOT EXISTS `idx_anuncios_layout` ON `anuncios`(`layout`);

-- Índices para tabela grupos_anuncios
CREATE INDEX IF NOT EXISTS `idx_grupos_localizacao_ativo` ON `grupos_anuncios`(`localizacao`, `ativo`);
CREATE INDEX IF NOT EXISTS `idx_grupos_posts_especificos` ON `grupos_anuncios`(`posts_especificos`);
CREATE INDEX IF NOT EXISTS `idx_grupos_aparecer_inicio` ON `grupos_anuncios`(`aparecer_inicio`);
CREATE INDEX IF NOT EXISTS `idx_grupos_marca` ON `grupos_anuncios`(`marca`);

-- Índices para tabela grupos_anuncios_items
CREATE INDEX IF NOT EXISTS `idx_grupos_items_grupo_ordem` ON `grupos_anuncios_items`(`grupo_id`, `ordem`);
CREATE INDEX IF NOT EXISTS `idx_grupos_items_anuncio` ON `grupos_anuncios_items`(`anuncio_id`);

-- Índices para tabela grupos_anuncios_posts
CREATE INDEX IF NOT EXISTS `idx_grupos_posts_grupo` ON `grupos_anuncios_posts`(`grupo_id`);
CREATE INDEX IF NOT EXISTS `idx_grupos_posts_post` ON `grupos_anuncios_posts`(`post_id`);

-- Índices para tabela anuncios_posts
CREATE INDEX IF NOT EXISTS `idx_anuncios_posts_anuncio` ON `anuncios_posts`(`anuncio_id`);
CREATE INDEX IF NOT EXISTS `idx_anuncios_posts_post` ON `anuncios_posts`(`post_id`);

-- Índices para tabela cliques_anuncios
CREATE INDEX IF NOT EXISTS `idx_cliques_anuncio_data` ON `cliques_anuncios`(`anuncio_id`, `data_clique`);
CREATE INDEX IF NOT EXISTS `idx_cliques_post_data` ON `cliques_anuncios`(`post_id`, `data_clique`);
CREATE INDEX IF NOT EXISTS `idx_cliques_tipo_data` ON `cliques_anuncios`(`tipo_clique`, `data_clique`);
CREATE INDEX IF NOT EXISTS `idx_cliques_ip_data` ON `cliques_anuncios`(`ip_usuario`, `data_clique`);

-- =====================================================
-- 3. ADIÇÃO DE CAMPOS PARA MELHOR CONTROLE
-- =====================================================

-- Adicionar campo de prioridade aos grupos
ALTER TABLE `grupos_anuncios` 
ADD COLUMN IF NOT EXISTS `prioridade` int(11) DEFAULT 0 
COMMENT 'Ordem de exibição (maior número = maior prioridade)';

-- Adicionar campo de data de início e fim aos anúncios
ALTER TABLE `anuncios` 
ADD COLUMN IF NOT EXISTS `data_inicio` datetime NULL 
COMMENT 'Data de início da exibição do anúncio';

ALTER TABLE `anuncios` 
ADD COLUMN IF NOT EXISTS `data_fim` datetime NULL 
COMMENT 'Data de fim da exibição do anúncio';

-- Adicionar campo de impressões aos anúncios
ALTER TABLE `anuncios` 
ADD COLUMN IF NOT EXISTS `impressoes` int(11) DEFAULT 0 
COMMENT 'Contador de impressões';

-- =====================================================
-- 4. TABELA DE CACHE PARA PERFORMANCE
-- =====================================================

CREATE TABLE IF NOT EXISTS `cache_anuncios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chave` varchar(255) NOT NULL,
  `valor` longtext NOT NULL,
  `expiracao` timestamp NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_cache_chave` (`chave`),
  KEY `idx_cache_expiracao` (`expiracao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. TABELA DE ESTATÍSTICAS AGREGADAS
-- =====================================================

CREATE TABLE IF NOT EXISTS `estatisticas_anuncios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anuncio_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `impressoes` int(11) DEFAULT 0,
  `cliques` int(11) DEFAULT 0,
  `cliques_imagem` int(11) DEFAULT 0,
  `cliques_titulo` int(11) DEFAULT 0,
  `cliques_cta` int(11) DEFAULT 0,
  `taxa_clique` decimal(5,4) DEFAULT 0.0000,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_estatistica_anuncio_data` (`anuncio_id`, `data`),
  KEY `idx_estatistica_data` (`data`),
  KEY `idx_estatistica_anuncio` (`anuncio_id`),
  CONSTRAINT `fk_estatisticas_anuncio` FOREIGN KEY (`anuncio_id`) REFERENCES `anuncios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. PROCEDIMENTO PARA LIMPEZA AUTOMÁTICA
-- =====================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS `LimparCacheAnuncios`$$

CREATE PROCEDURE `LimparCacheAnuncios`()
BEGIN
    -- Limpar cache expirado
    DELETE FROM `cache_anuncios` WHERE `expiracao` < NOW();
    
    -- Limpar cliques antigos (manter apenas últimos 90 dias)
    DELETE FROM `cliques_anuncios` WHERE `data_clique` < DATE_SUB(NOW(), INTERVAL 90 DAY);
    
    -- Otimizar tabelas
    OPTIMIZE TABLE `cache_anuncios`;
    OPTIMIZE TABLE `cliques_anuncios`;
END$$

DELIMITER ;

-- =====================================================
-- 7. PROCEDIMENTO PARA ATUALIZAR ESTATÍSTICAS
-- =====================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS `AtualizarEstatisticasAnuncios`$$

CREATE PROCEDURE `AtualizarEstatisticasAnuncios`(IN data_estatistica DATE)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE anuncio_id_var INT;
    DECLARE impressoes_var, cliques_var, cliques_imagem_var, cliques_titulo_var, cliques_cta_var INT;
    DECLARE taxa_clique_var DECIMAL(5,4);
    
    DECLARE anuncios_cursor CURSOR FOR 
        SELECT DISTINCT a.id 
        FROM anuncios a 
        WHERE a.ativo = 1;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN anuncios_cursor;
    
    read_loop: LOOP
        FETCH anuncios_cursor INTO anuncio_id_var;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Calcular estatísticas do dia
        SELECT 
            COUNT(DISTINCT ca.id) as cliques,
            SUM(CASE WHEN ca.tipo_clique = 'imagem' THEN 1 ELSE 0 END) as cliques_imagem,
            SUM(CASE WHEN ca.tipo_clique = 'titulo' THEN 1 ELSE 0 END) as cliques_titulo,
            SUM(CASE WHEN ca.tipo_clique = 'cta' THEN 1 ELSE 0 END) as cliques_cta
        INTO cliques_var, cliques_imagem_var, cliques_titulo_var, cliques_cta_var
        FROM cliques_anuncios ca
        WHERE ca.anuncio_id = anuncio_id_var 
        AND DATE(ca.data_clique) = data_estatistica;
        
        -- Calcular taxa de clique (assumindo impressões baseadas em visualizações)
        SET taxa_clique_var = CASE 
            WHEN cliques_var > 0 THEN cliques_var / 1000.0 -- Assumindo 1000 impressões por clique
            ELSE 0.0000 
        END;
        
        -- Inserir ou atualizar estatísticas
        INSERT INTO estatisticas_anuncios 
            (anuncio_id, data, impressoes, cliques, cliques_imagem, cliques_titulo, cliques_cta, taxa_clique)
        VALUES 
            (anuncio_id_var, data_estatistica, 1000, cliques_var, cliques_imagem_var, cliques_titulo_var, cliques_cta_var, taxa_clique_var)
        ON DUPLICATE KEY UPDATE
            impressoes = VALUES(impressoes),
            cliques = VALUES(cliques),
            cliques_imagem = VALUES(cliques_imagem),
            cliques_titulo = VALUES(cliques_titulo),
            cliques_cta = VALUES(cliques_cta),
            taxa_clique = VALUES(taxa_clique),
            atualizado_em = NOW();
            
    END LOOP;
    
    CLOSE anuncios_cursor;
END$$

DELIMITER ;

-- =====================================================
-- 8. VIEWS PARA FACILITAR CONSULTAS
-- =====================================================

-- View para anúncios ativos com estatísticas
CREATE OR REPLACE VIEW `vw_anuncios_ativos` AS
SELECT 
    a.*,
    COUNT(DISTINCT ca.id) as total_cliques,
    COUNT(DISTINCT ap.post_id) as total_posts_associados,
    COALESCE(SUM(CASE WHEN ca.tipo_clique = 'imagem' THEN 1 ELSE 0 END), 0) as cliques_imagem,
    COALESCE(SUM(CASE WHEN ca.tipo_clique = 'titulo' THEN 1 ELSE 0 END), 0) as cliques_titulo,
    COALESCE(SUM(CASE WHEN ca.tipo_clique = 'cta' THEN 1 ELSE 0 END), 0) as cliques_cta
FROM anuncios a
LEFT JOIN cliques_anuncios ca ON a.id = ca.anuncio_id
LEFT JOIN anuncios_posts ap ON a.id = ap.anuncio_id
WHERE a.ativo = 1
GROUP BY a.id;

-- View para grupos com estatísticas
CREATE OR REPLACE VIEW `vw_grupos_estatisticas` AS
SELECT 
    g.*,
    COUNT(DISTINCT gi.anuncio_id) as total_anuncios,
    COUNT(DISTINCT gap.post_id) as total_posts_especificos,
    COALESCE(SUM(ca.total_cliques), 0) as total_cliques_grupo
FROM grupos_anuncios g
LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
LEFT JOIN grupos_anuncios_posts gap ON g.id = gap.grupo_id
LEFT JOIN vw_anuncios_ativos ca ON gi.anuncio_id = ca.id
WHERE g.ativo = 1
GROUP BY g.id;

-- =====================================================
-- 9. TRIGGERS PARA MANUTENÇÃO AUTOMÁTICA
-- =====================================================

-- Trigger para atualizar impressões quando anúncio é visualizado
DELIMITER $$

DROP TRIGGER IF EXISTS `tr_anuncio_impressao`$$

CREATE TRIGGER `tr_anuncio_impressao` 
AFTER INSERT ON `cliques_anuncios`
FOR EACH ROW
BEGIN
    -- Incrementar impressões do anúncio
    UPDATE anuncios 
    SET impressoes = impressoes + 1 
    WHERE id = NEW.anuncio_id;
END$$

DELIMITER ;

-- =====================================================
-- 10. CONFIGURAÇÕES DE PERFORMANCE
-- =====================================================

-- Configurar variáveis de sessão para melhor performance
SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';
SET SESSION innodb_lock_wait_timeout = 50;
SET SESSION innodb_flush_log_at_trx_commit = 2;

-- =====================================================
-- 11. DADOS DE EXEMPLO OTIMIZADOS
-- =====================================================

-- Inserir configurações de cache
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`) VALUES
('anuncios_cache_time', '3600', 'integer', 'anuncios'),
('anuncios_max_por_pagina', '10', 'integer', 'anuncios'),
('anuncios_limpeza_automatica', '1', 'boolean', 'anuncios'),
('anuncios_retencao_dias', '90', 'integer', 'anuncios')
ON DUPLICATE KEY UPDATE 
    `valor` = VALUES(`valor`),
    `atualizado_em` = NOW();

-- =====================================================
-- 12. ÍNDICES COMPOSTOS PARA CONSULTAS COMPLEXAS
-- =====================================================

-- Índice composto para consultas de anúncios por localização e status
CREATE INDEX IF NOT EXISTS `idx_anuncios_localizacao_ativo_layout` 
ON `anuncios`(`localizacao`, `ativo`, `layout`);

-- Índice composto para consultas de grupos por localização e configurações
CREATE INDEX IF NOT EXISTS `idx_grupos_localizacao_config` 
ON `grupos_anuncios`(`localizacao`, `ativo`, `posts_especificos`, `aparecer_inicio`);

-- Índice composto para consultas de cliques por período
CREATE INDEX IF NOT EXISTS `idx_cliques_anuncio_periodo` 
ON `cliques_anuncios`(`anuncio_id`, `data_clique`, `tipo_clique`);

-- =====================================================
-- 13. PROCEDIMENTO DE MANUTENÇÃO PERIÓDICA
-- =====================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS `ManutencaoSistemaAnuncios`$$

CREATE PROCEDURE `ManutencaoSistemaAnuncios`()
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Limpar cache expirado
    CALL LimparCacheAnuncios();
    
    -- Atualizar estatísticas do dia anterior
    CALL AtualizarEstatisticasAnuncios(DATE_SUB(CURDATE(), INTERVAL 1 DAY));
    
    -- Otimizar tabelas
    OPTIMIZE TABLE anuncios;
    OPTIMIZE TABLE grupos_anuncios;
    OPTIMIZE TABLE cliques_anuncios;
    OPTIMIZE TABLE estatisticas_anuncios;
    
    -- Analisar tabelas para otimizar consultas
    ANALYZE TABLE anuncios;
    ANALYZE TABLE grupos_anuncios;
    ANALYZE TABLE cliques_anuncios;
    ANALYZE TABLE estatisticas_anuncios;
    
    COMMIT;
END$$

DELIMITER ;

-- =====================================================
-- 14. EVENTO AGENDADO PARA MANUTENÇÃO AUTOMÁTICA
-- =====================================================

-- Criar evento para manutenção diária às 3h da manhã
CREATE EVENT IF NOT EXISTS `evt_manutencao_anuncios_diaria`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP + INTERVAL 3 HOUR
DO
    CALL ManutencaoSistemaAnuncios();

-- =====================================================
-- 15. FINALIZAÇÃO
-- =====================================================

-- Verificar se todas as otimizações foram aplicadas
SELECT 'OTIMIZAÇÃO COMPLETA' as status, NOW() as aplicado_em;

-- Mostrar resumo das melhorias
SELECT 
    'Índices criados' as melhoria,
    COUNT(*) as quantidade
FROM information_schema.statistics 
WHERE table_schema = DATABASE() 
AND table_name IN ('anuncios', 'grupos_anuncios', 'cliques_anuncios', 'estatisticas_anuncios')
AND index_name LIKE 'idx_%'

UNION ALL

SELECT 
    'Tabelas otimizadas' as melhoria,
    COUNT(*) as quantidade
FROM information_schema.tables 
WHERE table_schema = DATABASE() 
AND table_name IN ('anuncios', 'grupos_anuncios', 'cliques_anuncios', 'estatisticas_anuncios', 'cache_anuncios');

COMMIT;
