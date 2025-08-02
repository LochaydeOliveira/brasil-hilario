<?php
require_once 'config/config.php';
require_once 'includes/db.php';
require_once 'includes/AnunciosManager.php';

echo "<h1>Teste do Sistema de Anúncios Nativos</h1>";

try {
    $anunciosManager = new AnunciosManager($pdo);
    
    // Teste 1: Verificar se as tabelas existem
    echo "<h2>1. Verificação das Tabelas</h2>";
    
    $tables = ['anuncios', 'anuncios_posts', 'cliques_anuncios'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $exists = $stmt->fetch();
        echo "<p><strong>$table:</strong> " . ($exists ? "✅ Existe" : "❌ Não existe") . "</p>";
    }
    
    // Teste 2: Verificar anúncios existentes
    echo "<h2>2. Anúncios Existentes</h2>";
    $anuncios = $anunciosManager->getAllAnunciosComStats();
    echo "<p>Total de anúncios: " . count($anuncios) . "</p>";
    
    if (!empty($anuncios)) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Título</th><th>Localização</th><th>Ativo</th><th>Total Cliques</th></tr>";
        foreach ($anuncios as $anuncio) {
            echo "<tr>";
            echo "<td>" . $anuncio['id'] . "</td>";
            echo "<td>" . htmlspecialchars($anuncio['titulo']) . "</td>";
            echo "<td>" . $anuncio['localizacao'] . "</td>";
            echo "<td>" . ($anuncio['ativo'] ? 'Sim' : 'Não') . "</td>";
            echo "<td>" . $anuncio['total_cliques'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Teste 3: Verificar cliques existentes
    echo "<h2>3. Cliques Registrados</h2>";
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM cliques_anuncios");
    $stmt->execute();
    $total_cliques = $stmt->fetch()['total'];
    echo "<p>Total de cliques registrados: $total_cliques</p>";
    
    if ($total_cliques > 0) {
        $stmt = $pdo->prepare("
            SELECT ca.*, a.titulo as anuncio_titulo, p.titulo as post_titulo 
            FROM cliques_anuncios ca 
            JOIN anuncios a ON ca.anuncio_id = a.id 
            JOIN posts p ON ca.post_id = p.id 
            ORDER BY ca.data_clique DESC 
            LIMIT 10
        ");
        $stmt->execute();
        $cliques = $stmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Data</th><th>Anúncio</th><th>Post</th><th>Tipo</th><th>IP</th></tr>";
        foreach ($cliques as $clique) {
            echo "<tr>";
            echo "<td>" . $clique['data_clique'] . "</td>";
            echo "<td>" . htmlspecialchars($clique['anuncio_titulo']) . "</td>";
            echo "<td>" . htmlspecialchars($clique['post_titulo']) . "</td>";
            echo "<td>" . $clique['tipo_clique'] . "</td>";
            echo "<td>" . $clique['ip_usuario'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Teste 4: Simular registro de clique
    echo "<h2>4. Teste de Registro de Clique</h2>";
    if (!empty($anuncios)) {
        $anuncio_teste = $anuncios[0];
        $post_id = 1; // Assumindo que existe um post com ID 1
        
        echo "<p>Testando registro de clique para anúncio ID: " . $anuncio_teste['id'] . "</p>";
        
        $sucesso = $anunciosManager->registrarClique($anuncio_teste['id'], $post_id, 'imagem');
        echo "<p>Resultado do teste: " . ($sucesso ? "✅ Sucesso" : "❌ Falha") . "</p>";
    }
    
    // Teste 5: Verificar posts existentes
    echo "<h2>5. Posts Disponíveis</h2>";
    $stmt = $pdo->prepare("SELECT id, titulo FROM posts ORDER BY id LIMIT 10");
    $stmt->execute();
    $posts = $stmt->fetchAll();
    echo "<p>Total de posts: " . count($posts) . "</p>";
    
    if (!empty($posts)) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Título</th></tr>";
        foreach ($posts as $post) {
            echo "<tr>";
            echo "<td>" . $post['id'] . "</td>";
            echo "<td>" . htmlspecialchars($post['titulo']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
}
?> 