<?php
echo "<h1>🔧 Corrigindo Tabela cliques_anuncios</h1>";

try {
    require_once 'config/database.php';
    echo "<p>✅ Conexão com banco OK</p>";
    
    // Verificar estrutura atual
    echo "<h2>📋 Estrutura Atual</h2>";
    $stmt = $pdo->query("DESCRIBE cliques_anuncios");
    $colunas = $stmt->fetchAll();
    
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
    
    // Verificar se post_id já permite NULL
    $post_id_null = false;
    foreach ($colunas as $coluna) {
        if ($coluna['Field'] === 'post_id' && $coluna['Null'] === 'YES') {
            $post_id_null = true;
            break;
        }
    }
    
    if ($post_id_null) {
        echo "<p style='color: green;'>✅ post_id já permite NULL</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ post_id não permite NULL - corrigindo...</p>";
        
        // Remover FOREIGN KEY primeiro
        try {
            $pdo->exec("ALTER TABLE cliques_anuncios DROP FOREIGN KEY cliques_anuncios_ibfk_2");
            echo "<p>✅ FOREIGN KEY removida</p>";
        } catch (Exception $e) {
            echo "<p>ℹ️ FOREIGN KEY já não existe ou erro: " . $e->getMessage() . "</p>";
        }
        
        // Modificar coluna para permitir NULL
        try {
            $pdo->exec("ALTER TABLE cliques_anuncios MODIFY post_id INT NULL");
            echo "<p style='color: green;'>✅ post_id agora permite NULL</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erro ao modificar: " . $e->getMessage() . "</p>";
        }
        
        // Recriar FOREIGN KEY (opcional)
        try {
            $pdo->exec("ALTER TABLE cliques_anuncios ADD CONSTRAINT cliques_anuncios_ibfk_2 FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE");
            echo "<p>✅ FOREIGN KEY recriada</p>";
        } catch (Exception $e) {
            echo "<p>ℹ️ FOREIGN KEY não recriada (opcional): " . $e->getMessage() . "</p>";
        }
    }
    
    // Verificar estrutura final
    echo "<h2>📋 Estrutura Final</h2>";
    $stmt = $pdo->query("DESCRIBE cliques_anuncios");
    $colunas = $stmt->fetchAll();
    
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
    
    echo "<h2>🧪 Teste da API</h2>";
    echo "<p><a href='teste-api.php' target='_blank'>Clique aqui para testar a API</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro geral: " . $e->getMessage() . "</p>";
}
?> 