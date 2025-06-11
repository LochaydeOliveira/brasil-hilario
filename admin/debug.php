<?php
// Habilitar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurar log de erros
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Função para log personalizado
function debug_log($message) {
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message\n";
    error_log($log_message, 3, __DIR__ . '/debug.log');
}

// Testar conexão com banco de dados
try {
    require_once '../includes/db.php';
    debug_log("Conexão com banco de dados: OK");
} catch (Exception $e) {
    debug_log("Erro na conexão com banco: " . $e->getMessage());
}

// Testar inclusão de arquivos
$required_files = [
    '../config/config.php',
    '../includes/db.php',
    'includes/auth.php',
    'includes/header.php',
    'includes/footer.php',
    'includes/sidebar.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        debug_log("Arquivo existe: $file");
    } else {
        debug_log("Arquivo não encontrado: $file");
    }
}

// Testar permissões
$paths = [
    '.',
    'includes',
    '../config',
    '../includes'
];

foreach ($paths as $path) {
    $perms = substr(sprintf('%o', fileperms($path)), -4);
    debug_log("Permissões de $path: $perms");
}

// Testar sessão
session_start();
debug_log("Sessão iniciada: " . session_id());

// Testar configurações do PHP
debug_log("PHP Version: " . phpversion());
debug_log("display_errors: " . ini_get('display_errors'));
debug_log("error_reporting: " . ini_get('error_reporting'));
debug_log("log_errors: " . ini_get('log_errors'));
debug_log("error_log: " . ini_get('error_log'));

echo "Debug concluído. Verifique o arquivo debug.log na pasta admin.";
?> 