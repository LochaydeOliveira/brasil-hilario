<?php
echo "<h1>🧪 Teste Simples da API</h1>";

// Teste 1: Verificar se a API responde
echo "<h2>1. Teste de Resposta da API</h2>";

$apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/api/registrar-clique-anuncio.php';
echo "<p><strong>URL da API:</strong> $apiUrl</p>";

// Simular requisição POST
$dados = [
    'anuncio_id' => 1,
    'post_id' => 1,
    'tipo_clique' => 'imagem'
];

echo "<p><strong>Dados de teste:</strong></p>";
echo "<pre>" . json_encode($dados, JSON_PRETTY_PRINT) . "</pre>";

// Fazer requisição real
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<h3>Resultado da Requisição:</h3>";
echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
echo "<p><strong>Resposta:</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

if ($error) {
    echo "<p style='color: red;'><strong>Erro cURL:</strong> $error</p>";
}

// Teste 2: Verificar arquivo de log
echo "<h2>2. Verificação do Arquivo de Log</h2>";
$logFile = 'logs/cliques_anuncios.log';

if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", trim($logContent));
    $totalEntries = count(array_filter($lines));
    
    echo "<p>✅ Arquivo de log encontrado</p>";
    echo "<p><strong>Total de entradas:</strong> $totalEntries</p>";
    
    if ($totalEntries > 0) {
        echo "<p><strong>Última entrada:</strong></p>";
        $lastLine = end(array_filter($lines));
        echo "<pre>" . htmlspecialchars($lastLine) . "</pre>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ Arquivo de log não encontrado</p>";
}

// Teste 3: Verificar meta tag
echo "<h2>3. Teste da Meta Tag</h2>";
echo "<p>Para testar a meta tag, abra uma página de post e execute no console:</p>";
echo "<code>console.log('Post ID:', document.querySelector('meta[name=\"post-id\"]')?.content);</code>";

// Teste 4: Teste JavaScript
echo "<h2>4. Teste JavaScript</h2>";
echo "<p>Execute este código no console do navegador:</p>";
echo "<pre>";
echo "fetch('./api/registrar-clique-anuncio.php', {
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
echo "<h2>5. Links Úteis</h2>";
echo "<ul>";
echo "<li><a href='visualizar-cliques.php'>Visualizar Cliques</a></li>";
echo "<li><a href='diagnostico-api.php'>Diagnóstico Completo</a></li>";
echo "<li><a href='teste-anuncios.php'>Teste Completo do Sistema</a></li>";
echo "</ul>";
?> 