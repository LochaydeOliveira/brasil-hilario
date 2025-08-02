<?php
echo "<h1>🔍 Verificação da Tabela de Cliques</h1>";

try {
    require_once 'config/config.php';
    require_once 'includes/db.php';
    
    // Conectar ao banco
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    echo "<p>✅ Conexão com banco de dados estabelecida</p>";
    
    // Verificar se a tabela cliques_anuncios existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'cliques_anuncios'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Tabela 'cliques_anuncios' existe</p>";
        
        // Verificar estrutura da tabela
        $stmt = $pdo->query("DESCRIBE cliques_anuncios");
        $colunas = $stmt->fetchAll();
        
        echo "<h2>Estrutura da Tabela:</h2>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th></tr>";
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
        
        // Contar cliques existentes
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM cliques_anuncios");
        $total = $stmt->fetch()['total'];
        echo "<p><strong>Total de cliques registrados:</strong> $total</p>";
        
        if ($total > 0) {
            // Mostrar últimos cliques
            $stmt = $pdo->query("SELECT * FROM cliques_anuncios ORDER BY data_clique DESC LIMIT 5");
            $ultimosCliques = $stmt->fetchAll();
            
            echo "<h2>Últimos Cliques:</h2>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Anúncio ID</th><th>Post ID</th><th>Tipo</th><th>IP</th><th>Data</th></tr>";
            foreach ($ultimosCliques as $clique) {
                echo "<tr>";
                echo "<td>" . $clique['id'] . "</td>";
                echo "<td>" . $clique['anuncio_id'] . "</td>";
                echo "<td>" . $clique['post_id'] . "</td>";
                echo "<td>" . ucfirst($clique['tipo_clique']) . "</td>";
                echo "<td>" . $clique['ip_usuario'] . "</td>";
                echo "<td>" . $clique['data_clique'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p style='color: orange;'>⚠️ Tabela 'cliques_anuncios' não existe</p>";
        echo "<p>Criando tabela...</p>";
        
        // Criar tabela cliques_anuncios
        $sql = "
        CREATE TABLE cliques_anuncios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            anuncio_id INT NOT NULL,
            post_id INT NOT NULL,
            tipo_clique ENUM('imagem', 'titulo', 'cta') NOT NULL,
            ip_usuario VARCHAR(45),
            data_clique TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_anuncio_id (anuncio_id),
            INDEX idx_post_id (post_id),
            INDEX idx_data_clique (data_clique)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        try {
            $pdo->exec($sql);
            echo "<p style='color: green;'>✅ Tabela 'cliques_anuncios' criada com sucesso!</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erro ao criar tabela: " . $e->getMessage() . "</p>";
        }
    }
    
    // Verificar se a tabela anuncios existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'anuncios'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Tabela 'anuncios' existe</p>";
        
        // Contar anúncios ativos
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM anuncios WHERE ativo = 1");
        $totalAnuncios = $stmt->fetch()['total'];
        echo "<p><strong>Total de anúncios ativos:</strong> $totalAnuncios</p>";
        
        if ($totalAnuncios > 0) {
            $stmt = $pdo->query("SELECT id, titulo, localizacao FROM anuncios WHERE ativo = 1 LIMIT 5");
            $anuncios = $stmt->fetchAll();
            
            echo "<h2>Anúncios Ativos:</h2>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Título</th><th>Localização</th></tr>";
            foreach ($anuncios as $anuncio) {
                echo "<tr>";
                echo "<td>" . $anuncio['id'] . "</td>";
                echo "<td>" . htmlspecialchars($anuncio['titulo']) . "</td>";
                echo "<td>" . htmlspecialchars($anuncio['localizacao']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: red;'>❌ Tabela 'anuncios' não existe!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<h2>Próximos Passos</h2>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px;'>";
echo "<h3>📋 O que fazer agora:</h3>";
echo "<ol>";
echo "<li>Teste clicando em um anúncio em uma página de post</li>";
echo "<li>Verifique se aparece '✅ Clique registrado com sucesso' no console</li>";
echo "<li>Acesse o painel admin para ver os cliques registrados</li>";
echo "<li>Execute este script novamente para verificar se os cliques foram salvos</li>";
echo "</ol>";
echo "</div>";

echo "<h2>Links Úteis</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";
echo "<a href='admin/anuncios.php' style='background: #4caf50; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>⚙️ Painel Admin</a>";
echo "<a href='visualizar-cliques.php' style='background: #2196f3; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>📊 Visualizar Cliques</a>";
echo "<a href='teste-final.php' style='background: #ff9800; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>🎯 Teste Final</a>";
echo "</div>";
?> 