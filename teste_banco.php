<?php
require_once 'config/config.php';
require_once 'includes/db.php';

echo "🔍 Teste de Conexão com Banco de Dados\n\n";

try {
    // Teste básico de conexão
    $stmt = $pdo->query("SELECT 1 as teste");
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resultado) {
        echo "✅ Conexão com banco de dados: OK\n";
    } else {
        echo "❌ Conexão com banco de dados: FALHA\n";
    }
    
    // Teste da tabela configuracoes_visuais
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM configuracoes_visuais");
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resultado) {
        echo "✅ Tabela configuracoes_visuais: OK ({$resultado['total']} registros)\n";
    } else {
        echo "❌ Tabela configuracoes_visuais: FALHA\n";
    }
    
    // Teste de inserção
    $stmt = $pdo->prepare("INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo) VALUES (?, ?, ?, ?, ?)");
    $resultado = $stmt->execute(['teste', 'teste', 'teste', '#ff0000', 'cor']);
    
    if ($resultado) {
        echo "✅ Teste de inserção: OK\n";
        
        // Limpar teste
        $pdo->exec("DELETE FROM configuracoes_visuais WHERE categoria = 'teste'");
        echo "🧹 Teste removido\n";
    } else {
        echo "❌ Teste de inserção: FALHA\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n🎯 Para testar as configurações visuais, acesse:\n";
echo "http://localhost/brasil-hilario/admin/configuracoes-visuais.php\n";
?> 