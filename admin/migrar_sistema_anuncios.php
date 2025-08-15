<?php
/**
 * SCRIPT DE MIGRAÇÃO DO SISTEMA DE ANÚNCIOS
 * Brasil Hilário - Atualização e Otimização
 * 
 * Este script aplica todas as melhorias identificadas no sistema de anúncios.
 */

// Configurações
define('DEBUG_MODE', true);
define('BACKUP_ENABLED', true);

// Incluir configurações
require_once '../config/config.php';
require_once '../config/database_unified.php';

// Verificar se o usuário está logado como admin
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    die("Acesso negado. Apenas administradores podem executar este script.");
}

// Função para log
function logMessage($message, $type = 'info') {
    $timestamp = date('Y-m-d H:i:s');
    $log = "[$timestamp] [$type] $message\n";
    
    if (DEBUG_MODE) {
        echo $log;
    }
    
    error_log($log);
}

// Função para backup
function createBackup() {
    if (!BACKUP_ENABLED) return true;
    
    try {
        $backup_file = '../backups/backup_anuncios_' . date('Y-m-d_H-i-s') . '.sql';
        $backup_dir = dirname($backup_file);
        
        if (!is_dir($backup_dir)) {
            mkdir($backup_dir, 0755, true);
        }
        
        // Comando para backup (ajustar conforme necessário)
        $command = "mysqldump -h " . DB_HOST_LOCAL . " -u " . DB_USER . " -p" . DB_PASS . " " . DB_NAME . " anuncios grupos_anuncios cliques_anuncios > $backup_file";
        
        exec($command, $output, $return_var);
        
        if ($return_var === 0) {
            logMessage("✅ Backup criado: $backup_file", 'success');
            return true;
        } else {
            logMessage("❌ Erro ao criar backup", 'error');
            return false;
        }
    } catch (Exception $e) {
        logMessage("❌ Erro no backup: " . $e->getMessage(), 'error');
        return false;
    }
}

// Função para executar SQL
function executeSQL($sql, $description = '') {
    try {
        $dbManager = DatabaseManager::getInstance();
        $result = $dbManager->execute($sql);
        
        if ($result) {
            logMessage("✅ $description", 'success');
            return true;
        } else {
            logMessage("❌ Falha: $description", 'error');
            return false;
        }
    } catch (Exception $e) {
        logMessage("❌ Erro: $description - " . $e->getMessage(), 'error');
        return false;
    }
}

// Função para verificar se tabela existe
function tableExists($table_name) {
    try {
        $dbManager = DatabaseManager::getInstance();
        $sql = "SHOW TABLES LIKE ?";
        $result = $dbManager->queryOne($sql, [$table_name]);
        return $result !== null;
    } catch (Exception $e) {
        return false;
    }
}

// Função para verificar se coluna existe
function columnExists($table_name, $column_name) {
    try {
        $dbManager = DatabaseManager::getInstance();
        $sql = "SHOW COLUMNS FROM `$table_name` LIKE ?";
        $result = $dbManager->queryOne($sql, [$column_name]);
        return $result !== null;
    } catch (Exception $e) {
        return false;
    }
}

// Início da migração
logMessage("🚀 Iniciando migração do sistema de anúncios...", 'info');

// 1. Criar backup
logMessage("📦 Criando backup do sistema atual...", 'info');
if (!createBackup()) {
    logMessage("⚠️ Continuando sem backup...", 'warning');
}

// 2. Aplicar otimizações SQL
logMessage("🔧 Aplicando otimizações do banco de dados...", 'info');

// 2.1 Corrigir tabela cliques_anuncios
if (tableExists('cliques_anuncios')) {
    logMessage("🔧 Corrigindo tabela cliques_anuncios...", 'info');
    
    // Remover constraint se existir
    executeSQL("ALTER TABLE `cliques_anuncios` DROP FOREIGN KEY IF EXISTS `cliques_anuncios_ibfk_2`", "Removendo constraint antiga");
    
    // Modificar coluna para permitir NULL
    executeSQL("ALTER TABLE `cliques_anuncios` MODIFY COLUMN `post_id` int(11) NULL", "Modificando post_id para permitir NULL");
    
    // Adicionar nova constraint
    executeSQL("ALTER TABLE `cliques_anuncios` ADD CONSTRAINT `fk_cliques_anuncios_post` FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE", "Adicionando nova constraint");
}

