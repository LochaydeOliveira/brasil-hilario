<?php
require_once '../config/config.php';
require_once '../config/database_unified.php';
require_once 'includes/auth.php';

check_login();

$dbManager = DatabaseManager::getInstance();

echo "<h2>🔧 Corrigindo Configuração do Anúncio</h2>";

try {
    // 1. Verificar configuração atual
    echo "<h3>1. 📋 Configuração atual:</h3>";
    
    $configuracao_atual = $dbManager->query("
        SELECT ap.anuncio_id, ap.post_id, a.titulo, p.titulo as post_titulo
        FROM anuncios_posts ap
        JOIN anuncios a ON ap.anuncio_id = a.id
        JOIN posts p ON ap.post_id = p.id
        WHERE a.localizacao = 'sidebar' AND a.ativo = 1
        ORDER BY ap.post_id
    ");
    
    if (empty($configuracao_atual)) {
        echo "<p style='color: red;'>❌ Nenhuma configuração encontrada!</p>";
    } else {
        echo "<ul>";
        foreach ($configuracao_atual as $config) {
            echo "<li>Anúncio ID {$config['anuncio_id']} ({$config['titulo']}) → Post ID {$config['post_id']} ({$config['post_titulo']})</li>";
        }
        echo "</ul>";
    }
    
    // 2. Adicionar configuração para post 71
    echo "<h3>2. 🔧 Adicionando configuração para post 71:</h3>";
    
    $anuncio_id = 23; // Brinquedo Star Plic
    $post_id = 71;
    
    // Verificar se já existe
    $ja_existe = $dbManager->queryOne("
        SELECT COUNT(*) as total
        FROM anuncios_posts ap
        WHERE ap.anuncio_id = ? AND ap.post_id = ?
    ", [$anuncio_id, $post_id]);
    
    if ($ja_existe['total'] == 0) {
        // Adicionar configuração
        $dbManager->execute("
            INSERT INTO anuncios_posts (anuncio_id, post_id) 
            VALUES (?, ?)
        ", [$anuncio_id, $post_id]);
        
        echo "<p style='color: green;'>✅ Configuração adicionada: Anúncio ID $anuncio_id → Post ID $post_id</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Configuração já existe</p>";
    }
    
    // 3. Verificar resultado final
    echo "<h3>3. ✅ Configuração final:</h3>";
    
    $configuracao_final = $dbManager->query("
        SELECT ap.anuncio_id, ap.post_id, a.titulo, p.titulo as post_titulo
        FROM anuncios_posts ap
        JOIN anuncios a ON ap.anuncio_id = a.id
        JOIN posts p ON ap.post_id = p.id
        WHERE a.localizacao = 'sidebar' AND a.ativo = 1
        ORDER BY ap.post_id
    ");
    
    echo "<ul>";
    foreach ($configuracao_final as $config) {
        echo "<li>Anúncio ID {$config['anuncio_id']} ({$config['titulo']}) → Post ID {$config['post_id']} ({$config['post_titulo']})</li>";
    }
    echo "</ul>";
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4 style='color: #155724; margin-top: 0;'>🎉 Configuração Corrigida!</h4>";
    echo "<p style='color: #155724; margin-bottom: 0;'>Agora teste novamente o post ID 71.</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<br><a href='anuncios.php'>← Voltar para Anúncios</a>";
?>
