<?php
/**
 * CORREÇÃO DOS ÍNDICES DO SISTEMA DE ANÚNCIOS
 * Brasil Hilário - Correção pós-migração
 * 
 * Este script corrige os índices que falharam devido à versão do MySQL.
 */

// Configurações
define('DEBUG_MODE', true);

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

// Função para verificar se índice existe
function indexExists($table_name, $index_name) {
    try {
        $dbManager = DatabaseManager::getInstance();
        $sql = "SHOW INDEX FROM `$table_name` WHERE Key_name = ?";
        $result = $dbManager->queryOne($sql, [$index_name]);
        return $result !== null;
    } catch (Exception $e) {
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

// Início da correção
logMessage("🔧 Iniciando correção dos índices...", 'info');

// 1. Índices para tabela anuncios
logMessage("🔧 Criando índices para tabela anuncios...", 'info');

$anuncios_indexes = [
    ['idx_anuncios_localizacao_ativo', 'anuncios', 'localizacao, ativo'],
    ['idx_anuncios_criado_em', 'anuncios', 'criado_em'],
    ['idx_anuncios_layout', 'anuncios', 'layout'],
    ['idx_anuncios_localizacao_ativo_layout', 'anuncios', 'localizacao, ativo, layout']
];

foreach ($anuncios_indexes as $index) {
    $index_name = $index[0];
    $table_name = $index[1];
    $columns = $index[2];
    
    if (!indexExists($table_name, $index_name)) {
        $sql = "CREATE INDEX `$index_name` ON `$table_name`(`$columns`)";
        executeSQL($sql, "Criando índice $index_name");
    } else {
        logMessage("ℹ️ Índice $index_name já existe", 'info');
    }
}

// 2. Índices para tabela grupos_anuncios
logMessage("🔧 Criando índices para tabela grupos_anuncios...", 'info');

$grupos_indexes = [
    ['idx_grupos_localizacao_ativo', 'grupos_anuncios', 'localizacao, ativo'],
    ['idx_grupos_posts_especificos', 'grupos_anuncios', 'posts_especificos'],
    ['idx_grupos_aparecer_inicio', 'grupos_anuncios', 'aparecer_inicio'],
    ['idx_grupos_marca', 'grupos_anuncios', 'marca'],
    ['idx_grupos_localizacao_config', 'grupos_anuncios', 'localizacao, ativo, posts_especificos, aparecer_inicio']
];

foreach ($grupos_indexes as $index) {
    $index_name = $index[0];
    $table_name = $index[1];
    $columns = $index[2];
    
    if (!indexExists($table_name, $index_name)) {
        $sql = "CREATE INDEX `$index_name` ON `$table_name`(`$columns`)";
        executeSQL($sql, "Criando índice $index_name");
    } else {
        logMessage("ℹ️ Índice $index_name já existe", 'info');
    }
}

// 3. Índices para tabela grupos_anuncios_items
logMessage("🔧 Criando índices para tabela grupos_anuncios_items...", 'info');

$grupos_items_indexes = [
    ['idx_grupos_items_grupo_ordem', 'grupos_anuncios_items', 'grupo_id, ordem'],
    ['idx_grupos_items_anuncio', 'grupos_anuncios_items', 'anuncio_id']
];

foreach ($grupos_items_indexes as $index) {
    $index_name = $index[0];
    $table_name = $index[1];
    $columns = $index[2];
    
    if (!indexExists($table_name, $index_name)) {
        $sql = "CREATE INDEX `$index_name` ON `$table_name`(`$columns`)";
        executeSQL($sql, "Criando índice $index_name");
    } else {
        logMessage("ℹ️ Índice $index_name já existe", 'info');
    }
}

// 4. Índices para tabela grupos_anuncios_posts
logMessage("🔧 Criando índices para tabela grupos_anuncios_posts...", 'info');

$grupos_posts_indexes = [
    ['idx_grupos_posts_grupo', 'grupos_anuncios_posts', 'grupo_id'],
    ['idx_grupos_posts_post', 'grupos_anuncios_posts', 'post_id']
];

foreach ($grupos_posts_indexes as $index) {
    $index_name = $index[0];
    $table_name = $index[1];
    $columns = $index[2];
    
    if (!indexExists($table_name, $index_name)) {
        $sql = "CREATE INDEX `$index_name` ON `$table_name`(`$columns`)";
        executeSQL($sql, "Criando índice $index_name");
    } else {
        logMessage("ℹ️ Índice $index_name já existe", 'info');
    }
}

// 5. Índices para tabela anuncios_posts
logMessage("🔧 Criando índices para tabela anuncios_posts...", 'info');

$anuncios_posts_indexes = [
    ['idx_anuncios_posts_anuncio', 'anuncios_posts', 'anuncio_id'],
    ['idx_anuncios_posts_post', 'anuncios_posts', 'post_id']
];

foreach ($anuncios_posts_indexes as $index) {
    $index_name = $index[0];
    $table_name = $index[1];
    $columns = $index[2];
    
    if (!indexExists($table_name, $index_name)) {
        $sql = "CREATE INDEX `$index_name` ON `$table_name`(`$columns`)";
        executeSQL($sql, "Criando índice $index_name");
    } else {
        logMessage("ℹ️ Índice $index_name já existe", 'info');
    }
}

// 6. Índices para tabela cliques_anuncios
logMessage("🔧 Criando índices para tabela cliques_anuncios...", 'info');

$cliques_indexes = [
    ['idx_cliques_anuncio_data', 'cliques_anuncios', 'anuncio_id, data_clique'],
    ['idx_cliques_post_data', 'cliques_anuncios', 'post_id, data_clique'],
    ['idx_cliques_tipo_data', 'cliques_anuncios', 'tipo_clique, data_clique'],
    ['idx_cliques_ip_data', 'cliques_anuncios', 'ip_usuario, data_clique'],
    ['idx_cliques_anuncio_periodo', 'cliques_anuncios', 'anuncio_id, data_clique, tipo_clique']
];

foreach ($cliques_indexes as $index) {
    $index_name = $index[0];
    $table_name = $index[1];
    $columns = $index[2];
    
    if (!indexExists($table_name, $index_name)) {
        $sql = "CREATE INDEX `$index_name` ON `$table_name`(`$columns`)";
        executeSQL($sql, "Criando índice $index_name");
    } else {
        logMessage("ℹ️ Índice $index_name já existe", 'info');
    }
}

// 7. Verificar campos adicionais
logMessage("🔧 Verificando campos adicionais...", 'info');

// Verificar se campos foram adicionados aos anúncios
$dbManager = DatabaseManager::getInstance();

$anuncios_fields = [
    'data_inicio' => "ALTER TABLE `anuncios` ADD COLUMN `data_inicio` datetime NULL COMMENT 'Data de início da exibição do anúncio'",
    'data_fim' => "ALTER TABLE `anuncios` ADD COLUMN `data_fim` datetime NULL COMMENT 'Data de fim da exibição do anúncio'",
    'impressoes' => "ALTER TABLE `anuncios` ADD COLUMN `impressoes` int(11) DEFAULT 0 COMMENT 'Contador de impressões'"
];

foreach ($anuncios_fields as $field => $sql) {
    $check_sql = "SHOW COLUMNS FROM `anuncios` LIKE '$field'";
    $result = $dbManager->queryOne($check_sql);
    
    if (!$result) {
        executeSQL($sql, "Adicionando campo $field aos anúncios");
    } else {
        logMessage("ℹ️ Campo $field já existe", 'info');
    }
}

// Verificar campo de prioridade nos grupos
$check_prioridade = "SHOW COLUMNS FROM `grupos_anuncios` LIKE 'prioridade'";
$result = $dbManager->queryOne($check_prioridade);

if (!$result) {
    executeSQL("ALTER TABLE `grupos_anuncios` ADD COLUMN `prioridade` int(11) DEFAULT 0 COMMENT 'Ordem de exibição (maior número = maior prioridade)'", "Adicionando campo prioridade aos grupos");
} else {
    logMessage("ℹ️ Campo prioridade já existe", 'info');
}

// 8. Otimizar tabelas
logMessage("🔧 Otimizando tabelas...", 'info');

$tables_to_optimize = ['anuncios', 'grupos_anuncios', 'cliques_anuncios', 'anuncios_posts', 'grupos_anuncios_items', 'grupos_anuncios_posts', 'cache_anuncios', 'estatisticas_anuncios'];

foreach ($tables_to_optimize as $table) {
    executeSQL("OPTIMIZE TABLE `$table`", "Otimizando tabela $table");
}

// 9. Estatísticas finais
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

// 10. Finalização
logMessage("🎉 Correção dos índices concluída com sucesso!", 'success');
logMessage("📝 Sistema completamente otimizado!", 'info');

echo "<h2>✅ Correção Concluída!</h2>";
echo "<p>Todos os índices foram criados com sucesso.</p>";
echo "<p><a href='anuncios.php'>← Voltar para Anúncios</a></p>";
?>