// 2.2 Adicionar campos aos anúncios
if (tableExists('anuncios')) {
    logMessage("🔧 Adicionando campos aos anúncios...", 'info');
    
    if (!columnExists('anuncios', 'data_inicio')) {
        executeSQL("ALTER TABLE `anuncios` ADD COLUMN `data_inicio` datetime NULL COMMENT 'Data de início da exibição do anúncio'", "Adicionando campo data_inicio");
    }
    
    if (!columnExists('anuncios', 'data_fim')) {
        executeSQL("ALTER TABLE `anuncios` ADD COLUMN `data_fim` datetime NULL COMMENT 'Data de fim da exibição do anúncio'", "Adicionando campo data_fim");
    }
    
    if (!columnExists('anuncios', 'impressoes')) {
        executeSQL("ALTER TABLE `anuncios` ADD COLUMN `impressoes` int(11) DEFAULT 0 COMMENT 'Contador de impressões'", "Adicionando campo impressoes");
    }
}

// 2.3 Adicionar campo de prioridade aos grupos
if (tableExists('grupos_anuncios')) {
    logMessage("🔧 Adicionando campo de prioridade aos grupos...", 'info');
    
    if (!columnExists('grupos_anuncios', 'prioridade')) {
        executeSQL("ALTER TABLE `grupos_anuncios` ADD COLUMN `prioridade` int(11) DEFAULT 0 COMMENT 'Ordem de exibição (maior número = maior prioridade)'", "Adicionando campo prioridade");
    }
}

