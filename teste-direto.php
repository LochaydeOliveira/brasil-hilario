<?php
echo "<h1>🎯 Teste Direto da API</h1>";

// Teste 1: Verificar se o arquivo da API existe e é acessível
echo "<h2>1. Verificação do Arquivo da API</h2>";
$apiFile = 'api/registrar-clique-anuncio.php';

if (file_exists($apiFile)) {
    echo "<p>✅ Arquivo da API encontrado</p>";
    echo "<p><strong>Permissões:</strong> " . substr(sprintf('%o', fileperms($apiFile)), -4) . "</p>";
    echo "<p><strong>Legível:</strong> " . (is_readable($apiFile) ? "Sim" : "Não") . "</p>";
} else {
    echo "<p style='color: red;'>❌ Arquivo da API não encontrado!</p>";
    exit;
}

// Teste 2: Simular uma requisição POST diretamente
echo "<h2>2. Teste Simulado da API</h2>";

// Simular dados POST
$_POST = [
    'anuncio_id' => 1,
    'post_id' => 1,
    'tipo_clique' => 'imagem'
];

// Simular dados JSON
$jsonData = json_encode([
    'anuncio_id' => 1,
    'post_id' => 1,
    'tipo_clique' => 'imagem'
]);

// Simular php://input
$inputFile = 'php://temp';
file_put_contents($inputFile, $jsonData);
rewind($inputFile);

// Capturar saída
ob_start();
include $apiFile;
$output = ob_get_clean();

echo "<p><strong>Saída da API:</strong></p>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Teste 3: Verificar arquivo de log
echo "<h2>3. Verificação do Arquivo de Log</h2>";
$logFile = 'logs/cliques_anuncios.log';

if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", trim($logContent));
    $totalEntries = count(array_filter($lines));
    
    echo "<p>✅ Arquivo de log encontrado</p>";
    echo "<p><strong>Total de entradas:</strong> $totalEntries</p>";
    
    if ($totalEntries > 0) {
        echo "<p><strong>Últimas 3 entradas:</strong></p>";
        $lastLines = array_slice(array_filter($lines), -3);
        foreach ($lastLines as $line) {
            echo "<pre>" . htmlspecialchars($line) . "</pre>";
        }
    }
} else {
    echo "<p style='color: orange;'>⚠️ Arquivo de log não encontrado</p>";
    
    // Tentar criar o diretório e arquivo
    $logDir = 'logs';
    if (!is_dir($logDir)) {
        if (mkdir($logDir, 0755, true)) {
            echo "<p>✅ Diretório logs criado</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao criar diretório logs</p>";
        }
    }
    
    // Testar se consegue escrever no arquivo
    if (file_put_contents($logFile, "Teste de escrita\n", FILE_APPEND | LOCK_EX) !== false) {
        echo "<p>✅ Arquivo de log criado e testado</p>";
        unlink($logFile); // Remover arquivo de teste
    } else {
        echo "<p style='color: red;'>❌ Erro ao criar arquivo de log</p>";
    }
}

// Teste 4: Verificar se os anúncios estão sendo exibidos
echo "<h2>4. Verificação dos Anúncios</h2>";

// Verificar se há anúncios no banco
try {
    require_once 'config/config.php';
    require_once 'includes/db.php';
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM anuncios WHERE ativo = 1");
    $stmt->execute();
    $totalAnuncios = $stmt->fetch()['total'];
    
    echo "<p><strong>Total de anúncios ativos:</strong> $totalAnuncios</p>";
    
    if ($totalAnuncios > 0) {
        $stmt = $pdo->prepare("SELECT id, titulo, localizacao FROM anuncios WHERE ativo = 1 LIMIT 5");
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
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao conectar com banco de dados: " . $e->getMessage() . "</p>";
}

// Teste 5: Verificar se a meta tag está sendo gerada
echo "<h2>5. Verificação da Meta Tag</h2>";
echo "<p>Para verificar se a meta tag está sendo gerada:</p>";
echo "<ol>";
echo "<li>Abra uma página de post (ex: /post/nome-do-post)</li>";
echo "<li>Pressione F12 para abrir as ferramentas do desenvolvedor</li>";
echo "<li>Vá para a aba 'Elements' (Elementos)</li>";
echo "<li>Procure por: <code>&lt;meta name=\"post-id\" content=\"...\"&gt;</code></li>";
echo "<li>Ou execute no console: <code>console.log(document.querySelector('meta[name=\"post-id\"]')?.content);</code></li>";
echo "</ol>";

// Teste 6: Teste manual no navegador
echo "<h2>6. Teste Manual no Navegador</h2>";
echo "<p>Execute este código no console do navegador em uma página de post:</p>";
echo "<pre>";
echo "// Teste 1: Verificar meta tag
console.log('Post ID:', document.querySelector('meta[name=\"post-id\"]')?.content);

// Teste 2: Verificar se a função existe
console.log('Função existe:', typeof registrarCliqueAnuncio);

// Teste 3: Testar clique manual
registrarCliqueAnuncio(1, 'imagem');

// Teste 4: Testar fetch direto
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
echo "<ul>";
echo "<li><a href='visualizar-cliques.php'>Visualizar Cliques</a></li>";
echo "<li><a href='teste-anuncios.php'>Teste Completo do Sistema</a></li>";
echo "<li><a href='admin/anuncios.php'>Gerenciar Anúncios</a></li>";
echo "</ul>";
?> 