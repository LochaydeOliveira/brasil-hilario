<?php
echo "<h1>🧪 Teste das Seções de Posts</h1>";

try {
    require_once 'includes/db.php';
    require_once 'config/config.php';
    require_once 'config/search.php';
    
    echo "<p>✅ Conexões carregadas</p>";
    
    // Teste 1: Verificar se há posts publicados
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM posts WHERE publicado = 1");
    $totalPosts = $stmt->fetch()['total'];
    echo "<p>✅ Total de posts publicados: $totalPosts</p>";
    
    if ($totalPosts == 0) {
        echo "<p style='color: orange;'>⚠️ Nenhum post publicado encontrado</p>";
        exit;
    }
    
    // Teste 2: Buscar um post para testar
    $stmt = $pdo->query("
        SELECT p.*, c.nome as categoria_nome, c.id as categoria_id
        FROM posts p 
        JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.publicado = 1 
        ORDER BY p.data_publicacao DESC 
        LIMIT 1
    ");
    $post = $stmt->fetch();
    
    if (!$post) {
        echo "<p style='color: red;'>❌ Nenhum post encontrado</p>";
        exit;
    }
    
    echo "<p>✅ Post de teste: " . htmlspecialchars($post['titulo']) . "</p>";
    echo "<p>✅ Categoria: " . htmlspecialchars($post['categoria_nome']) . "</p>";
    
    // Teste 3: Buscar posts relacionados
    $stmt_related = $pdo->prepare("
        SELECT p.id, p.titulo, p.slug, p.imagem_destacada, c.nome as categoria_nome
        FROM posts p 
        JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.categoria_id = ? AND p.id != ? AND p.publicado = 1
        ORDER BY p.data_publicacao DESC 
        LIMIT 4
    ");
    $stmt_related->execute([$post['categoria_id'], $post['id']]);
    $related_posts = $stmt_related->fetchAll();
    
    echo "<p>✅ Posts relacionados encontrados: " . count($related_posts) . "</p>";
    
    if (!empty($related_posts)) {
        echo "<h3>📋 Posts Relacionados:</h3>";
        echo "<ul>";
        foreach ($related_posts as $rp) {
            echo "<li>" . htmlspecialchars($rp['titulo']) . "</li>";
        }
        echo "</ul>";
    }
    
    // Teste 4: Buscar últimas notícias
    $stmt_latest = $pdo->prepare("
        SELECT p.id, p.titulo, p.slug, p.imagem_destacada, c.nome as categoria_nome
        FROM posts p 
        JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.publicado = 1 AND p.id != ?
        ORDER BY p.data_publicacao DESC 
        LIMIT 4
    ");
    $stmt_latest->execute([$post['id']]);
    $latest_posts = $stmt_latest->fetchAll();
    
    echo "<p>✅ Últimas notícias encontradas: " . count($latest_posts) . "</p>";
    
    if (!empty($latest_posts)) {
        echo "<h3>📋 Últimas Notícias:</h3>";
        echo "<ul>";
        foreach ($latest_posts as $lp) {
            echo "<li>" . htmlspecialchars($lp['titulo']) . "</li>";
        }
        echo "</ul>";
    }
    
    // Teste 5: Simular a função buildPostsSectionHtml
    echo "<h2>🧪 Teste da Função buildPostsSectionHtml</h2>";
    
    if (!empty($related_posts)) {
        $section_html = '<section class="related-posts-block my-5">';
        $section_html .= '<h4 class="related-posts-title">Leia Também</h4>';
        $section_html .= '<div class="row">';
        
        foreach ($related_posts as $p) {
            $post_url = BLOG_URL . '/post/' . htmlspecialchars($p['slug']);
            $image_path = !empty($p['imagem_destacada']) ? BLOG_URL . '/uploads/images/' . htmlspecialchars($p['imagem_destacada']) : BLOG_URL . '/assets/img/logo-brasil-hilario-para-og.png';
            
            $section_html .= '<div class="col-lg-3 col-md-6 mb-4">';
            $section_html .= '<a href="' . $post_url . '" class="related-post-link">';
            $section_html .= '<div class="card h-100 related-post-card">';
            $section_html .= '<img src="' . $image_path . '" class="related-post-img" alt="' . htmlspecialchars($p['titulo']) . '">';
            $section_html .= '<div class="pad-01 d-flex flex-column ">';
            $section_html .= '<h6 class="card-title related-post-title mt-auto">' . htmlspecialchars($p['titulo']) . '</h6>';
            $section_html .= '<div><span class="badge mb-2">' . htmlspecialchars($p['categoria_nome']) . '</span></div>';
            $section_html .= '</div>';
            $section_html .= '</div>';
            $section_html .= '</a>';
            $section_html .= '</div>';
        }
        
        $section_html .= '</div>';
        $section_html .= '</section>';
        
        echo "<p style='color: green;'>✅ HTML da seção gerado com sucesso!</p>";
        echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
        echo "<p><strong>HTML gerado:</strong></p>";
        echo "<pre>" . htmlspecialchars(substr($section_html, 0, 500)) . "...</pre>";
        echo "</div>";
    }
    
    echo "<h2>🎯 Instruções de Teste</h2>";
    echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px;'>";
    echo "<p><strong>1.</strong> Vá para uma página de post</p>";
    echo "<p><strong>2.</strong> Verifique se aparecem as seções 'Leia Também' e 'Últimas do Portal'</p>";
    echo "<p><strong>3.</strong> As seções devem aparecer no meio do conteúdo do post</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}
?> 