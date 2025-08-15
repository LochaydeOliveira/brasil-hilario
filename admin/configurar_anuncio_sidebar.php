<?php
require_once '../config/config.php';
require_once '../config/database_unified.php';
require_once 'includes/auth.php';

check_login();

$dbManager = DatabaseManager::getInstance();

echo "<h2>⚙️ Configurando Anúncio da Sidebar</h2>";

try {
    // 1. Verificar anúncio da sidebar
    echo "<h3>1. Anúncio da sidebar:</h3>";
    $anuncio_sidebar = $dbManager->query("
        SELECT id, titulo, localizacao, ativo, marca
        FROM anuncios 
        WHERE localizacao = 'sidebar' AND ativo = 1
        ORDER BY criado_em DESC
    ");
    
    if (empty($anuncio_sidebar)) {
        echo "<p style='color: red;'>❌ Nenhum anúncio da sidebar ativo encontrado!</p>";
        return;
    }
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Título</th><th>Localização</th><th>Status</th><th>Marca</th></tr>";
    foreach ($anuncio_sidebar as $anuncio) {
        echo "<tr>";
        echo "<td>{$anuncio['id']}</td>";
        echo "<td>{$anuncio['titulo']}</td>";
        echo "<td>{$anuncio['localizacao']}</td>";
        echo "<td>" . ($anuncio['ativo'] ? '✅ Ativo' : '❌ Inativo') . "</td>";
        echo "<td>{$anuncio['marca']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 2. Verificar posts já configurados
    echo "<h3>2. Posts já configurados para o anúncio:</h3>";
    $posts_configurados = $dbManager->query("
        SELECT ap.post_id, p.titulo, p.slug
        FROM anuncios_posts ap
        JOIN posts p ON ap.post_id = p.id
        WHERE ap.anuncio_id = ?
        ORDER BY p.titulo
    ", [$anuncio_sidebar[0]['id']]);
    
    if (empty($posts_configurados)) {
        echo "<p style='color: orange;'>⚠️ Nenhum post configurado para este anúncio!</p>";
    } else {
        echo "<ul>";
        foreach ($posts_configurados as $post) {
            echo "<li>ID {$post['post_id']}: {$post['titulo']} (/{$post['slug']})</li>";
        }
        echo "</ul>";
    }
    
    // 3. Configurar posts específicos
    echo "<h3>3. Configurando posts específicos:</h3>";
    
    // Posts que devem mostrar o anúncio (IDs 72 e 73)
    $posts_para_configurar = [72, 73];
    
    foreach ($posts_para_configurar as $post_id) {
        // Verificar se já está configurado
        $ja_configurado = $dbManager->queryOne("
            SELECT COUNT(*) as total
            FROM anuncios_posts ap
            WHERE ap.anuncio_id = ? AND ap.post_id = ?
        ", [$anuncio_sidebar[0]['id'], $post_id]);
        
        if ($ja_configurado['total'] == 0) {
            // Configurar o post
            $dbManager->execute("
                INSERT INTO anuncios_posts (anuncio_id, post_id) 
                VALUES (?, ?)
            ", [$anuncio_sidebar[0]['id'], $post_id]);
            
            echo "<p style='color: green;'>✅ Configurado anúncio para post ID: $post_id</p>";
        } else {
            echo "<p style='color: blue;'>ℹ️ Post ID $post_id já estava configurado</p>";
        }
    }
    
    // 4. Verificar resultado final
    echo "<h3>4. Configuração final:</h3>";
    $posts_final = $dbManager->query("
        SELECT ap.post_id, p.titulo, p.slug
        FROM anuncios_posts ap
        JOIN posts p ON ap.post_id = p.id
        WHERE ap.anuncio_id = ?
        ORDER BY p.titulo
    ", [$anuncio_sidebar[0]['id']]);
    
    if (empty($posts_final)) {
        echo "<p style='color: red;'>❌ Ainda não há posts configurados!</p>";
    } else {
        echo "<p style='color: green;'>✅ Posts configurados para exibir o anúncio da sidebar:</p>";
        echo "<ul>";
        foreach ($posts_final as $post) {
            echo "<li>ID {$post['post_id']}: {$post['titulo']} (/{$post['slug']})</li>";
        }
        echo "</ul>";
    }
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4 style='color: #155724; margin-top: 0;'>🎉 Configuração Concluída!</h4>";
    echo "<p style='color: #155724; margin-bottom: 0;'>O anúncio da sidebar agora aparecerá apenas nos posts específicos configurados.</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<br><a href='anuncios.php'>← Voltar para Anúncios</a>";
?>