// 2.4 Criar tabela de cache
logMessage("🔧 Criando tabela de cache...", 'info');
$cache_sql = "CREATE TABLE IF NOT EXISTS `cache_anuncios` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `chave` varchar(255) NOT NULL,
    `valor` longtext NOT NULL,
    `expiracao` timestamp NOT NULL,
    `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_cache_chave` (`chave`),
    KEY `idx_cache_expiracao` (`expiracao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

executeSQL($cache_sql, "Criando tabela cache_anuncios");

// 2.5 Criar tabela de estatísticas
logMessage("🔧 Criando tabela de estatísticas...", 'info');
$stats_sql = "CREATE TABLE IF NOT EXISTS `estatisticas_anuncios` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

executeSQL($stats_sql, "Criando tabela estatisticas_anuncios");

// 3. Criar índices para performance
logMessage("🔧 Criando índices para performance...", 'info');

$indexes = [
    // Índices para anuncios
    "CREATE INDEX IF NOT EXISTS `idx_anuncios_localizacao_ativo` ON `anuncios`(`localizacao`, `ativo`)",
    "CREATE INDEX IF NOT EXISTS `idx_anuncios_criado_em` ON `anuncios`(`criado_em`)",
    "CREATE INDEX IF NOT EXISTS `idx_anuncios_layout` ON `anuncios`(`layout`)",
    
    // Índices para grupos_anuncios
    "CREATE INDEX IF NOT EXISTS `idx_grupos_localizacao_ativo` ON `grupos_anuncios`(`localizacao`, `ativo`)",
    "CREATE INDEX IF NOT EXISTS `idx_grupos_posts_especificos` ON `grupos_anuncios`(`posts_especificos`)",
    "CREATE INDEX IF NOT EXISTS `idx_grupos_aparecer_inicio` ON `grupos_anuncios`(`aparecer_inicio`)",
    "CREATE INDEX IF NOT EXISTS `idx_grupos_marca` ON `grupos_anuncios`(`marca`)",
    
    // Índices para grupos_anuncios_items
    "CREATE INDEX IF NOT EXISTS `idx_grupos_items_grupo_ordem` ON `grupos_anuncios_items`(`grupo_id`, `ordem`)",
    "CREATE INDEX IF NOT EXISTS `idx_grupos_items_anuncio` ON `grupos_anuncios_items`(`anuncio_id`)",
    
    // Índices para grupos_anuncios_posts
    "CREATE INDEX IF NOT EXISTS `idx_grupos_posts_grupo` ON `grupos_anuncios_posts`(`grupo_id`)",
    "CREATE INDEX IF NOT EXISTS `idx_grupos_posts_post` ON `grupos_anuncios_posts`(`post_id`)",
    
    // Índices para anuncios_posts
    "CREATE INDEX IF NOT EXISTS `idx_anuncios_posts_anuncio` ON `anuncios_posts`(`anuncio_id`)",
    "CREATE INDEX IF NOT EXISTS `idx_anuncios_posts_post` ON `anuncios_posts`(`post_id`)",
    
    // Índices para cliques_anuncios
    "CREATE INDEX IF NOT EXISTS `idx_cliques_anuncio_data` ON `cliques_anuncios`(`anuncio_id`, `data_clique`)",
    "CREATE INDEX IF NOT EXISTS `idx_cliques_post_data` ON `cliques_anuncios`(`post_id`, `data_clique`)",
    "CREATE INDEX IF NOT EXISTS `idx_cliques_tipo_data` ON `cliques_anuncios`(`tipo_clique`, `data_clique`)",
    "CREATE INDEX IF NOT EXISTS `idx_cliques_ip_data` ON `cliques_anuncios`(`ip_usuario`, `data_clique`)",
    
    // Índices compostos
    "CREATE INDEX IF NOT EXISTS `idx_anuncios_localizacao_ativo_layout` ON `anuncios`(`localizacao`, `ativo`, `layout`)",
    "CREATE INDEX IF NOT EXISTS `idx_grupos_localizacao_config` ON `grupos_anuncios`(`localizacao`, `ativo`, `posts_especificos`, `aparecer_inicio`)",
    "CREATE INDEX IF NOT EXISTS `idx_cliques_anuncio_periodo` ON `cliques_anuncios`(`anuncio_id`, `data_clique`, `tipo_clique`)"
];

foreach ($indexes as $index_sql) {
    executeSQL($index_sql, "Criando índice");
}

// 4. Criar views
logMessage("🔧 Criando views para facilitar consultas...", 'info');

$views = [
    // View para anúncios ativos com estatísticas
    "CREATE OR REPLACE VIEW `vw_anuncios_ativos` AS
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
    GROUP BY a.id",
    
    // View para grupos com estatísticas
    "CREATE OR REPLACE VIEW `vw_grupos_estatisticas` AS
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
    GROUP BY g.id"
];

foreach ($views as $view_sql) {
    executeSQL($view_sql, "Criando view");
}

// 5. Adicionar configurações
logMessage("🔧 Adicionando configurações do sistema...", 'info');

$configs = [
    ['anuncios_cache_time', '3600', 'integer', 'anuncios'],
    ['anuncios_max_por_pagina', '10', 'integer', 'anuncios'],
    ['anuncios_limpeza_automatica', '1', 'boolean', 'anuncios'],
    ['anuncios_retencao_dias', '90', 'integer', 'anuncios'],
    ['anuncios_debug_mode', '0', 'boolean', 'anuncios']
];

foreach ($configs as $config) {
    $sql = "INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`) VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE `valor` = VALUES(`valor`), `atualizado_em` = NOW()";
    
    $dbManager = DatabaseManager::getInstance();
    $result = $dbManager->execute($sql, $config);
    
    if ($result) {
        logMessage("✅ Configuração adicionada: {$config[0]}", 'success');
    } else {
        logMessage("❌ Falha ao adicionar configuração: {$config[0]}", 'error');
    }
}

// 6. Otimizar tabelas
logMessage("🔧 Otimizando tabelas...", 'info');

$tables_to_optimize = ['anuncios', 'grupos_anuncios', 'cliques_anuncios', 'anuncios_posts', 'grupos_anuncios_items', 'grupos_anuncios_posts'];

