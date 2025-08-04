<?php
echo "<h1>🔍 Debug do Sistema de Cliques - V2</h1>";

// Teste 1: Verificar dados recebidos
echo "<h2>📥 Dados Recebidos</h2>";
$input = file_get_contents('php://input');
echo "<p>Raw input: " . htmlspecialchars($input) . "</p>";

$dados = json_decode($input, true);
echo "<p>Dados decodificados: " . print_r($dados, true) . "</p>";

// Teste 2: Verificar conexão
echo "<h2>🔌 Teste de Conexão</h2>";
try {
    require_once 'config/database.php';
    echo "<p>✅ Conexão com banco OK</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na conexão: " . $e->getMessage() . "</p>";
    exit;
}

// Teste 3: Verificar AnunciosManager
echo "<h2>📦 Teste do AnunciosManager</h2>";
try {
    require_once 'includes/AnunciosManager.php';
    $anunciosManager = new AnunciosManager($pdo);
    echo "<p>✅ AnunciosManager carregado</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro no AnunciosManager: " . $e->getMessage() . "</p>";
    exit;
}

// Teste 4: Verificar se o anúncio existe
echo "<h2>🎯 Teste de Existência do Anúncio</h2>";
if ($dados && isset($dados['anuncio_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT id, titulo FROM anuncios WHERE id = ?");
        $stmt->execute([$dados['anuncio_id']]);
        $anuncio = $stmt->fetch();
        
        if ($anuncio) {
            echo "<p>✅ Anúncio encontrado: " . htmlspecialchars($anuncio['titulo']) . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Anúncio não encontrado (ID: " . $dados['anuncio_id'] . ")</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erro ao verificar anúncio: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ Nenhum anuncio_id fornecido</p>";
}

// Teste 5: Verificar estrutura da tabela cliques_anuncios
echo "<h2>📋 Estrutura da Tabela cliques_anuncios</h2>";
try {
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
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao verificar estrutura: " . $e->getMessage() . "</p>";
}

// Teste 6: Testar método registrarClique
echo "<h2>🔧 Teste do Método registrarClique</h2>";
if ($dados && isset($dados['anuncio_id'])) {
    try {
        $sucesso = $anunciosManager->registrarClique(
            $dados['anuncio_id'],
            $dados['post_id'] ?? 0,
            $dados['tipo_clique'] ?? 'imagem'
        );
        
        if ($sucesso) {
            echo "<p style='color: green;'>✅ Método registrarClique funcionou!</p>";
        } else {
            echo "<p style='color: red;'>❌ Método registrarClique falhou</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erro no método: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ Nenhum dado para testar</p>";
}

// Teste 7: Verificar logs de erro do PHP
echo "<h2>📝 Logs de Erro do PHP</h2>";
$errorLog = error_get_last();
if ($errorLog) {
    echo "<p style='color: red;'>Último erro: " . print_r($errorLog, true) . "</p>";
} else {
    echo "<p>✅ Nenhum erro encontrado</p>";
}

// Teste 8: Verificar se há cliques já registrados
echo "<h2>📊 Cliques Já Registrados</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cliques_anuncios");
    $total = $stmt->fetch()['total'];
    echo "<p>✅ Total de cliques registrados: $total</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao contar cliques: " . $e->getMessage() . "</p>";
}

echo "<h2>🎯 Próximos Passos</h2>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>1.</strong> Execute este script via POST com dados JSON</p>";
echo "<p><strong>2.</strong> Verifique se há erros na estrutura da tabela</p>";
echo "<p><strong>3.</strong> Compare os resultados dos testes</p>";
echo "</div>";
?> 