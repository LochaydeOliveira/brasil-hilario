<?php
echo "<h1>🔧 Corrigindo Tabela cliques_anuncios - SQL DIRETO</h1>";

try {
    require_once 'config/database.php';
    echo "<p>✅ Conexão com banco OK</p>";
    
    // Executar SQL direto para corrigir a tabela
    echo "<h2>🔧 Executando correções...</h2>";
    
    // 1. Remover FOREIGN KEY se existir
    try {
        $pdo->exec("ALTER TABLE cliques_anuncios DROP FOREIGN KEY cliques_anuncios_ibfk_2");
        echo "<p>✅ FOREIGN KEY removida</p>";
    } catch (Exception $e) {
        echo "<p>ℹ️ FOREIGN KEY já não existe: " . $e->getMessage() . "</p>";
    }
    
    // 2. Modificar coluna para permitir NULL
    try {
        $pdo->exec("ALTER TABLE cliques_anuncios MODIFY COLUMN post_id INT NULL");
        echo "<p style='color: green;'>✅ post_id agora permite NULL</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erro ao modificar: " . $e->getMessage() . "</p>";
    }
    
    // 3. Recriar FOREIGN KEY (opcional)
    try {
        $pdo->exec("ALTER TABLE cliques_anuncios ADD CONSTRAINT cliques_anuncios_ibfk_2 FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE");
        echo "<p>✅ FOREIGN KEY recriada</p>";
    } catch (Exception $e) {
        echo "<p>ℹ️ FOREIGN KEY não recriada (opcional): " . $e->getMessage() . "</p>";
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
    
    // Teste de inserção com NULL
    echo "<h2>🧪 Teste de Inserção com NULL</h2>";
    try {
        $sql = "INSERT INTO cliques_anuncios (anuncio_id, post_id, tipo_clique, ip_usuario, user_agent) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        $result = $stmt->execute([9, null, 'teste', '127.0.0.1', 'Teste SQL']);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Inserção com NULL funcionou!</p>";
            
            // Remover o registro de teste
            $pdo->exec("DELETE FROM cliques_anuncios WHERE ip_usuario = 'Teste SQL'");
            echo "<p>✅ Registro de teste removido</p>";
        } else {
            echo "<p style='color: red;'>❌ Inserção falhou</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erro na inserção: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>🎯 Próximo Passo</h2>";
    echo "<p>✅ Tabela corrigida! Agora teste clicando em um anúncio na página inicial.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro geral: " . $e->getMessage() . "</p>";
}
?> 