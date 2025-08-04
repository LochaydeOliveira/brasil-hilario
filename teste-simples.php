<?php
echo "<h1>🧪 Teste Simples do Sistema</h1>";

// Teste 1: Verificar se há anúncios
try {
    require_once 'config/database.php';
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM anuncios WHERE ativo = 1");
    $total = $stmt->fetch()['total'];
    echo "<p>✅ Anúncios ativos: $total</p>";
    
    if ($total > 0) {
        $stmt = $pdo->query("SELECT id, titulo FROM anuncios WHERE ativo = 1 LIMIT 3");
        $anuncios = $stmt->fetchAll();
        echo "<p>Anúncios disponíveis:</p>";
        echo "<ul>";
        foreach ($anuncios as $a) {
            echo "<li>ID: " . $a['id'] . " - " . htmlspecialchars($a['titulo']) . "</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<h2>🎯 Teste Manual</h2>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>1.</strong> Vá para uma página de post</p>";
echo "<p><strong>2.</strong> Clique em um anúncio</p>";
echo "<p><strong>3.</strong> Abra o console (F12)</p>";
echo "<p><strong>4.</strong> Deve aparecer: '✅ Clique registrado'</p>";
echo "</div>";
?> 