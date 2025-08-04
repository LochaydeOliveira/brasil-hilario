<?php
echo "<h1>🧪 Teste de Acesso ao Debug</h1>";

// Teste 1: Verificar se o arquivo existe
if (file_exists('debug-clique-v2.php')) {
    echo "<p>✅ Arquivo debug-clique-v2.php existe</p>";
} else {
    echo "<p style='color: red;'>❌ Arquivo debug-clique-v2.php não existe</p>";
}

// Teste 2: Verificar permissões
if (is_readable('debug-clique-v2.php')) {
    echo "<p>✅ Arquivo debug-clique-v2.php é legível</p>";
} else {
    echo "<p style='color: red;'>❌ Arquivo debug-clique-v2.php não é legível</p>";
}

// Teste 3: Simular uma requisição POST
echo "<h2>🧪 Simulando Requisição POST</h2>";

// Simular dados POST
$_POST = [
    'anuncio_id' => 1,
    'post_id' => 0,
    'tipo_clique' => 'imagem'
];

// Simular php://input
$input_data = json_encode($_POST);
file_put_contents('php://temp', $input_data);
rewind(fopen('php://temp', 'r'));

echo "<p>✅ Dados simulados criados</p>";

// Teste 4: Incluir o arquivo de debug
echo "<h2>🧪 Incluindo debug-clique-v2.php</h2>";
echo "<div style='border: 1px solid #ccc; padding: 10px; background: #f9f9f9;'>";

// Capturar a saída
ob_start();
include 'debug-clique-v2.php';
$output = ob_get_clean();

echo htmlspecialchars($output);
echo "</div>";

echo "<h2>🎯 Instruções</h2>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>1.</strong> Se o teste acima funcionou, o problema é no .htaccess</p>";
echo "<p><strong>2.</strong> Se não funcionou, há um problema no código</p>";
echo "<p><strong>3.</strong> Agora teste clicando em um anúncio</p>";
echo "</div>";
?> 