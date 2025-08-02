<?php
echo "<h1>🧪 Teste Direto da API</h1>";

// Habilitar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>1. Verificação de Arquivos</h2>";

// Verificar se os arquivos necessários existem
$files = [
    'config/config.php',
    'api/registrar-clique-anuncio.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p>✅ $file - Existe</p>";
    } else {
        echo "<p style='color: red;'>❌ $file - Não existe!</p>";
    }
}

echo "<h2>2. Teste de Configuração</h2>";

try {
    require_once 'config/config.php';
    echo "<p>✅ config/config.php carregado</p>";
    
    // Verificar se as constantes estão definidas
    $constants = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
    foreach ($constants as $constant) {
        if (defined($constant)) {
            echo "<p>✅ $constant definida</p>";
        } else {
            echo "<p style='color: red;'>❌ $constant não definida</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao carregar config: " . $e->getMessage() . "</p>";
}

echo "<h2>3. Teste de Conexão com Banco</h2>";

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    echo "<p>✅ Conexão com banco estabelecida</p>";
    
    // Testar se consegue fazer uma query simples
    $stmt = $pdo->query("SELECT 1");
    echo "<p>✅ Query de teste executada</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na conexão: " . $e->getMessage() . "</p>";
}

echo "<h2>4. Teste Simulado da API</h2>";

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

// Capturar saída e erros
ob_start();
try {
    include 'api/registrar-clique-anuncio.php';
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro fatal: " . $e->getMessage() . "</p>";
    echo "<p><strong>Stack trace:</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
$output = ob_get_clean();

echo "<p><strong>Saída da API:</strong></p>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

echo "<h2>5. Verificação do Arquivo de Log</h2>";
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
}

echo "<h2>6. Teste no Navegador</h2>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px;'>";
echo "<h3>📋 Como Testar:</h3>";
echo "<ol>";
echo "<li>Abra uma página de post</li>";
echo "<li>Pressione F12 para abrir o console</li>";
echo "<li>Execute: <code>registrarCliqueAnuncio(1, 'imagem');</code></li>";
echo "<li>Verifique se aparece '✅ Clique registrado com sucesso'</li>";
echo "<li>Se ainda der erro 500, verifique os logs do servidor</li>";
echo "</ol>";
echo "</div>";

echo "<h2>7. Código para Testar no Console</h2>";
echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
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

echo "<h2>8. Links Úteis</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";
echo "<a href='verificar-tabela-cliques.php' style='background: #4caf50; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>🔍 Verificar Tabela</a>";
echo "<a href='visualizar-cliques.php' style='background: #2196f3; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>📊 Visualizar Cliques</a>";
echo "<a href='teste-final.php' style='background: #ff9800; color: white; padding: 10px; text-decoration: none; border-radius: 5px; text-align: center;'>🎯 Teste Final</a>";
echo "</div>";
?> 