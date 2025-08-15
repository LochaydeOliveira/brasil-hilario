<?php
require_once '../config/config.php';
require_once '../config/database_unified.php';
require_once 'includes/auth.php';

check_login();

$dbManager = DatabaseManager::getInstance();

echo "<h2>🔍 Diagnóstico Detalhado da Sidebar</h2>";

try {
    // 1. Verificar grupos da sidebar
    echo "<h3>1. Grupos da sidebar:</h3>";
    $grupos_sidebar = $dbManager->query("
        SELECT g.*, COUNT(gi.anuncio_id) as total_anuncios
        FROM grupos_anuncios g 
        LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
        WHERE g.localizacao = 'sidebar' AND g.ativo = 1
        GROUP BY g.id
        ORDER BY g.criado_em DESC
    ");
    
    if (empty($grupos_sidebar)) {
        echo "<p style='color: red;'>❌ Nenhum grupo da sidebar encontrado!</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Aparecer Início</th><th>Posts Específicos</th><th>Total Anúncios</th></tr>";
        foreach ($grupos_sidebar as $grupo) {
            echo "<tr>";
            echo "<td>{$grupo['id']}</td>";
            echo "<td>{$grupo['nome']}</td>";
            echo "<td>" . ($grupo['aparecer_inicio'] ? '✅ SIM' : '❌ NÃO') . "</td>";
            echo "<td>" . ($grupo['posts_especificos'] ? '✅ SIM' : '❌ NÃO') . "</td>";
            echo "<td>{$grupo['total_anuncios']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 2. Verificar posts associados a cada grupo
    echo "<h3>2. Posts associados aos grupos da sidebar:</h3>";
    foreach ($grupos_sidebar as $grupo) {
        echo "<h4>Grupo: {$grupo['nome']} (ID: {$grupo['id']})</h4>";
        
        $posts_associados = $dbManager->query("
            SELECT gap.post_id, p.titulo, p.slug
            FROM grupos_anuncios_posts gap
            JOIN posts p ON gap.post_id = p.id
            WHERE gap.grupo_id = ?
            ORDER BY p.titulo
        ", [$grupo['id']]);
        
        if (empty($posts_associados)) {
            echo "<p style='color: orange;'>⚠️ Nenhum post associado a este grupo!</p>";
        } else {
            echo "<ul>";
            foreach ($posts_associados as $post) {
                echo "<li>ID {$post['post_id']}: {$post['titulo']} (/{$post['slug']})</li>";
            }
            echo "</ul>";
        }
    }
    
    // 3. Simular lógica do getGruposPorLocalizacao
    echo "<h3>3. Simulando lógica do getGruposPorLocalizacao:</h3>";
    
    // Teste 1: Página inicial
    echo "<h4>Teste 1: Página inicial (isHomePage = true, postId = null)</h4>";
    $sql_home = "SELECT g.*, COUNT(gi.anuncio_id) as total_anuncios
                 FROM grupos_anuncios g 
                 LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
                 WHERE g.localizacao = 'sidebar' AND g.ativo = 1 AND g.aparecer_inicio = 1
                 GROUP BY g.id 
                 ORDER BY g.criado_em DESC";
    
    $grupos_home = $dbManager->query($sql_home);
    
    if (empty($grupos_home)) {
        echo "<p style='color: red;'>❌ Nenhum grupo da sidebar configurado para aparecer na página inicial!</p>";
    } else {
        echo "<p style='color: green;'>✅ Grupos que apareceriam na página inicial:</p>";
        echo "<ul>";
        foreach ($grupos_home as $grupo) {
            echo "<li>{$grupo['nome']} (ID: {$grupo['id']}) - {$grupo['total_anuncios']} anúncios</li>";
        }
        echo "</ul>";
    }
    
    // Teste 2: Post específico (usando post ID 73 como exemplo)
    echo "<h4>Teste 2: Post específico (postId = 73)</h4>";
    $sql_post = "SELECT g.*, COUNT(gi.anuncio_id) as total_anuncios
                 FROM grupos_anuncios g 
                 LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
                 WHERE g.localizacao = 'sidebar' AND g.ativo = 1 
                 AND g.id IN (SELECT gap.grupo_id FROM grupos_anuncios_posts gap WHERE gap.post_id = 73)
                 GROUP BY g.id 
                 ORDER BY g.criado_em DESC";
    
    $grupos_post = $dbManager->query($sql_post);
    
    if (empty($grupos_post)) {
        echo "<p style='color: red;'>❌ Nenhum grupo da sidebar configurado para o post ID 73!</p>";
    } else {
        echo "<p style='color: green;'>✅ Grupos que apareceriam no post ID 73:</p>";
        echo "<ul>";
        foreach ($grupos_post as $grupo) {
            echo "<li>{$grupo['nome']} (ID: {$grupo['id']}) - {$grupo['total_anuncios']} anúncios</li>";
        }
        echo "</ul>";
    }
    
    // 4. Verificar anúncios ativos
    echo "<h3>4. Anúncios ativos em grupos da sidebar:</h3>";
    $anuncios_ativos = $dbManager->query("
        SELECT a.id, a.titulo, a.ativo, g.nome as grupo_nome, g.id as grupo_id
        FROM anuncios a 
        JOIN grupos_anuncios_items gi ON a.id = gi.anuncio_id
        JOIN grupos_anuncios g ON gi.grupo_id = g.id
        WHERE g.localizacao = 'sidebar' AND g.ativo = 1 AND a.ativo = 1
        ORDER BY g.id, a.titulo
    ");
    
    if (empty($anuncios_ativos)) {
        echo "<p style='color: red;'>❌ Nenhum anúncio ativo encontrado em grupos da sidebar!</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Anúncio</th><th>Status</th><th>Grupo</th></tr>";
        foreach ($anuncios_ativos as $anuncio) {
            echo "<tr>";
            echo "<td>{$anuncio['id']}</td>";
            echo "<td>{$anuncio['titulo']}</td>";
            echo "<td>" . ($anuncio['ativo'] ? '✅ Ativo' : '❌ Inativo') . "</td>";
            echo "<td>{$anuncio['grupo_nome']} (ID: {$anuncio['grupo_id']})</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 5. Teste da lógica atual
    echo "<h3>5. Teste da lógica atual do GruposAnunciosManager:</h3>";
    
    require_once '../includes/GruposAnunciosManager.php';
    $gruposManager = new GruposAnunciosManager($pdo);
    
    // Teste página inicial
    $grupos_home_manager = $gruposManager->getGruposPorLocalizacao('sidebar', null, true);
    echo "<h4>Página inicial (getGruposPorLocalizacao):</h4>";
    if (empty($grupos_home_manager)) {
        echo "<p style='color: red;'>❌ Nenhum grupo retornado para página inicial!</p>";
    } else {
        echo "<p style='color: green;'>✅ Grupos retornados para página inicial:</p>";
        echo "<ul>";
        foreach ($grupos_home_manager as $grupo) {
            echo "<li>{$grupo['nome']} (ID: {$grupo['id']}) - {$grupo['total_anuncios']} anúncios</li>";
        }
        echo "</ul>";
    }
    
    // Teste post específico
    $grupos_post_manager = $gruposManager->getGruposPorLocalizacao('sidebar', 73, false);
    echo "<h4>Post específico ID 73 (getGruposPorLocalizacao):</h4>";
    if (empty($grupos_post_manager)) {
        echo "<p style='color: red;'>❌ Nenhum grupo retornado para post ID 73!</p>";
    } else {
        echo "<p style='color: green;'>✅ Grupos retornados para post ID 73:</p>";
        echo "<ul>";
        foreach ($grupos_post_manager as $grupo) {
            echo "<li>{$grupo['nome']} (ID: {$grupo['id']}) - {$grupo['total_anuncios']} anúncios</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<br><a href='anuncios.php'>← Voltar para Anúncios</a>";
?>
