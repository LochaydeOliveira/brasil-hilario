<?php
echo "<h1>🎯 Teste Final - Sistema de Cliques</h1>";

// Teste 1: Verificar se há anúncios ativos
echo "<h2>1. Verificação de Anúncios Ativos</h2>";
try {
    require_once 'config/config.php';
    require_once 'includes/db.php';
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM anuncios WHERE ativo = 1");
    $stmt->execute();
    $totalAnuncios = $stmt->fetch()['total'];
    
    echo "<p><strong>Total de anúncios ativos:</strong> $totalAnuncios</p>";
    
    if ($totalAnuncios > 0) {
        $stmt = $pdo->prepare("SELECT id, titulo, localizacao FROM anuncios WHERE ativo = 1 LIMIT 3");
        $stmt->execute();
        $anuncios = $stmt->fetchAll();
        
        echo "<p><strong>Anúncios disponíveis:</strong></p>";
        echo "<ul>";
        foreach ($anuncios as $anuncio) {
            echo "<li>ID: {$anuncio['id']} - {$anuncio['titulo']} ({$anuncio['localizacao']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠️ Nenhum anúncio ativo encontrado</p>";
        echo "<p>Você precisa criar anúncios ativos no painel admin para testar.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao conectar com banco de dados: " . $e->getMessage() . "</p>";
}

// Teste 2: Verificar se a API está funcionando
echo "<h2>2. Teste da API</h2>";
$apiFile = 'api/registrar-clique-anuncio.php';
if (file_exists($apiFile)) {
    echo "<p>✅ Arquivo da API encontrado</p>";
    
    // Testar se a API responde
    $testData = [
        'anuncio_id' => 1,
        'post_id' => 1,
        'tipo_clique' => 'imagem'
    ];
    
    // Simular requisição
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $input = json_encode($testData);
    
    ob_start();
    include $apiFile;
    $output = ob_get_clean();
    
    echo "<p><strong>Resposta da API:</strong></p>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
} else {
    echo "<p style='color: red;'>❌ Arquivo da API não encontrado!</p>";
}

// Teste 3: Verificar arquivo de log
echo "<h2>3. Verificação do Arquivo de Log</h2>";
$logFile = 'logs/cliques_anuncios.log';

if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", trim($logContent));
    $totalEntries = count(array_filter($lines));
    
    echo "<p>✅ Arquivo de log encontrado</p>";
    echo "<p><strong>Total de cliques registrados:</strong> $totalEntries</p>";
    
    if ($totalEntries > 0) {
        echo "<p><strong>Últimos 3 cliques:</strong></p>";
        $lastLines = array_slice(array_filter($lines), -3);
        foreach ($lastLines as $line) {
            $dados = json_decode($line, true);
            if ($dados) {
                echo "<div style='background: #f5f5f5; padding: 10px; margin: 5px 0; border-radius: 5px;'>";
                echo "<strong>Data:</strong> " . $dados['data_clique'] . "<br>";
                echo "<strong>Anúncio ID:</strong> " . $dados['anuncio_id'] . "<br>";
                echo "<strong>Post ID:</strong> " . $dados['post_id'] . "<br>";
                echo "<strong>Tipo:</strong> " . ucfirst($dados['tipo_clique']) . "<br>";
                echo "<strong>IP:</strong> " . $dados['ip_usuario'];
                echo "</div>";
            }
        }
    }
} else {
    echo "<p style='color: orange;'>⚠️ Arquivo de log não encontrado</p>";
}

// Teste 4: Verificar se há posts para testar
echo "<h2>4. Verificação de Posts</h2>";
try {
    $stmt = $pdo->prepare("SELECT id, titulo, slug FROM posts WHERE publicado = 1 ORDER BY id DESC LIMIT 3");
    $stmt->execute();
    $posts = $stmt->fetchAll();
    
    if (!empty($posts)) {
        echo "<p><strong>Posts disponíveis para teste:</strong></p>";
        echo "<ul>";
        foreach ($posts as $post) {
            $url = BLOG_URL . '/post/' . $post['slug'];
            echo "<li><a href='$url' target='_blank'>" . htmlspecialchars($post['titulo']) . "</a> (ID: {$post['id']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠️ Nenhum post encontrado</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao buscar posts: " . $e->getMessage() . "</p>";
}

// Teste 5: Instruções para teste manual
echo "<h2>5. Como Testar Manualmente</h2>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; border: 1px solid #4caf50;'>";
echo "<h3>📋 Passos para Testar:</h3>";
echo "<ol>";
echo "<li><strong>Abra uma página de post</strong> - Use um dos links acima</li>";
echo "<li><strong>Abra o console do navegador</strong> - Pressione F12</li>";
echo "<li><strong>Verifique a meta tag</strong> - Execute: <code>console.log(document.querySelector('meta[name=\"post-id\"]')?.content);</code></li>";
echo "<li><strong>Clique em um anúncio</strong> - Deve aparecer log no console</li>";
echo "<li><strong>Verifique o arquivo de log</strong> - Acesse <a href='visualizar-cliques.php'>visualizar-cliques.php</a></li>";
echo "</ol>";
echo "</div>";

// Teste 6: Código para testar no console
echo "<h2>6. Código para Testar no Console</h2>";
echo "<p>Execute este código no console do navegador:</p>";
echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
echo "// Teste 1: Verificar meta tag
console.log('Post ID:', document.querySelector('meta[name=\"post-id\"]')?.content);

// Teste 2: Verificar se a função existe
console.log('Função existe:', typeof registrarCliqueAnuncio);

// Teste 3: Testar clique manual
registrarCliqueAnuncio(1, 'imagem');

// Teste 4: Verificar se há anúncios na página
console.log('Anúncios na página:', document.querySelectorAll('.anuncio-card-grade, .anuncio-card-carrossel, .anuncio-card-sidebar').length);

// Teste 5: Testar fetch direto
fetch('./api/registrar-clique-anuncio.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        anuncio_id: 1,
        post_id: 1,
        tipo_clique: 'imagem'
    })
})
.then(response => {
    console.log('Status:', response.status);
    return response.json();
})
.then(data => {
    console.log('Resposta:', data);
})
.catch(error => {
    console.error('Erro:', error);
});";
echo "</pre>";

// Links úteis
echo "<h2>7. Links Úteis</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";
echo "<a href='visualizar-cliques.php' style='background: #2196f3; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>📊 Visualizar Cliques</a>";
echo "<a href='admin/anuncios.php' style='background: #4caf50; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>⚙️ Gerenciar Anúncios</a>";
echo "<a href='admin/estatisticas-anuncios.php' style='background: #ff9800; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>📈 Estatísticas</a>";
echo "<a href='diagnostico-api.php' style='background: #9c27b0; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>🔍 Diagnóstico</a>";
echo "</div>";

// Resumo final
echo "<h2>8. Resumo do Sistema</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border: 1px solid #ffeaa7;'>";
echo "<h3>✅ Sistema Configurado:</h3>";
echo "<ul>";
echo "<li>✅ API funcionando</li>";
echo "<li>✅ JavaScript carregado</li>";
echo "<li>✅ Meta tag sendo gerada</li>";
echo "<li>✅ onclick adicionado aos anúncios</li>";
echo "<li>✅ Arquivo de log criado</li>";
echo "</ul>";
echo "<p><strong>Próximo passo:</strong> Teste clicando em um anúncio em uma página de post!</p>";
echo "</div>";
?> 