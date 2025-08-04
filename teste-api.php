<?php
echo "<h1>🧪 Teste da API de Cliques</h1>";

// Simular dados de teste
$dados = [
    'anuncio_id' => 9,
    'post_id' => 8,
    'tipo_clique' => 'imagem'
];

echo "<h2>📤 Dados sendo enviados:</h2>";
echo "<pre>" . json_encode($dados, JSON_PRETTY_PRINT) . "</pre>";

// Fazer requisição para a API
$url = 'https://brasilhilario.com.br/api/registrar-clique.php';
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "<h2>📥 Resposta da API:</h2>";
echo "<p><strong>HTTP Code:</strong> $httpCode</p>";

if ($error) {
    echo "<p style='color: red;'><strong>Erro cURL:</strong> $error</p>";
} else {
    echo "<p><strong>Resposta:</strong></p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    // Tentar decodificar JSON
    $json = json_decode($response, true);
    if ($json) {
        echo "<h3>📋 Resposta decodificada:</h3>";
        echo "<pre>" . json_encode($json, JSON_PRETTY_PRINT) . "</pre>";
    }
}

echo "<h2>🔍 Verificação do Banco</h2>";
echo "<p><a href='verificar-tabela.php' target='_blank'>Clique aqui para verificar a tabela cliques_anuncios</a></p>";
?> 