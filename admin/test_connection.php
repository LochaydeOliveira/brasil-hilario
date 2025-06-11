<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

echo "<h1>Teste de Conexão</h1>";

// Teste 1: Verificar se os arquivos necessários existem
echo "<h2>Teste 1: Verificação de Arquivos</h2>";
$required_files = [
    '../config/config.php' => 'Configurações',
    '../includes/db.php' => 'Conexão com Banco',
    'includes/auth.php' => 'Autenticação'
];

foreach ($required_files as $file => $description) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ {$description} ({$file}) - OK</p>";
    } else {
        echo "<p style='color: red;'>✗ {$description} ({$file}) - Arquivo não encontrado</p>";
    }
}

// Teste 2: Verificar configurações do PHP
echo "<h2>Teste 2: Configurações do PHP</h2>";
echo "<p>Versão do PHP: " . phpversion() . "</p>";
echo "<p>Display Errors: " . ini_get('display_errors') . "</p>";
echo "<p>Error Reporting: " . ini_get('error_reporting') . "</p>";
echo "<p>Log Errors: " . ini_get('log_errors') . "</p>";
echo "<p>Error Log: " . ini_get('error_log') . "</p>";

// Teste 3: Tentar carregar as configurações
echo "<h2>Teste 3: Carregando Configurações</h2>";
try {
    require_once '../config/config.php';
    echo "<p style='color: green;'>✓ Configurações carregadas com sucesso</p>";
    echo "<p>BLOG_URL: " . BLOG_URL . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erro ao carregar configurações: " . $e->getMessage() . "</p>";
}

// Teste 4: Testar conexão com o banco de dados
echo "<h2>Teste 4: Conexão com Banco de Dados</h2>";
try {
    require_once '../includes/db.php';
    echo "<p style='color: green;'>✓ Conexão com banco de dados estabelecida</p>";
    
    // Testar uma query simples
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch();
    echo "<p>Total de usuários: " . $result['total'] . "</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Erro na conexão com banco de dados: " . $e->getMessage() . "</p>";
}

// Teste 5: Verificar permissões de diretório
echo "<h2>Teste 5: Permissões de Diretório</h2>";
$directories = [
    __DIR__ => 'Diretório Admin',
    __DIR__ . '/includes' => 'Diretório Includes',
    dirname(__DIR__) . '/config' => 'Diretório Config',
    dirname(__DIR__) . '/includes' => 'Diretório Includes Raiz'
];

foreach ($directories as $dir => $description) {
    if (is_readable($dir)) {
        echo "<p style='color: green;'>✓ {$description} - Legível</p>";
    } else {
        echo "<p style='color: red;'>✗ {$description} - Não legível</p>";
    }
    
    if (is_writable($dir)) {
        echo "<p style='color: green;'>✓ {$description} - Gravável</p>";
    } else {
        echo "<p style='color: red;'>✗ {$description} - Não gravável</p>";
    }
}

// Teste 6: Verificar módulos do PHP
echo "<h2>Teste 6: Módulos do PHP</h2>";
$required_modules = ['pdo', 'pdo_mysql', 'session', 'mbstring'];
foreach ($required_modules as $module) {
    if (extension_loaded($module)) {
        echo "<p style='color: green;'>✓ Módulo {$module} carregado</p>";
    } else {
        echo "<p style='color: red;'>✗ Módulo {$module} não carregado</p>";
    }
}
?> 