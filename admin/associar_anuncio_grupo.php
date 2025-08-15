<?php
/**
 * ASSOCIAR ANÚNCIO AO GRUPO
 * Brasil Hilário - Correção automática
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

// Início da correção
logMessage("🔧 Iniciando associação do anúncio ao grupo...", 'info');

$dbManager = DatabaseManager::getInstance();

// 1. Verificar anúncio ID 23
logMessage("📊 Verificando anúncio ID 23...", 'info');

$anuncio = $dbManager->queryOne("
    SELECT id, titulo, localizacao, ativo
    FROM anuncios 
    WHERE id = 23
");

if (!$anuncio) {
    logMessage("❌ Anúncio ID 23 não encontrado!", 'error');
    die("Anúncio não encontrado!");
}

logMessage("✅ Anúncio encontrado: {$anuncio['titulo']} | Localização: {$anuncio['localizacao']}", 'success');

// 2. Verificar grupo "Ferramentas - Amazon"
logMessage("📊 Verificando grupo Ferramentas - Amazon...", 'info');

$grupo = $dbManager->queryOne("
    SELECT id, nome, localizacao, ativo
    FROM grupos_anuncios 
    WHERE nome LIKE '%Ferramentas%' AND localizacao = 'sidebar'
");

if (!$grupo) {
    logMessage("❌ Grupo Ferramentas - Amazon não encontrado!", 'error');
    die("Grupo não encontrado!");
}

logMessage("✅ Grupo encontrado: {$grupo['nome']} | ID: {$grupo['id']}", 'success');

// 3. Verificar se já existe associação
logMessage("📊 Verificando se já existe associação...", 'info');

$associacao_existe = $dbManager->queryOne("
    SELECT id FROM grupos_anuncios_items 
    WHERE grupo_id = ? AND anuncio_id = 23
", [$grupo['id']]);

if ($associacao_existe) {
    logMessage("ℹ️ Associação já existe!", 'info');
} else {
    // 4. Criar associação
    logMessage("🔧 Criando associação...", 'info');
    
    // Pegar a próxima ordem disponível
    $ultima_ordem = $dbManager->queryOne("
        SELECT MAX(ordem) as max_ordem 
        FROM grupos_anuncios_items 
        WHERE grupo_id = ?
    ", [$grupo['id']]);
    
    $nova_ordem = ($ultima_ordem['max_ordem'] ?? -1) + 1;
    
    $resultado = $dbManager->execute("
        INSERT INTO grupos_anuncios_items (grupo_id, anuncio_id, ordem) 
        VALUES (?, 23, ?)
    ", [$grupo['id'], $nova_ordem]);
    
    if ($resultado) {
        logMessage("✅ Associação criada com sucesso! Ordem: $nova_ordem", 'success');
    } else {
        logMessage("❌ Erro ao criar associação!", 'error');
    }
}

// 5. Verificar resultado final
logMessage("📊 Verificando resultado final...", 'info');

$anuncios_grupo = $dbManager->query("
    SELECT a.id, a.titulo, gi.ordem
    FROM anuncios a
    JOIN grupos_anuncios_items gi ON a.id = gi.anuncio_id
    WHERE gi.grupo_id = ? AND a.ativo = 1
    ORDER BY gi.ordem
", [$grupo['id']]);

logMessage("📋 Anúncios no grupo {$grupo['nome']}: " . count($anuncios_grupo), 'info');
foreach ($anuncios_grupo as $anuncio_grupo) {
    logMessage("   - Ordem {$anuncio_grupo['ordem']}: {$anuncio_grupo['titulo']}", 'info');
}

// 6. Testar se o anúncio aparece agora
logMessage("🔧 Testando se o anúncio aparece na sidebar...", 'info');

require_once '../includes/GruposAnunciosManager.php';
$gruposManager = new GruposAnunciosManager($dbManager->getConnection());

$grupos_sidebar = $gruposManager->getGruposPorLocalizacao('sidebar', null, true);
logMessage("📋 Grupos encontrados para sidebar (página inicial): " . count($grupos_sidebar), 'info');

foreach ($grupos_sidebar as $grupo_sidebar) {
    logMessage("   - Grupo: {$grupo_sidebar['nome']}", 'info');
    
    $anuncios_grupo = $gruposManager->getAnunciosDoGrupo($grupo_sidebar['id']);
    logMessage("     Anúncios no grupo: " . count($anuncios_grupo), 'info');
    
    foreach ($anuncios_grupo as $anuncio_grupo) {
        logMessage("       - {$anuncio_grupo['titulo']}", 'info');
    }
}

// 7. Finalização
logMessage("🎉 Correção concluída com sucesso!", 'success');
logMessage("💡 Agora o anúncio deve aparecer na sidebar!", 'info');

echo "<h2>✅ Anúncio Associado com Sucesso!</h2>";
echo "<p>O anúncio 'Brinquedo Star Plic' foi associado ao grupo 'Ferramentas - Amazon'.</p>";
echo "<p>Agora ele deve aparecer na sidebar!</p>";
echo "<p><a href='../index.php' target='_blank'>← Testar no Site</a></p>";
echo "<p><a href='anuncios.php'>← Voltar para Anúncios</a></p>";
?>
