<?php
require_once 'config/config.php';
require_once 'includes/db.php';

try {
    // Adicionar coluna layout
    $sql1 = "ALTER TABLE anuncios ADD COLUMN layout ENUM('carrossel', 'grade') DEFAULT 'carrossel' AFTER localizacao";
    $stmt1 = $pdo->prepare($sql1);
    $stmt1->execute();
    echo "✅ Coluna 'layout' adicionada com sucesso!\n";
    
    // Atualizar anúncios existentes
    $sql2 = "UPDATE anuncios SET layout = 'carrossel' WHERE layout IS NULL";
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->execute();
    echo "✅ Anúncios existentes atualizados para usar 'carrossel' como padrão!\n";
    
    echo "\n🎉 Script executado com sucesso! Agora você pode usar os layouts de anúncios.\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?> 