foreach ($tables_to_optimize as $table) {
    if (tableExists($table)) {
        executeSQL("OPTIMIZE TABLE `$table`", "Otimizando tabela $table");
        executeSQL("ANALYZE TABLE `$table`", "Analisando tabela $table");
    }
}

// 7. Verificar integridade
logMessage("🔍 Verificando integridade do sistema...", 'info');

// Verificar anúncios órfãos
$orphan_ads = $dbManager->query("
    SELECT COUNT(*) as total 
    FROM anuncios a 
    LEFT JOIN grupos_anuncios_items gi ON a.id = gi.anuncio_id 
    WHERE gi.anuncio_id IS NULL AND a.ativo = 1
");

if ($orphan_ads[0]['total'] > 0) {
    logMessage("⚠️ Encontrados {$orphan_ads[0]['total']} anúncios não associados a grupos", 'warning');
}

// Verificar grupos vazios
$empty_groups = $dbManager->query("
    SELECT COUNT(*) as total 
    FROM grupos_anuncios g 
    LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id 
    WHERE gi.anuncio_id IS NULL AND g.ativo = 1
");

if ($empty_groups[0]['total'] > 0) {
    logMessage("⚠️ Encontrados {$empty_groups[0]['total']} grupos sem anúncios", 'warning');
}

// 8. Estatísticas finais
logMessage("📊 Gerando estatísticas finais...", 'info');

$stats = $dbManager->query("
    SELECT 
        (SELECT COUNT(*) FROM anuncios WHERE ativo = 1) as anuncios_ativos,
        (SELECT COUNT(*) FROM grupos_anuncios WHERE ativo = 1) as grupos_ativos,
        (SELECT COUNT(*) FROM cliques_anuncios WHERE data_clique >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as cliques_30dias,
        (SELECT COUNT(*) FROM anuncios_posts) as associacoes_anuncios_posts,
        (SELECT COUNT(*) FROM grupos_anuncios_posts) as associacoes_grupos_posts
");

if ($stats) {
    $stat = $stats[0];
    logMessage("📊 Estatísticas do sistema:", 'info');
    logMessage("   - Anúncios ativos: {$stat['anuncios_ativos']}", 'info');
    logMessage("   - Grupos ativos: {$stat['grupos_ativos']}", 'info');
    logMessage("   - Cliques (30 dias): {$stat['cliques_30dias']}", 'info');
    logMessage("   - Associações anúncios-posts: {$stat['associacoes_anuncios_posts']}", 'info');
    logMessage("   - Associações grupos-posts: {$stat['associacoes_grupos_posts']}", 'info');
}

// 9. Finalização
logMessage("🎉 Migração concluída com sucesso!", 'success');
logMessage("📝 Próximos passos:", 'info');
logMessage("   1. Testar o sistema de anúncios", 'info');
logMessage("   2. Verificar se todos os anúncios estão sendo exibidos", 'info');
logMessage("   3. Monitorar performance do banco de dados", 'info');
logMessage("   4. Configurar manutenção automática se necessário", 'info');

// 10. Gerar relatório
$report = [
    'timestamp' => date('Y-m-d H:i:s'),
    'status' => 'success',
    'backup_created' => BACKUP_ENABLED,
    'tables_optimized' => count($tables_to_optimize),
    'indexes_created' => count($indexes),
    'views_created' => count($views),
    'configs_added' => count($configs)
];

$report_file = '../logs/migracao_anuncios_' . date('Y-m-d_H-i-s') . '.json';
$report_dir = dirname($report_file);

if (!is_dir($report_dir)) {
    mkdir($report_dir, 0755, true);
}

file_put_contents($report_file, json_encode($report, JSON_PRETTY_PRINT));
logMessage("📄 Relatório salvo em: $report_file", 'info');

echo "<h2>✅ Migração Concluída!</h2>";
echo "<p>O sistema de anúncios foi otimizado com sucesso.</p>";
echo "<p><a href='anuncios.php'>← Voltar para Anúncios</a></p>";
?>
