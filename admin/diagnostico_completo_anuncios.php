<?php
/**
 * DIAGNÓSTICO COMPLETO DO SISTEMA DE ANÚNCIOS
 * Brasil Hilário - Análise detalhada
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

// Início do diagnóstico
logMessage("🔍 INICIANDO DIAGNÓSTICO COMPLETO DO SISTEMA DE ANÚNCIOS", 'info');
logMessage("=======================================================", 'info');

$dbManager = DatabaseManager::getInstance();

// 1. VERIFICAR ESTRUTURA DO BANCO
logMessage("📋 1. VERIFICANDO ESTRUTURA DO BANCO", 'info');

$tabelas_necessarias = [
    'anuncios' => ['id', 'titulo', 'imagem', 'link_compra', 'marca', 'ativo', 'criado_em'],
    'grupos_anuncios' => ['id', 'nome', 'localizacao', 'layout', 'marca', 'ativo', 'posts_especificos', 'aparecer_inicio', 'criado_em'],
    'grupos_anuncios_items' => ['id', 'grupo_id', 'anuncio_id', 'ordem'],
    'grupos_anuncios_posts' => ['id', 'grupo_id', 'post_id'],
    'cliques_anuncios' => ['id', 'anuncio_id', 'post_id', 'tipo_clique', 'ip_usuario', 'data_clique']
];

foreach ($tabelas_necessarias as $tabela => $colunas) {
    try {
        $resultado = $dbManager->queryOne("SHOW TABLES LIKE '$tabela'");
        if ($resultado) {
            logMessage("✅ Tabela '$tabela' existe", 'success');
            
            // Verificar colunas
            $colunas_banco = $dbManager->query("SHOW COLUMNS FROM $tabela");
            $colunas_banco_nomes = array_column($colunas_banco, 'Field');
            
            foreach ($colunas as $coluna) {
                if (in_array($coluna, $colunas_banco_nomes)) {
                    logMessage("   ✅ Coluna '$coluna' existe", 'success');
                } else {
                    logMessage("   ❌ Coluna '$coluna' NÃO existe", 'error');
                }
            }
        } else {
            logMessage("❌ Tabela '$tabela' NÃO existe", 'error');
        }
    } catch (Exception $e) {
        logMessage("❌ Erro ao verificar tabela '$tabela': " . $e->getMessage(), 'error');
    }
}

// 2. VERIFICAR DADOS EXISTENTES
logMessage("📊 2. VERIFICANDO DADOS EXISTENTES", 'info');

try {
    // Anúncios
    $total_anuncios = $dbManager->queryOne("SELECT COUNT(*) as total FROM anuncios")['total'];
    $anuncios_ativos = $dbManager->queryOne("SELECT COUNT(*) as total FROM anuncios WHERE ativo = 1")['total'];
    
    logMessage("📈 Anúncios: $total_anuncios total, $anuncios_ativos ativos", 'info');
    
    if ($anuncios_ativos > 0) {
        $anuncios = $dbManager->query("SELECT id, titulo, ativo FROM anuncios WHERE ativo = 1 ORDER BY criado_em DESC LIMIT 5");
        foreach ($anuncios as $anuncio) {
            logMessage("   - ID {$anuncio['id']}: {$anuncio['titulo']}", 'info');
        }
    }
    
    // Grupos
    $total_grupos = $dbManager->queryOne("SELECT COUNT(*) as total FROM grupos_anuncios")['total'];
    $grupos_ativos = $dbManager->queryOne("SELECT COUNT(*) as total FROM grupos_anuncios WHERE ativo = 1")['total'];
    
    logMessage("📈 Grupos: $total_grupos total, $grupos_ativos ativos", 'info');
    
    if ($grupos_ativos > 0) {
        $grupos = $dbManager->query("SELECT id, nome, localizacao, layout, ativo, posts_especificos, aparecer_inicio FROM grupos_anuncios WHERE ativo = 1 ORDER BY criado_em DESC");
        foreach ($grupos as $grupo) {
            logMessage("   - ID {$grupo['id']}: {$grupo['nome']} ({$grupo['localizacao']}, {$grupo['layout']})", 'info');
            logMessage("     Posts específicos: " . ($grupo['posts_especificos'] ? 'SIM' : 'NÃO'), 'info');
            logMessage("     Aparecer início: " . ($grupo['aparecer_inicio'] ? 'SIM' : 'NÃO'), 'info');
        }
    }
    
} catch (Exception $e) {
    logMessage("❌ Erro ao verificar dados: " . $e->getMessage(), 'error');
}

// 3. VERIFICAR ASSOCIAÇÕES
logMessage("🔗 3. VERIFICANDO ASSOCIAÇÕES", 'info');

try {
    $associacoes = $dbManager->query("
        SELECT g.id as grupo_id, g.nome as grupo_nome, g.localizacao, 
               COUNT(gi.anuncio_id) as total_anuncios,
               GROUP_CONCAT(a.titulo SEPARATOR ', ') as anuncios
        FROM grupos_anuncios g
        LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
        LEFT JOIN anuncios a ON gi.anuncio_id = a.id
        WHERE g.ativo = 1
        GROUP BY g.id
        ORDER BY g.criado_em DESC
    ");
    
    foreach ($associacoes as $assoc) {
        logMessage("📋 Grupo '{$assoc['grupo_nome']}' ({$assoc['localizacao']}): {$assoc['total_anuncios']} anúncios", 'info');
        if ($assoc['total_anuncios'] > 0) {
            logMessage("   Anúncios: {$assoc['anuncios']}", 'info');
        }
    }
    
} catch (Exception $e) {
    logMessage("❌ Erro ao verificar associações: " . $e->getMessage(), 'error');
}

// 4. TESTAR LÓGICA DE FILTRAGEM
logMessage("🎯 4. TESTANDO LÓGICA DE FILTRAGEM", 'info');

try {
    require_once '../includes/GruposAnunciosManager.php';
    $gruposManager = new GruposAnunciosManager($dbManager->getConnection());
    
    // Teste 1: Grupos para sidebar (página inicial)
    logMessage("🔍 Teste 1: Grupos para sidebar (página inicial)", 'info');
    $grupos_sidebar_home = $gruposManager->getGruposPorLocalizacao('sidebar', null, true);
    logMessage("   Grupos encontrados: " . count($grupos_sidebar_home), 'info');
    
    foreach ($grupos_sidebar_home as $grupo) {
        logMessage("   - Grupo: {$grupo['nome']} (aparecer_inicio: {$grupo['aparecer_inicio']})", 'info');
        
        $anuncios_grupo = $gruposManager->getAnunciosDoGrupo($grupo['id']);
        logMessage("     Anúncios no grupo: " . count($anuncios_grupo), 'info');
        
        foreach ($anuncios_grupo as $anuncio) {
            logMessage("       - {$anuncio['titulo']} (ativo: {$anuncio['ativo']})", 'info');
        }
    }
    
    // Teste 2: Grupos para sidebar (post específico)
    logMessage("🔍 Teste 2: Grupos para sidebar (post específico)", 'info');
    $grupos_sidebar_post = $gruposManager->getGruposPorLocalizacao('sidebar', 1, false);
    logMessage("   Grupos encontrados: " . count($grupos_sidebar_post), 'info');
    
    foreach ($grupos_sidebar_post as $grupo) {
        logMessage("   - Grupo: {$grupo['nome']} (posts_especificos: {$grupo['posts_especificos']})", 'info');
    }
    
} catch (Exception $e) {
    logMessage("❌ Erro ao testar lógica: " . $e->getMessage(), 'error');
}

// 5. VERIFICAR PROBLEMAS ESPECÍFICOS
logMessage("🚨 5. VERIFICANDO PROBLEMAS ESPECÍFICOS", 'info');

try {
    // Anúncios ativos sem grupo
    $anuncios_sem_grupo = $dbManager->query("
        SELECT a.id, a.titulo, a.ativo
        FROM anuncios a
        LEFT JOIN grupos_anuncios_items gi ON a.id = gi.anuncio_id
        WHERE gi.anuncio_id IS NULL AND a.ativo = 1
    ");
    
    if (!empty($anuncios_sem_grupo)) {
        logMessage("⚠️ PROBLEMA: Anúncios ativos sem grupo", 'warning');
        foreach ($anuncios_sem_grupo as $anuncio) {
            logMessage("   - ID {$anuncio['id']}: {$anuncio['titulo']} (NÃO APARECERÁ NO SITE)", 'warning');
        }
    } else {
        logMessage("✅ Todos os anúncios ativos estão em grupos", 'success');
    }
    
    // Grupos ativos sem anúncios
    $grupos_sem_anuncios = $dbManager->query("
        SELECT g.id, g.nome, g.localizacao
        FROM grupos_anuncios g
        LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
        WHERE gi.anuncio_id IS NULL AND g.ativo = 1
    ");
    
    if (!empty($grupos_sem_anuncios)) {
        logMessage("⚠️ PROBLEMA: Grupos ativos sem anúncios", 'warning');
        foreach ($grupos_sem_anuncios as $grupo) {
            logMessage("   - ID {$grupo['id']}: {$grupo['nome']} ({$grupo['localizacao']}) - VAZIO", 'warning');
        }
    } else {
        logMessage("✅ Todos os grupos ativos têm anúncios", 'success');
    }
    
    // Grupos com configuração incorreta
    $grupos_config_incorreta = $dbManager->query("
        SELECT g.id, g.nome, g.localizacao, g.posts_especificos, g.aparecer_inicio,
               COUNT(gap.post_id) as posts_associados
        FROM grupos_anuncios g
        LEFT JOIN grupos_anuncios_posts gap ON g.id = gap.grupo_id
        WHERE g.ativo = 1 AND g.posts_especificos = 1
        GROUP BY g.id
        HAVING posts_associados = 0
    ");
    
    if (!empty($grupos_config_incorreta)) {
        logMessage("⚠️ PROBLEMA: Grupos com posts específicos mas sem posts associados", 'warning');
        foreach ($grupos_config_incorreta as $grupo) {
            logMessage("   - ID {$grupo['id']}: {$grupo['nome']} - Configurado para posts específicos mas sem posts", 'warning');
        }
    }
    
} catch (Exception $e) {
    logMessage("❌ Erro ao verificar problemas: " . $e->getMessage(), 'error');
}

// 6. SIMULAR LÓGICA DO FRONTEND
logMessage("🌐 6. SIMULANDO LÓGICA DO FRONTEND", 'info');

try {
    // Simular página inicial
    $current_url = '/';
    $isHomePage = (
        $current_url === '/' || 
        $current_url === '/index.php' || 
        preg_match('/^\/\d+$/', $current_url) ||
        (basename($_SERVER['PHP_SELF']) === 'index.php' && !isset($_GET['slug']))
    );
    $postId = null;
    
    logMessage("🔍 Simulando página inicial:", 'info');
    logMessage("   URL: $current_url", 'info');
    logMessage("   isHomePage: " . ($isHomePage ? 'SIM' : 'NÃO'), 'info');
    logMessage("   postId: " . ($postId ?? 'null'), 'info');
    
    $gruposSidebar = $gruposManager->getGruposPorLocalizacao('sidebar', $postId, $isHomePage);
    logMessage("   Grupos encontrados para sidebar: " . count($gruposSidebar), 'info');
    
    foreach ($gruposSidebar as $grupo) {
        logMessage("   📋 Grupo: {$grupo['nome']}", 'info');
        logMessage("     - Localização: {$grupo['localizacao']}", 'info');
        logMessage("     - Aparecer início: " . ($grupo['aparecer_inicio'] ? 'SIM' : 'NÃO'), 'info');
        logMessage("     - Posts específicos: " . ($grupo['posts_especificos'] ? 'SIM' : 'NÃO'), 'info');
        
        $anuncios = $gruposManager->getAnunciosDoGrupo($grupo['id']);
        logMessage("     - Anúncios no grupo: " . count($anuncios), 'info');
        
        foreach ($anuncios as $anuncio) {
            logMessage("       • {$anuncio['titulo']} (ativo: {$anuncio['ativo']})", 'info');
        }
    }
    
} catch (Exception $e) {
    logMessage("❌ Erro ao simular frontend: " . $e->getMessage(), 'error');
}

// 7. VERIFICAR CONFIGURAÇÕES ESPECÍFICAS
logMessage("⚙️ 7. VERIFICANDO CONFIGURAÇÕES ESPECÍFICAS", 'info');

try {
    // Verificar se há grupos configurados corretamente para sidebar
    $grupos_sidebar_corretos = $dbManager->query("
        SELECT g.*, COUNT(gi.anuncio_id) as total_anuncios
        FROM grupos_anuncios g
        LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
        WHERE g.localizacao = 'sidebar' 
        AND g.ativo = 1 
        AND g.aparecer_inicio = 1
        GROUP BY g.id
        HAVING total_anuncios > 0
    ");
    
    logMessage("📋 Grupos corretamente configurados para sidebar (página inicial): " . count($grupos_sidebar_corretos), 'info');
    
    if (empty($grupos_sidebar_corretos)) {
        logMessage("🚨 PROBLEMA PRINCIPAL: Nenhum grupo configurado corretamente para sidebar!", 'error');
        logMessage("   Para aparecer na sidebar da página inicial, o grupo deve ter:", 'info');
        logMessage("   - localizacao = 'sidebar'", 'info');
        logMessage("   - ativo = 1", 'info');
        logMessage("   - aparecer_inicio = 1", 'info');
        logMessage("   - pelo menos um anúncio associado", 'info');
    } else {
        foreach ($grupos_sidebar_corretos as $grupo) {
            logMessage("   ✅ Grupo '{$grupo['nome']}' configurado corretamente", 'success');
        }
    }
    
} catch (Exception $e) {
    logMessage("❌ Erro ao verificar configurações: " . $e->getMessage(), 'error');
}

// 8. RECOMENDAÇÕES
logMessage("💡 8. RECOMENDAÇÕES", 'info');
logMessage("================", 'info');

try {
    // Verificar se há problemas
    $problemas = [];
    
    // Anúncios sem grupo
    $anuncios_sem_grupo_count = $dbManager->queryOne("
        SELECT COUNT(*) as total 
        FROM anuncios a
        LEFT JOIN grupos_anuncios_items gi ON a.id = gi.anuncio_id
        WHERE gi.anuncio_id IS NULL AND a.ativo = 1
    ")['total'];
    
    if ($anuncios_sem_grupo_count > 0) {
        $problemas[] = "Há $anuncios_sem_grupo_count anúncios ativos sem grupo";
    }
    
    // Grupos sem anúncios
    $grupos_sem_anuncios_count = $dbManager->queryOne("
        SELECT COUNT(*) as total 
        FROM grupos_anuncios g
        LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
        WHERE gi.anuncio_id IS NULL AND g.ativo = 1
    ")['total'];
    
    if ($grupos_sem_anuncios_count > 0) {
        $problemas[] = "Há $grupos_sem_anuncios_count grupos ativos sem anúncios";
    }
    
    // Grupos sidebar sem aparecer_inicio
    $grupos_sidebar_sem_inicio = $dbManager->queryOne("
        SELECT COUNT(*) as total 
        FROM grupos_anuncios 
        WHERE localizacao = 'sidebar' AND ativo = 1 AND aparecer_inicio = 0
    ")['total'];
    
    if ($grupos_sidebar_sem_inicio > 0) {
        $problemas[] = "Há $grupos_sidebar_sem_inicio grupos de sidebar que não aparecem na página inicial";
    }
    
    if (empty($problemas)) {
        logMessage("✅ Sistema configurado corretamente!", 'success');
    } else {
        logMessage("⚠️ Problemas identificados:", 'warning');
        foreach ($problemas as $problema) {
            logMessage("   - $problema", 'warning');
        }
        
        logMessage("🔧 Ações recomendadas:", 'info');
        logMessage("   1. Associe anúncios ativos a grupos", 'info');
        logMessage("   2. Configure grupos para aparecer na página inicial", 'info');
        logMessage("   3. Verifique se os grupos têm anúncios", 'info');
    }
    
} catch (Exception $e) {
    logMessage("❌ Erro ao gerar recomendações: " . $e->getMessage(), 'error');
}

logMessage("🎉 DIAGNÓSTICO CONCLUÍDO!", 'success');

echo "<h2>✅ Diagnóstico Completo Concluído!</h2>";
echo "<p>Verifique os logs acima para identificar problemas específicos.</p>";
echo "<p><a href='anuncios.php'>← Voltar para Anúncios</a></p>";
echo "<p><a href='grupos-anuncios.php'>← Voltar para Grupos</a></p>";
?>
