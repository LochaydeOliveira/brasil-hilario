<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    // Verificar se a coluna marca existe
    $sql = "SHOW COLUMNS FROM grupos_anuncios LIKE 'marca'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
        echo "✅ Coluna 'marca' existe na tabela grupos_anuncios\n";
        echo "Tipo: " . $result['Type'] . "\n";
        echo "Padrão: " . $result['Default'] . "\n";
    } else {
        echo "❌ Coluna 'marca' NÃO existe na tabela grupos_anuncios\n";
        echo "Execute o SQL: ALTER TABLE grupos_anuncios ADD COLUMN marca ENUM('', 'shopee', 'amazon') DEFAULT '' AFTER layout;\n";
    }
    
    // Verificar estrutura atual da tabela
    echo "\n📋 Estrutura atual da tabela grupos_anuncios:\n";
    $sql = "DESCRIBE grupos_anuncios";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?> 