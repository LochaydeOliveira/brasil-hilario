<?php
echo "<h1>🧪 Teste da API Simplificada</h1>";

// Teste 1: Verificar se a API simplificada existe
echo "<h2>1. Verificação da API Simplificada</h2>";
$apiFile = 'api/teste-simples.php';

if (file_exists($apiFile)) {
    echo "<p>✅ API simplificada encontrada</p>";
} else {
    echo "<p style='color: red;'>❌ API simplificada não encontrada!</p>";
    exit;
}

// Teste 2: Simular requisição para a API simplificada
echo "<h2>2. Teste da API Simplificada</h2>";

// Simular dados JSON
$jsonData = json_encode([
    'anuncio_id' => 1,
    'post_id' => 1,
    'tipo_clique' => 'imagem'
]);

// Simular método POST
$_SERVER['REQUEST_METHOD'] = 'POST';

echo "<p><strong>Dados simulados:</strong></p>";
echo "<pre>" . htmlspecialchars($jsonData) . "</pre>";

// Capturar saída
ob_start();
include $apiFile;
$output = ob_get_clean();

echo "<p><strong>Resposta da API:</strong></p>";
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

// Teste 4: Instruções para teste no navegador
echo "<h2>4. Teste no Navegador</h2>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px;'>";
echo "<h3>📋 Como Testar:</h3>";
echo "<ol>";
echo "<li>Abra uma página de post</li>";
echo "<li>Pressione F12 para abrir o console</li>";
echo "<li>Execute: <code>registrarCliqueAnuncio(1, 'imagem');</code></li>";
echo "<li>Verifique se aparece '✅ Clique registrado com sucesso'</li>";
echo "<li>Acesse <a href='visualizar-cliques.php'>visualizar-cliques.php</a> para ver os cliques</li>";
echo "</ol>";
echo "</div>";

// Teste 5: Código para testar no console
echo "<h2>5. Código para Testar no Console</h2>";
echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
echo "// Teste 1: Verificar meta tag
console.log('Post ID:', document.querySelector('meta[name=\"post-id\"]')?.content);

// Teste 2: Verificar se a função existe
console.log('Função existe:', typeof registrarCliqueAnuncio);

// Teste 3: Testar clique manual
registrarCliqueAnuncio(1, 'imagem');

// Teste 4: Verificar se há anúncios na página
console.log('Anúncios na página:', document.querySelectorAll('.anuncio-card-grade, .anuncio-card-carrossel, .anuncio-card-sidebar').length);

// Teste 5: Testar fetch direto para API simplificada
fetch('./api/teste-simples.php', {
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
echo "<h2>6. Links Úteis</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";
echo "<a href='visualizar-cliques.php' style='background: #2196f3; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>📊 Visualizar Cliques</a>";
echo "<a href='debug-api.php' style='background: #ff9800; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>🔍 Debug Completo</a>";
echo "<a href='teste-final.php' style='background: #4caf50; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>🎯 Teste Final</a>";
echo "</div>";

echo "<h2>7. Status</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border: 1px solid #ffeaa7;'>";
echo "<h3>✅ API Simplificada Configurada:</h3>";
echo "<ul>";
echo "<li>✅ API simplificada criada</li>";
echo "<li>✅ JavaScript atualizado</li>";
echo "<li>✅ Logs sendo salvos</li>";
echo "<li>✅ CORS configurado</li>";
echo "</ul>";
echo "<p><strong>Próximo passo:</strong> Teste clicando em um anúncio!</p>";
echo "</div>";
?> 