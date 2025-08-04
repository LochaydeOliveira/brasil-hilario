<?php
echo "<h1>🔍 Debug da API de Cliques</h1>";

// Simular dados de teste
$dados_teste = [
    'anuncio_id' => 1,
    'post_id' => 0,
    'tipo_clique' => 'imagem'
];

echo "<h2>📥 Dados de Teste</h2>";
echo "<pre>" . print_r($dados_teste, true) . "</pre>";

// Teste 1: Verificar conexão com banco
echo "<h2>🔌 Teste de Conexão</h2>";
try {
    require_once 'config/database.php';
    echo "<p style='color: green;'>✅ Conexão com banco OK</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na conexão: " . $e->getMessage() . "</p>";
    exit;
}

// Teste 2: Verificar se a tabela anuncios existe
echo "<h2>📋 Teste da Tabela Anúncios</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM anuncios");
    $total = $stmt->fetch()['total'];
    echo "<p style='color: green;'>✅ Tabela anuncios existe - $total registros</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na tabela anuncios: " . $e->getMessage() . "</p>";
}

// Teste 3: Verificar se a tabela cliques_anuncios existe
echo "<h2>📋 Teste da Tabela Cliques</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cliques_anuncios");
    $total = $stmt->fetch()['total'];
    echo "<p style='color: green;'>✅ Tabela cliques_anuncios existe - $total registros</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na tabela cliques_anuncios: " . $e->getMessage() . "</p>";
}

// Teste 4: Verificar se o anúncio existe
echo "<h2>🎯 Teste de Existência do Anúncio</h2>";
try {
    $stmt = $pdo->prepare("SELECT id, titulo FROM anuncios WHERE id = ? AND ativo = 1");
    $stmt->execute([$dados_teste['anuncio_id']]);
    $anuncio = $stmt->fetch();
    
    if ($anuncio) {
        echo "<p style='color: green;'>✅ Anúncio encontrado: " . htmlspecialchars($anuncio['titulo']) . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Anúncio não encontrado (ID: " . $dados_teste['anuncio_id'] . ")</p>";
        
        // Listar anúncios disponíveis
        $stmt = $pdo->query("SELECT id, titulo FROM anuncios WHERE ativo = 1 LIMIT 5");
        $anuncios = $stmt->fetchAll();
        echo "<p>Anúncios disponíveis:</p>";
        echo "<ul>";
        foreach ($anuncios as $a) {
            echo "<li>ID: " . $a['id'] . " - " . htmlspecialchars($a['titulo']) . "</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao verificar anúncio: " . $e->getMessage() . "</p>";
}

// Teste 5: Simular inserção de clique
echo "<h2>🧪 Teste de Inserção</h2>";
try {
    $sql = "INSERT INTO cliques_anuncios (anuncio_id, post_id, tipo_clique, ip_usuario, user_agent) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
        $dados_teste['anuncio_id'],
        $dados_teste['post_id'],
        $dados_teste['tipo_clique'],
        '127.0.0.1',
        'Teste Debug'
    ]);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Inserção de clique funcionou!</p>";
        
        // Verificar se foi inserido
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM cliques_anuncios");
        $total = $stmt->fetch()['total'];
        echo "<p>Total de cliques após teste: $total</p>";
    } else {
        echo "<p style='color: red;'>❌ Inserção de clique falhou</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na inserção: " . $e->getMessage() . "</p>";
}

echo "<h2>🎯 Instruções</h2>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>1.</strong> Se todos os testes passaram, a API deve funcionar</p>";
echo "<p><strong>2.</strong> Se há erros, verifique as tabelas do banco</p>";
echo "<p><strong>3.</strong> Teste clicando em um anúncio real</p>";
echo "</div>";
?> 