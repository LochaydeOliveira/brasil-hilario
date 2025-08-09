<?php
require_once 'includes/db.php';

try {
    // Verificar todas as configurações de fontes
    $stmt = $pdo->prepare("
        SELECT categoria, elemento, propriedade, valor, ativo
        FROM configuracoes_visuais 
        WHERE categoria = 'fontes' 
        ORDER BY elemento, propriedade
    ");
    $stmt->execute();
    
    echo "<h2>Configurações de Fontes no Banco de Dados:</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Categoria</th><th>Elemento</th><th>Propriedade</th><th>Valor</th><th>Ativo</th></tr>";
    
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['categoria']) . "</td>";
        echo "<td>" . htmlspecialchars($row['elemento']) . "</td>";
        echo "<td>" . htmlspecialchars($row['propriedade']) . "</td>";
        echo "<td>" . htmlspecialchars($row['valor']) . "</td>";
        echo "<td>" . ($row['ativo'] ? 'Sim' : 'Não') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Verificar especificamente as configurações de títulos de conteúdo
    echo "<h3>Configurações de Títulos de Conteúdo:</h3>";
    $stmt_titulos = $pdo->prepare("
        SELECT elemento, propriedade, valor, ativo
        FROM configuracoes_visuais 
        WHERE categoria = 'fontes' AND elemento LIKE 'titulo_conteudo%'
        ORDER BY elemento, propriedade
    ");
    $stmt_titulos->execute();
    
    if ($stmt_titulos->rowCount() > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Elemento</th><th>Propriedade</th><th>Valor</th><th>Ativo</th></tr>";
        
        while ($row = $stmt_titulos->fetch()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['elemento']) . "</td>";
            echo "<td>" . htmlspecialchars($row['propriedade']) . "</td>";
            echo "<td>" . htmlspecialchars($row['valor']) . "</td>";
            echo "<td>" . ($row['ativo'] ? 'Sim' : 'Não') . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p style='color: red;'>Nenhuma configuração encontrada para títulos de conteúdo!</p>";
    }
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?> 