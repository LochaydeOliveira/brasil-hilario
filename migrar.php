<?php
echo "<h1>🔄 Migrar Cliques para o Banco de Dados</h1>";

try {
    require_once 'config/config.php';
    
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
    if ($stmt->rowCount() == 0) {
        echo "<p>Criando tabela cliques_anuncios...</p>";
        
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
        
        $pdo->exec($sql);
        echo "<p>✅ Tabela cliques_anuncios criada</p>";
    } else {
        echo "<p>✅ Tabela cliques_anuncios já existe</p>";
    }
    
    // Verificar arquivo de log
    $logFile = 'logs/cliques_anuncios.log';
    
    if (!file_exists($logFile)) {
        echo "<p style='color: orange;'>⚠️ Arquivo de log não encontrado</p>";
        echo "<p>Não há cliques para migrar.</p>";
        exit;
    }
    
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", trim($logContent));
    $cliques = [];
    
    foreach ($lines as $line) {
        if (!empty($line)) {
            $dados = json_decode($line, true);
            if ($dados) {
                $cliques[] = $dados;
            }
        }
    }
    
    if (empty($cliques)) {
        echo "<p style='color: orange;'>⚠️ Nenhum clique encontrado no arquivo de log</p>";
        exit;
    }
    
    echo "<p><strong>Total de cliques encontrados:</strong> " . count($cliques) . "</p>";
    
    // Verificar cliques já existentes no banco
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cliques_anuncios");
    $totalBanco = $stmt->fetch()['total'];
    echo "<p><strong>Cliques já no banco:</strong> $totalBanco</p>";
    
    // Migrar cliques
    $migrados = 0;
    $duplicados = 0;
    
    foreach ($cliques as $clique) {
        // Verificar se já existe no banco (evitar duplicatas)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total FROM cliques_anuncios 
            WHERE anuncio_id = ? AND post_id = ? AND tipo_clique = ? AND ip_usuario = ? AND data_clique = ?
        ");
        
        $stmt->execute([
            $clique['anuncio_id'],
            $clique['post_id'],
            $clique['tipo_clique'],
            $clique['ip_usuario'],
            $clique['data_clique']
        ]);
        
        $existe = $stmt->fetch()['total'] > 0;
        
        if (!$existe) {
            // Inserir no banco
            $stmt = $pdo->prepare("
                INSERT INTO cliques_anuncios (anuncio_id, post_id, tipo_clique, ip_usuario, data_clique) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $clique['anuncio_id'],
                $clique['post_id'],
                $clique['tipo_clique'],
                $clique['ip_usuario'],
                $clique['data_clique']
            ]);
            
            if ($result) {
                $migrados++;
            }
        } else {
            $duplicados++;
        }
    }
    
    echo "<h2>📊 Resultado da Migração</h2>";
    echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px;'>";
    echo "<p><strong>✅ Cliques migrados:</strong> $migrados</p>";
    echo "<p><strong>⚠️ Duplicados ignorados:</strong> $duplicados</p>";
    echo "<p><strong>📈 Total no banco agora:</strong> " . ($totalBanco + $migrados) . "</p>";
    echo "</div>";
    
    // Mostrar estatísticas
    if ($migrados > 0) {
        echo "<h2>📈 Estatísticas dos Cliques Migrados</h2>";
        
        // Cliques por anúncio
        $stmt = $pdo->query("
            SELECT anuncio_id, COUNT(*) as total 
            FROM cliques_anuncios 
            GROUP BY anuncio_id 
            ORDER BY total DESC 
            LIMIT 5
        ");
        $topAnuncios = $stmt->fetchAll();
        
        echo "<h3>🏆 Top Anúncios Mais Clicados:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
        echo "<tr><th>Anúncio ID</th><th>Total de Cliques</th></tr>";
        foreach ($topAnuncios as $anuncio) {
            echo "<tr>";
            echo "<td>" . $anuncio['anuncio_id'] . "</td>";
            echo "<td>" . $anuncio['total'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Cliques por tipo
        $stmt = $pdo->query("
            SELECT tipo_clique, COUNT(*) as total 
            FROM cliques_anuncios 
            GROUP BY tipo_clique
        ");
        $tiposClique = $stmt->fetchAll();
        
        echo "<h3>🎯 Cliques por Tipo:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Tipo</th><th>Total</th></tr>";
        foreach ($tiposClique as $tipo) {
            echo "<tr>";
            echo "<td>" . ucfirst($tipo['tipo_clique']) . "</td>";
            echo "<td>" . $tipo['total'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Opção para limpar arquivo de log
    echo "<h2>🧹 Limpar Arquivo de Log</h2>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
    echo "<p><strong>⚠️ Atenção:</strong> Depois de migrar os cliques, você pode limpar o arquivo de log para evitar duplicatas futuras.</p>";
    echo "<a href='?action=clear_log' onclick='return confirm(\"Tem certeza que deseja limpar o arquivo de log?\")' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🗑️ Limpar Arquivo de Log</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

// Processar limpeza do log
if (isset($_GET['action']) && $_GET['action'] === 'clear_log') {
    $logFile = 'logs/cliques_anuncios.log';
    if (file_put_contents($logFile, '') !== false) {
        echo "<script>alert('Arquivo de log limpo com sucesso!'); window.location.href = 'migrar.php';</script>";
    } else {
        echo "<script>alert('Erro ao limpar o arquivo de log!');</script>";
    }
}

echo "<h2>🔗 Links Úteis</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";
echo "<a href='admin/anuncios.php' style='background: #4caf50; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>⚙️ Painel Admin</a>";
echo "<a href='visualizar-cliques.php' style='background: #2196f3; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>📊 Visualizar Cliques</a>";
echo "<a href='verificar-tabela-cliques.php' style='background: #ff9800; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>🔍 Verificar Tabela</a>";
echo "</div>";
?> 