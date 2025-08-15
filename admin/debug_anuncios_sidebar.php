<?php
/**
 * DIAGNÓSTICO DE ANÚNCIOS NA SIDEBAR
 * Brasil Hilário - Debug de anúncios
 */

// Configurações
define('DEBUG_MODE', true);

// Incluir configurações
require_once '../config/config.php';
require_once '../config/database_unified.php';
require_once '../includes/GruposAnunciosManager.php';

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
logMessage("🔍 Iniciando diagnóstico de anúncios na sidebar...", 'info');

$dbManager = DatabaseManager::getInstance();
$pdo = $dbManager->getConnection();

// 1. Verificar anúncios ativos
logMessage("📊 Verificando anúncios ativos...", 'info');

$anuncios_ativos = $dbManager->query("
    SELECT id, titulo, localizacao, ativo, criado_em, data_inicio, data_fim
    FROM anuncios 
    WHERE ativo = 1
    ORDER BY criado_em DESC
");

logMessage("📋 Anúncios ativos encontrados: " . count($anuncios_ativos), 'info');
foreach ($anuncios_ativos as $anuncio) {
    logMessage("   - ID: {$anuncio['id']} | Título: {$anuncio['titulo']} | Localização: {$anuncio['localizacao']}", 'info');
}

// 2. Verificar grupos ativos
logMessage("📊 Verificando grupos ativos...", 'info');

$grupos_ativos = $dbManager->query("
    SELECT id, nome, localizacao, ativo, posts_especificos, aparecer_inicio, prioridade
    FROM grupos_anuncios 
    WHERE ativo = 1
    ORDER BY prioridade DESC, criado_em DESC
");

logMessage("📋 Grupos ativos encontrados: " . count($grupos_ativos), 'info');
foreach ($grupos_ativos as $grupo) {
    logMessage("   - ID: {$grupo['id']} | Nome: {$grupo['nome']} | Localização: {$grupo['localizacao']} | Posts específicos: {$grupo['posts_especificos']} | Aparecer início: {$grupo['aparecer_inicio']}", 'info');
}

// 3. Verificar associações anúncios-grupos
logMessage("📊 Verificando associações anúncios-grupos...", 'info');

$associacoes = $dbManager->query("
    SELECT gi.grupo_id, gi.anuncio_id, gi.ordem, g.nome as grupo_nome, a.titulo as anuncio_titulo
    FROM grupos_anuncios_items gi
    JOIN grupos_anuncios g ON gi.grupo_id = g.id
    JOIN anuncios a ON gi.anuncio_id = a.id
    WHERE g.ativo = 1 AND a.ativo = 1
    ORDER BY gi.grupo_id, gi.ordem
");

logMessage("📋 Associações encontradas: " . count($associacoes), 'info');
foreach ($associacoes as $assoc) {
    logMessage("   - Grupo: {$assoc['grupo_nome']} | Anúncio: {$assoc['anuncio_titulo']} | Ordem: {$assoc['ordem']}", 'info');
}

// 4. Testar método getGruposPorLocalizacao
logMessage("🔧 Testando método getGruposPorLocalizacao...", 'info');

$gruposManager = new GruposAnunciosManager($pdo);

// Teste 1: Página inicial
logMessage("   Teste 1: Página inicial (isHomePage = true)", 'info');
$grupos_home = $gruposManager->getGruposPorLocalizacao('sidebar', null, true);
logMessage("   Grupos encontrados para página inicial: " . count($grupos_home), 'info');
foreach ($grupos_home as $grupo) {
    logMessage("     - Grupo: {$grupo['nome']} | Posts específicos: {$grupo['posts_especificos']} | Aparecer início: {$grupo['aparecer_inicio']}", 'info');
}

// Teste 2: Post específico (simulando post ID 1)
logMessage("   Teste 2: Post específico (postId = 1)", 'info');
$grupos_post = $gruposManager->getGruposPorLocalizacao('sidebar', 1, false);
logMessage("   Grupos encontrados para post específico: " . count($grupos_post), 'info');
foreach ($grupos_post as $grupo) {
    logMessage("     - Grupo: {$grupo['nome']} | Posts específicos: {$grupo['posts_especificos']} | Aparecer início: {$grupo['aparecer_inicio']}", 'info');
}

// 5. Verificar posts específicos associados
logMessage("📊 Verificando posts específicos associados...", 'info');

$posts_especificos = $dbManager->query("
    SELECT gap.grupo_id, gap.post_id, g.nome as grupo_nome, p.titulo as post_titulo
    FROM grupos_anuncios_posts gap
    JOIN grupos_anuncios g ON gap.grupo_id = g.id
    JOIN posts p ON gap.post_id = p.id
    WHERE g.ativo = 1
    ORDER BY gap.grupo_id, gap.post_id
");

logMessage("📋 Posts específicos associados: " . count($posts_especificos), 'info');
foreach ($posts_especificos as $post) {
    logMessage("   - Grupo: {$post['grupo_nome']} | Post: {$post['post_titulo']}", 'info');
}

// 6. Verificar anúncios sem grupo
logMessage("📊 Verificando anúncios sem grupo...", 'info');

$anuncios_sem_grupo = $dbManager->query("
    SELECT a.id, a.titulo, a.localizacao
    FROM anuncios a
    LEFT JOIN grupos_anuncios_items gi ON a.id = gi.anuncio_id
    WHERE a.ativo = 1 AND gi.anuncio_id IS NULL
    ORDER BY a.criado_em DESC
");

logMessage("📋 Anúncios sem grupo: " . count($anuncios_sem_grupo), 'info');
foreach ($anuncios_sem_grupo as $anuncio) {
    logMessage("   - ID: {$anuncio['id']} | Título: {$anuncio['titulo']} | Localização: {$anuncio['localizacao']}", 'warning');
}

// 7. Verificar grupos sem anúncios
logMessage("📊 Verificando grupos sem anúncios...", 'info');

$grupos_sem_anuncios = $dbManager->query("
    SELECT g.id, g.nome, g.localizacao
    FROM grupos_anuncios g
    LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
    WHERE g.ativo = 1 AND gi.grupo_id IS NULL
    ORDER BY g.criado_em DESC
");

logMessage("📋 Grupos sem anúncios: " . count($grupos_sem_anuncios), 'info');
foreach ($grupos_sem_anuncios as $grupo) {
    logMessage("   - ID: {$grupo['id']} | Nome: {$grupo['nome']} | Localização: {$grupo['localizacao']}", 'warning');
}

// 8. Verificar configurações de data
logMessage("📊 Verificando configurações de data...", 'info');

$anuncios_com_data = $dbManager->query("
    SELECT id, titulo, data_inicio, data_fim, NOW() as agora
    FROM anuncios 
    WHERE ativo = 1 AND (data_inicio IS NOT NULL OR data_fim IS NOT NULL)
    ORDER BY criado_em DESC
");

logMessage("📋 Anúncios com controle de data: " . count($anuncios_com_data), 'info');
foreach ($anuncios_com_data as $anuncio) {
    $status_data = "OK";
    if ($anuncio['data_inicio'] && $anuncio['data_inicio'] > $anuncio['agora']) {
        $status_data = "Ainda não iniciou";
    } elseif ($anuncio['data_fim'] && $anuncio['data_fim'] < $anuncio['agora']) {
        $status_data = "Já expirou";
    }
    logMessage("   - ID: {$anuncio['id']} | Título: {$anuncio['titulo']} | Início: {$anuncio['data_inicio']} | Fim: {$anuncio['data_fim']} | Status: $status_data", 'info');
}

// 9. Resumo e recomendações
logMessage("📋 RESUMO DO DIAGNÓSTICO:", 'info');
logMessage("   - Anúncios ativos: " . count($anuncios_ativos), 'info');
logMessage("   - Grupos ativos: " . count($grupos_ativos), 'info');
logMessage("   - Associações: " . count($associacoes), 'info');
logMessage("   - Anúncios sem grupo: " . count($anuncios_sem_grupo), 'info');
logMessage("   - Grupos sem anúncios: " . count($grupos_sem_anuncios), 'info');

// 10. Possíveis problemas identificados
logMessage("🔍 POSSÍVEIS PROBLEMAS IDENTIFICADOS:", 'warning');

if (count($anuncios_sem_grupo) > 0) {
    logMessage("   ❌ PROBLEMA: Existem anúncios ativos que não estão associados a nenhum grupo!", 'error');
    logMessage("   💡 SOLUÇÃO: Associe os anúncios a um grupo ou crie um grupo para eles.", 'info');
}

if (count($grupos_sem_anuncios) > 0) {
    logMessage("   ❌ PROBLEMA: Existem grupos ativos que não têm anúncios associados!", 'error');
    logMessage("   💡 SOLUÇÃO: Adicione anúncios aos grupos ou desative os grupos vazios.", 'info');
}

if (count($associacoes) == 0) {
    logMessage("   ❌ PROBLEMA: Não há nenhuma associação entre anúncios e grupos!", 'error');
    logMessage("   💡 SOLUÇÃO: Crie associações no painel admin.", 'info');
}

// 11. Finalização
logMessage("🎉 Diagnóstico concluído!", 'success');

echo "<h2>✅ Diagnóstico Concluído!</h2>";
echo "<p>Verifique os logs acima para identificar o problema.</p>";
echo "<p><a href='anuncios.php'>← Voltar para Anúncios</a></p>";
echo "<p><a href='grupos-anuncios.php'>← Ir para Grupos de Anúncios</a></p>";
?>
