<?php
echo "<h1>🧪 Teste do Sistema Integrado</h1>";

// Teste 1: Verificar conexão com banco
try {
    require_once 'config/database.php';
    echo "<p>✅ Conexão com banco OK</p>";
    
    // Verificar se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'cliques_anuncios'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Tabela cliques_anuncios existe</p>";
    } else {
        echo "<p style='color: red;'>❌ Tabela cliques_anuncios não existe</p>";
        exit;
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro no banco: " . $e->getMessage() . "</p>";
    exit;
}

// Teste 2: Verificar AnunciosManager
try {
    require_once 'includes/AnunciosManager.php';
    $anunciosManager = new AnunciosManager($pdo);
    echo "<p>✅ AnunciosManager carregado</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro no AnunciosManager: " . $e->getMessage() . "</p>";
    exit;
}

// Teste 3: Verificar anúncios existentes
try {
    $anuncios = $anunciosManager->getAnunciosPorLocalizacao('sidebar');
    echo "<p>✅ Anúncios encontrados: " . count($anuncios) . "</p>";
    
    if (empty($anuncios)) {
        echo "<p style='color: orange;'>⚠️ Nenhum anúncio ativo encontrado</p>";
    } else {
        echo "<h3>📊 Anúncios Ativos:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Título</th><th>Localização</th><th>Status</th></tr>";
        foreach ($anuncios as $anuncio) {
            echo "<tr>";
            echo "<td>" . $anuncio['id'] . "</td>";
            echo "<td>" . htmlspecialchars($anuncio['titulo']) . "</td>";
            echo "<td>" . $anuncio['localizacao'] . "</td>";
            echo "<td>" . ($anuncio['ativo'] ? 'Ativo' : 'Inativo') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao buscar anúncios: " . $e->getMessage() . "</p>";
}

// Teste 4: Verificar cliques existentes
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cliques_anuncios");
    $totalCliques = $stmt->fetch()['total'];
    echo "<p>✅ Total de cliques registrados: $totalCliques</p>";
    
    if ($totalCliques > 0) {
        // Mostrar últimos cliques
        $stmt = $pdo->query("
            SELECT ca.*, a.titulo as anuncio_titulo 
            FROM cliques_anuncios ca 
            JOIN anuncios a ON ca.anuncio_id = a.id 
            ORDER BY ca.data_clique DESC 
            LIMIT 5
        ");
        $ultimosCliques = $stmt->fetchAll();
        
        echo "<h3>📈 Últimos Cliques:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Anúncio</th><th>Tipo</th><th>Data</th><th>IP</th></tr>";
        foreach ($ultimosCliques as $clique) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($clique['anuncio_titulo']) . "</td>";
            echo "<td>" . $clique['tipo_clique'] . "</td>";
            echo "<td>" . $clique['data_clique'] . "</td>";
            echo "<td>" . $clique['ip_usuario'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao verificar cliques: " . $e->getMessage() . "</p>";
}

// Teste 5: Simular registro de clique
echo "<h2>🧪 Teste de Registro de Clique</h2>";
if (!empty($anuncios)) {
    $primeiroAnuncio = $anuncios[0];
    echo "<p>Testando registro de clique para anúncio ID: " . $primeiroAnuncio['id'] . "</p>";
    
    try {
        $sucesso = $anunciosManager->registrarClique($primeiroAnuncio['id'], 1, 'teste');
        if ($sucesso) {
            echo "<p style='color: green;'>✅ Clique registrado com sucesso!</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao registrar clique</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erro no teste: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ Nenhum anúncio para testar</p>";
}

echo "<h2>🎯 Instruções de Teste</h2>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>1.</strong> Vá para uma página de post</p>";
echo "<p><strong>2.</strong> Clique em um anúncio nativo</p>";
echo "<p><strong>3.</strong> Abra o console do navegador (F12)</p>";
echo "<p><strong>4.</strong> Verifique se aparece: '✅ Clique registrado'</p>";
echo "<p><strong>5.</strong> Verifique no painel admin se o clique apareceu</p>";
echo "</div>";

echo "<h2>🔗 Links Úteis</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";
echo "<a href='admin/anuncios.php' style='background: #4caf50; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>⚙️ Painel Admin</a>";
echo "<a href='migrar.php' style='background: #2196f3; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>🔄 Migrar Cliques</a>";
echo "</div>";
?> 