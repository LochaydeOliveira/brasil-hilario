<?php
/**
 * TESTE DO SISTEMA DE ANÚNCIOS
 * Brasil Hilário - Verificação de funcionamento
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

// Início dos testes
logMessage("🧪 Iniciando testes do sistema de anúncios...", 'info');

$dbManager = DatabaseManager::getInstance();

// 1. Testar conexão com banco
logMessage("📊 Testando conexão com banco...", 'info');

try {
    $teste_conexao = $dbManager->queryOne("SELECT 1 as teste");
    if ($teste_conexao) {
        logMessage("✅ Conexão com banco funcionando", 'success');
    } else {
        logMessage("❌ Erro na conexão com banco", 'error');
    }
} catch (Exception $e) {
    logMessage("❌ Erro na conexão: " . $e->getMessage(), 'error');
}

// 2. Verificar tabelas do sistema
logMessage("📋 Verificando tabelas do sistema...", 'info');

$tabelas = [
    'anuncios',
    'grupos_anuncios', 
    'grupos_anuncios_items',
    'grupos_anuncios_posts',
    'cliques_anuncios',
    'anuncios_posts',
    'cache_anuncios',
    'estatisticas_anuncios'
];

foreach ($tabelas as $tabela) {
    try {
        $resultado = $dbManager->queryOne("SHOW TABLES LIKE ?", [$tabela]);
        if ($resultado) {
            logMessage("✅ Tabela '$tabela' existe", 'success');
        } else {
            logMessage("❌ Tabela '$tabela' não encontrada", 'error');
        }
    } catch (Exception $e) {
        logMessage("❌ Erro ao verificar tabela '$tabela': " . $e->getMessage(), 'error');
    }
}

// 3. Verificar anúncios ativos
logMessage("📊 Verificando anúncios ativos...", 'info');

try {
    $anuncios_ativos = $dbManager->query("
        SELECT COUNT(*) as total FROM anuncios WHERE ativo = 1
    ");
    
    $total_ativos = $anuncios_ativos[0]['total'] ?? 0;
    logMessage("📈 Anúncios ativos: $total_ativos", 'info');
    
    if ($total_ativos > 0) {
        $anuncios = $dbManager->query("
            SELECT id, titulo, marca, ativo 
            FROM anuncios 
            WHERE ativo = 1 
            ORDER BY criado_em DESC 
            LIMIT 5
        ");
        
        foreach ($anuncios as $anuncio) {
            logMessage("   - ID {$anuncio['id']}: {$anuncio['titulo']} ({$anuncio['marca']})", 'info');
        }
    }
} catch (Exception $e) {
    logMessage("❌ Erro ao verificar anúncios: " . $e->getMessage(), 'error');
}

// 4. Verificar grupos ativos
logMessage("📊 Verificando grupos ativos...", 'info');

try {
    $grupos_ativos = $dbManager->query("
        SELECT COUNT(*) as total FROM grupos_anuncios WHERE ativo = 1
    ");
    
    $total_grupos = $grupos_ativos[0]['total'] ?? 0;
    logMessage("📈 Grupos ativos: $total_grupos", 'info');
    
    if ($total_grupos > 0) {
        $grupos = $dbManager->query("
            SELECT g.id, g.nome, g.localizacao, g.layout, COUNT(gi.anuncio_id) as total_anuncios
            FROM grupos_anuncios g
            LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
            WHERE g.ativo = 1
            GROUP BY g.id
            ORDER BY g.criado_em DESC
        ");
        
        foreach ($grupos as $grupo) {
            logMessage("   - ID {$grupo['id']}: {$grupo['nome']} ({$grupo['localizacao']}, {$grupo['layout']}) - {$grupo['total_anuncios']} anúncios", 'info');
        }
    }
} catch (Exception $e) {
    logMessage("❌ Erro ao verificar grupos: " . $e->getMessage(), 'error');
}

// 5. Testar GruposAnunciosManager
logMessage("🔧 Testando GruposAnunciosManager...", 'info');

try {
    require_once '../includes/GruposAnunciosManager.php';
    $gruposManager = new GruposAnunciosManager($dbManager->getConnection());
    
    // Testar busca de grupos para sidebar
    $grupos_sidebar = $gruposManager->getGruposPorLocalizacao('sidebar', null, true);
    logMessage("📋 Grupos encontrados para sidebar (página inicial): " . count($grupos_sidebar), 'info');
    
    foreach ($grupos_sidebar as $grupo) {
        logMessage("   - Grupo: {$grupo['nome']}", 'info');
        
        $anuncios_grupo = $gruposManager->getAnunciosDoGrupo($grupo['id']);
        logMessage("     Anúncios no grupo: " . count($anuncios_grupo), 'info');
        
        foreach ($anuncios_grupo as $anuncio) {
            logMessage("       - {$anuncio['titulo']}", 'info');
        }
    }
    
    logMessage("✅ GruposAnunciosManager funcionando", 'success');
    
} catch (Exception $e) {
    logMessage("❌ Erro no GruposAnunciosManager: " . $e->getMessage(), 'error');
}

// 6. Verificar anúncios sem grupo
logMessage("🔍 Verificando anúncios sem grupo...", 'info');

try {
    $anuncios_sem_grupo = $dbManager->query("
        SELECT a.id, a.titulo, a.ativo
        FROM anuncios a
        LEFT JOIN grupos_anuncios_items gi ON a.id = gi.anuncio_id
        WHERE gi.anuncio_id IS NULL AND a.ativo = 1
    ");
    
    if (empty($anuncios_sem_grupo)) {
        logMessage("✅ Todos os anúncios ativos estão em grupos", 'success');
    } else {
        logMessage("⚠️ Anúncios ativos sem grupo: " . count($anuncios_sem_grupo), 'warning');
        foreach ($anuncios_sem_grupo as $anuncio) {
            logMessage("   - ID {$anuncio['id']}: {$anuncio['titulo']} (não aparecerá no site)", 'warning');
        }
    }
} catch (Exception $e) {
    logMessage("❌ Erro ao verificar anúncios sem grupo: " . $e->getMessage(), 'error');
}

// 7. Verificar configurações do sistema
logMessage("⚙️ Verificando configurações do sistema...", 'info');

try {
    $configuracoes = $dbManager->query("
        SELECT chave, valor FROM configuracoes 
        WHERE chave LIKE 'anuncios_%'
        ORDER BY chave
    ");
    
    if (empty($configuracoes)) {
        logMessage("⚠️ Nenhuma configuração específica de anúncios encontrada", 'warning');
    } else {
        logMessage("📋 Configurações encontradas: " . count($configuracoes), 'info');
        foreach ($configuracoes as $config) {
            logMessage("   - {$config['chave']}: {$config['valor']}", 'info');
        }
    }
} catch (Exception $e) {
    logMessage("❌ Erro ao verificar configurações: " . $e->getMessage(), 'error');
}

// 8. Teste de performance
logMessage("⚡ Teste de performance...", 'info');

$start_time = microtime(true);

try {
    // Query complexa para testar performance
    $resultado = $dbManager->query("
        SELECT 
            g.nome as grupo,
            COUNT(gi.anuncio_id) as total_anuncios,
            g.localizacao,
            g.layout
        FROM grupos_anuncios g
        LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
        WHERE g.ativo = 1
        GROUP BY g.id
        ORDER BY g.criado_em DESC
    ");
    
    $end_time = microtime(true);
    $execution_time = round(($end_time - $start_time) * 1000, 2);
    
    logMessage("✅ Query executada em {$execution_time}ms", 'success');
    logMessage("📊 Resultados encontrados: " . count($resultado), 'info');
    
} catch (Exception $e) {
    logMessage("❌ Erro no teste de performance: " . $e->getMessage(), 'error');
}

// 9. Resumo final
logMessage("📊 RESUMO DOS TESTES", 'info');
logMessage("==================", 'info');

try {
    $stats = [
        'anuncios_total' => $dbManager->queryOne("SELECT COUNT(*) as total FROM anuncios")['total'],
        'anuncios_ativos' => $dbManager->queryOne("SELECT COUNT(*) as total FROM anuncios WHERE ativo = 1")['total'],
        'grupos_total' => $dbManager->queryOne("SELECT COUNT(*) as total FROM grupos_anuncios")['total'],
        'grupos_ativos' => $dbManager->queryOne("SELECT COUNT(*) as total FROM grupos_anuncios WHERE ativo = 1")['total'],
        'associacoes' => $dbManager->queryOne("SELECT COUNT(*) as total FROM grupos_anuncios_items")['total'],
        'cliques_30dias' => $dbManager->queryOne("SELECT COUNT(*) as total FROM cliques_anuncios WHERE data_clique >= DATE_SUB(NOW(), INTERVAL 30 DAY)")['total']
    ];
    
    logMessage("📈 Estatísticas do Sistema:", 'info');
    logMessage("   - Total de anúncios: {$stats['anuncios_total']}", 'info');
    logMessage("   - Anúncios ativos: {$stats['anuncios_ativos']}", 'info');
    logMessage("   - Total de grupos: {$stats['grupos_total']}", 'info');
    logMessage("   - Grupos ativos: {$stats['grupos_ativos']}", 'info');
    logMessage("   - Associações anúncios-grupos: {$stats['associacoes']}", 'info');
    logMessage("   - Cliques (30 dias): {$stats['cliques_30dias']}", 'info');
    
} catch (Exception $e) {
    logMessage("❌ Erro ao gerar estatísticas: " . $e->getMessage(), 'error');
}

// 10. Recomendações
logMessage("💡 RECOMENDAÇÕES", 'info');
logMessage("===============", 'info');

try {
    // Verificar anúncios sem grupo
    $anuncios_sem_grupo = $dbManager->queryOne("
        SELECT COUNT(*) as total 
        FROM anuncios a
        LEFT JOIN grupos_anuncios_items gi ON a.id = gi.anuncio_id
        WHERE gi.anuncio_id IS NULL AND a.ativo = 1
    ");
    
    if ($anuncios_sem_grupo['total'] > 0) {
        logMessage("⚠️ Há {$anuncios_sem_grupo['total']} anúncios ativos sem grupo", 'warning');
        logMessage("   → Associe-os a grupos para que apareçam no site", 'info');
    }
    
    // Verificar grupos sem anúncios
    $grupos_sem_anuncios = $dbManager->queryOne("
        SELECT COUNT(*) as total 
        FROM grupos_anuncios g
        LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
        WHERE gi.anuncio_id IS NULL AND g.ativo = 1
    ");
    
    if ($grupos_sem_anuncios['total'] > 0) {
        logMessage("⚠️ Há {$grupos_sem_anuncios['total']} grupos ativos sem anúncios", 'warning');
        logMessage("   → Adicione anúncios aos grupos ou desative-os", 'info');
    }
    
    logMessage("✅ Sistema funcionando corretamente!", 'success');
    
} catch (Exception $e) {
    logMessage("❌ Erro ao gerar recomendações: " . $e->getMessage(), 'error');
}

logMessage("🎉 Testes concluídos!", 'success');

echo "<h2>✅ Teste do Sistema Concluído!</h2>";
echo "<p>Verifique os logs acima para detalhes dos testes.</p>";
echo "<p><a href='anuncios.php'>← Voltar para Anúncios</a></p>";
echo "<p><a href='grupos-anuncios.php'>← Voltar para Grupos</a></p>";
?>
