<?php
echo "<h1>🔍 Debug da API - Erro 500</h1>";

// Habilitar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>1. Verificando Configuração do PHP</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Display Errors:</strong> " . (ini_get('display_errors') ? 'On' : 'Off') . "</p>";
echo "<p><strong>Error Reporting:</strong> " . error_reporting() . "</p>";

// Teste 1: Verificar se os arquivos necessários existem
echo "<h2>2. Verificação de Arquivos</h2>";
$files = [
    'config/config.php',
    'includes/db.php',
    'includes/AnunciosManager.php',
    'api/registrar-clique-anuncio.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p>✅ $file - Existe</p>";
    } else {
        echo "<p style='color: red;'>❌ $file - Não existe!</p>";
    }
}

// Teste 2: Verificar se consegue incluir os arquivos
echo "<h2>3. Teste de Inclusão de Arquivos</h2>";

try {
    echo "<p>Tentando incluir config/config.php...</p>";
    require_once 'config/config.php';
    echo "<p>✅ config/config.php incluído com sucesso</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao incluir config/config.php: " . $e->getMessage() . "</p>";
}

try {
    echo "<p>Tentando incluir includes/db.php...</p>";
    require_once 'includes/db.php';
    echo "<p>✅ includes/db.php incluído com sucesso</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao incluir includes/db.php: " . $e->getMessage() . "</p>";
}

try {
    echo "<p>Tentando incluir includes/AnunciosManager.php...</p>";
    require_once 'includes/AnunciosManager.php';
    echo "<p>✅ includes/AnunciosManager.php incluído com sucesso</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao incluir includes/AnunciosManager.php: " . $e->getMessage() . "</p>";
}

// Teste 3: Verificar conexão com banco de dados
echo "<h2>4. Teste de Conexão com Banco de Dados</h2>";
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
    echo "<p>✅ Conexão com banco de dados estabelecida</p>";
    
    // Testar se a tabela anuncios existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'anuncios'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Tabela 'anuncios' existe</p>";
    } else {
        echo "<p style='color: red;'>❌ Tabela 'anuncios' não existe!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na conexão com banco de dados: " . $e->getMessage() . "</p>";
}

// Teste 4: Simular a API com dados reais
echo "<h2>5. Teste Simulado da API</h2>";

// Simular dados JSON
$jsonData = json_encode([
    'anuncio_id' => 1,
    'post_id' => 1,
    'tipo_clique' => 'imagem'
]);

// Simular php://input
$input = $jsonData;

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

// Teste 5: Verificar permissões do diretório logs
echo "<h2>6. Verificação de Permissões</h2>";
$logDir = 'logs';
$logFile = 'logs/cliques_anuncios.log';

if (!is_dir($logDir)) {
    echo "<p>Tentando criar diretório logs...</p>";
    if (mkdir($logDir, 0755, true)) {
        echo "<p>✅ Diretório logs criado</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao criar diretório logs</p>";
    }
} else {
    echo "<p>✅ Diretório logs existe</p>";
}

if (is_writable($logDir)) {
    echo "<p>✅ Diretório logs é gravável</p>";
} else {
    echo "<p style='color: red;'>❌ Diretório logs não é gravável</p>";
}

// Testar escrita no arquivo de log
$testContent = "Teste de escrita: " . date('Y-m-d H:i:s') . "\n";
if (file_put_contents($logFile, $testContent, FILE_APPEND | LOCK_EX) !== false) {
    echo "<p>✅ Arquivo de log é gravável</p>";
    // Remover conteúdo de teste
    $currentContent = file_get_contents($logFile);
    $newContent = str_replace($testContent, '', $currentContent);
    file_put_contents($logFile, $newContent);
} else {
    echo "<p style='color: red;'>❌ Erro ao escrever no arquivo de log</p>";
}

// Teste 6: Verificar se há erros no log do PHP
echo "<h2>7. Verificação de Logs do PHP</h2>";
$phpLogFile = ini_get('error_log');
if ($phpLogFile && file_exists($phpLogFile)) {
    echo "<p><strong>Arquivo de log do PHP:</strong> $phpLogFile</p>";
    $logContent = file_get_contents($phpLogFile);
    if (!empty($logContent)) {
        echo "<p><strong>Últimas linhas do log:</strong></p>";
        $lines = explode("\n", $logContent);
        $lastLines = array_slice($lines, -10);
        foreach ($lastLines as $line) {
            if (!empty(trim($line))) {
                echo "<pre>" . htmlspecialchars($line) . "</pre>";
            }
        }
    } else {
        echo "<p>Log do PHP está vazio</p>";
    }
} else {
    echo "<p>Arquivo de log do PHP não encontrado</p>";
}

echo "<h2>8. Próximos Passos</h2>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px;'>";
echo "<h3>🔧 Ações Recomendadas:</h3>";
echo "<ol>";
echo "<li>Verifique se todos os arquivos necessários existem</li>";
echo "<li>Confirme se a conexão com o banco de dados está funcionando</li>";
echo "<li>Verifique se as permissões do diretório logs estão corretas</li>";
echo "<li>Teste a API novamente após as correções</li>";
echo "</ol>";
echo "</div>";
?> 