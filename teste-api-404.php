<?php
echo "<h1>🔍 Teste da API - Erro 404</h1>";

// Verificar se o arquivo existe
$apiFile = 'api/clique-simples.php';
if (file_exists($apiFile)) {
    echo "<p>✅ Arquivo $apiFile existe</p>";
} else {
    echo "<p style='color: red;'>❌ Arquivo $apiFile NÃO existe</p>";
    exit;
}

// Verificar permissões
if (is_readable($apiFile)) {
    echo "<p>✅ Arquivo $apiFile é legível</p>";
} else {
    echo "<p style='color: red;'>❌ Arquivo $apiFile não é legível</p>";
}

// Testar acesso via HTTP
$url = 'https://brasilhilario.com.br/api/clique-simples.php';
echo "<p>Testando acesso a: $url</p>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Status Code: $httpCode</p>";

if ($httpCode == 200) {
    echo "<p style='color: green;'>✅ API acessível</p>";
} elseif ($httpCode == 404) {
    echo "<p style='color: red;'>❌ API retorna 404 - arquivo não encontrado</p>";
} else {
    echo "<p style='color: orange;'>⚠️ API retorna código $httpCode</p>";
}

// Verificar .htaccess
echo "<h2>🔧 Verificando .htaccess</h2>";
$htaccessFile = '.htaccess';
if (file_exists($htaccessFile)) {
    echo "<p>✅ Arquivo .htaccess existe</p>";
    
    $htaccessContent = file_get_contents($htaccessFile);
    if (strpos($htaccessContent, 'clique-simples') !== false) {
        echo "<p>✅ Regra para clique-simples.php encontrada no .htaccess</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Regra para clique-simples.php NÃO encontrada no .htaccess</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Arquivo .htaccess não existe</p>";
}

// Testar simulação de POST
echo "<h2>🧪 Teste de POST para a API</h2>";

$testData = [
    'anuncio_id' => 1,
    'post_id' => 1,
    'tipo_clique' => 'imagem'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<p>POST Test HTTP Code: $httpCode</p>";
if ($error) {
    echo "<p style='color: red;'>❌ Erro cURL: $error</p>";
} else {
    echo "<p>✅ Requisição POST executada</p>";
    echo "<p>Resposta: " . htmlspecialchars($response) . "</p>";
}

// Verificar estrutura de diretórios
echo "<h2>📁 Estrutura de Diretórios</h2>";
echo "<p>Diretório atual: " . getcwd() . "</p>";
echo "<p>Arquivo API: " . realpath($apiFile) . "</p>";

// Listar arquivos no diretório api
$apiDir = 'api';
if (is_dir($apiDir)) {
    echo "<p>Arquivos no diretório api:</p>";
    $files = scandir($apiDir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>❌ Diretório api não existe</p>";
}

echo "<h2>🔗 Links Úteis</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";
echo "<a href='migrar.php' style='background: #4caf50; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>🔄 Migrar Cliques</a>";
echo "<a href='verificar-tabela-cliques.php' style='background: #2196f3; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>🔍 Verificar Tabela</a>";
echo "<a href='admin/anuncios.php' style='background: #ff9800; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>⚙️ Painel Admin</a>";
echo "</div>";
?> 