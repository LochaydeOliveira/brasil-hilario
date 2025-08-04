<?php
echo "<h1>🔍 Verificação da Tabela cliques_anuncios</h1>";

try {
    require_once 'config/database.php';
    echo "<p>✅ Conexão com banco OK</p>";
    
    // Verificar se há posts na base
    echo "<h2>📝 Verificação de Posts</h2>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM posts");
    $total_posts = $stmt->fetch()['total'];
    echo "<p>Total de posts: $total_posts</p>";
    
    if ($total_posts > 0) {
        $stmt = $pdo->query("SELECT id, titulo FROM posts WHERE status = 'publicado' ORDER BY id DESC LIMIT 5");
        $posts = $stmt->fetchAll();
        echo "<p>Posts publicados:</p>";
        echo "<ul>";
        foreach ($posts as $post) {
            echo "<li>ID: " . $post['id'] . " - " . htmlspecialchars($post['titulo']) . "</li>";
        }
        echo "</ul>";
    }
    
    // Verificar se há anúncios na base
    echo "<h2>📢 Verificação de Anúncios</h2>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM anuncios WHERE ativo = 1");
    $total_anuncios = $stmt->fetch()['total'];
    echo "<p>Anúncios ativos: $total_anuncios</p>";
    
    if ($total_anuncios > 0) {
        $stmt = $pdo->query("SELECT id, titulo FROM anuncios WHERE ativo = 1 LIMIT 5");
        $anuncios = $stmt->fetchAll();
        echo "<p>Anúncios disponíveis:</p>";
        echo "<ul>";
        foreach ($anuncios as $anuncio) {
            echo "<li>ID: " . $anuncio['id'] . " - " . htmlspecialchars($anuncio['titulo']) . "</li>";
        }
        echo "</ul>";
    }
    
    // Verificar se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'cliques_anuncios'");
    $tabela_existe = $stmt->fetch();
    
    if ($tabela_existe) {
        echo "<p style='color: green;'>✅ Tabela cliques_anuncios existe</p>";
        
        // Verificar estrutura da tabela
        $stmt = $pdo->query("DESCRIBE cliques_anuncios");
        $colunas = $stmt->fetchAll();
        
        echo "<h2>📋 Estrutura da Tabela</h2>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($colunas as $coluna) {
            echo "<tr>";
            echo "<td>" . $coluna['Field'] . "</td>";
            echo "<td>" . $coluna['Type'] . "</td>";
            echo "<td>" . $coluna['Null'] . "</td>";
            echo "<td>" . $coluna['Key'] . "</td>";
            echo "<td>" . $coluna['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Verificar se há registros
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM cliques_anuncios");
        $total = $stmt->fetch()['total'];
        echo "<p>Total de cliques registrados: $total</p>";
        
        // Testar inserção
        echo "<h2>🧪 Teste de Inserção</h2>";
        try {
            // Buscar um post válido
            $stmt = $pdo->query("SELECT id FROM posts WHERE status = 'publicado' ORDER BY id DESC LIMIT 1");
            $post = $stmt->fetch();
            
            if ($post) {
                $sql = "INSERT INTO cliques_anuncios (anuncio_id, post_id, tipo_clique, ip_usuario, user_agent) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                
                $result = $stmt->execute([1, $post['id'], 'teste', '127.0.0.1', 'Teste']);
                
                if ($result) {
                    echo "<p style='color: green;'>✅ Inserção funcionou!</p>";
                    
                    // Remover o registro de teste
                    $pdo->exec("DELETE FROM cliques_anuncios WHERE ip_usuario = 'Teste'");
                    echo "<p>✅ Registro de teste removido</p>";
                } else {
                    echo "<p style='color: red;'>❌ Inserção falhou</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ Nenhum post válido encontrado para teste</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erro na inserção: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Tabela cliques_anuncios NÃO existe</p>";
        
        // Criar a tabela
        echo "<h2>🔧 Criando Tabela</h2>";
        $sql = "CREATE TABLE cliques_anuncios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            anuncio_id INT NOT NULL,
            post_id INT NOT NULL,
            tipo_clique ENUM('imagem', 'titulo', 'cta') NOT NULL,
            ip_usuario VARCHAR(45),
            user_agent TEXT,
            data_clique TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        try {
            $pdo->exec($sql);
            echo "<p style='color: green;'>✅ Tabela criada com sucesso!</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erro ao criar tabela: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro geral: " . $e->getMessage() . "</p>";
}
?> 