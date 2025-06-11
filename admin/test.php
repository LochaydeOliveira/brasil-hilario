<?php
// Configurações básicas de erro
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Teste 1: PHP básico
echo "<h1>Teste Básico PHP</h1>";
echo "<p>PHP está funcionando!</p>";
echo "<p>Versão do PHP: " . phpversion() . "</p>";

// Teste 2: Verificar diretórios
echo "<h2>Teste de Diretórios</h2>";
$paths = [
    '../config' => 'Config',
    '../includes' => 'Includes',
    'includes' => 'Admin Includes'
];

foreach ($paths as $path => $name) {
    echo "<p>Verificando {$name}: ";
    if (is_dir($path)) {
        echo "✓ Existe";
    } else {
        echo "✗ Não existe";
    }
    echo "</p>";
}

// Teste 3: Verificar arquivos
echo "<h2>Teste de Arquivos</h2>";
$files = [
    '../config/config.php' => 'Config',
    '../includes/db.php' => 'Database',
    'includes/auth.php' => 'Auth'
];

foreach ($files as $file => $name) {
    echo "<p>Verificando {$name}: ";
    if (file_exists($file)) {
        echo "✓ Existe";
    } else {
        echo "✗ Não existe";
    }
    echo "</p>";
}

// Teste 4: Verificar permissões
echo "<h2>Teste de Permissões</h2>";
$test_dirs = [
    __DIR__ => 'Admin',
    dirname(__DIR__) => 'Raiz'
];

foreach ($test_dirs as $dir => $name) {
    echo "<p>Diretório {$name}: ";
    if (is_readable($dir)) {
        echo "✓ Legível";
    } else {
        echo "✗ Não legível";
    }
    echo " | ";
    if (is_writable($dir)) {
        echo "✓ Gravável";
    } else {
        echo "✗ Não gravável";
    }
    echo "</p>";
}

// Teste 5: Informações do servidor
echo "<h2>Informações do Servidor</h2>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
?> 