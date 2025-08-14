<?php
// Configurações do Banco de Dados com Variáveis de Ambiente
// Crie um arquivo .env na raiz do projeto com as seguintes variáveis:
// DB_HOST_LOCAL=localhost
// DB_HOST_IP=192.185.222.27
// DB_NAME=paymen58_brasil_hilario
// DB_USER=paymen58
// DB_PASS=sua_senha_aqui

// Carregar variáveis de ambiente se o arquivo .env existir
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Configurações com fallback para variáveis de ambiente
define('DB_HOST_LOCAL', $_ENV['DB_HOST_LOCAL'] ?? 'localhost');
define('DB_HOST_IP', $_ENV['DB_HOST_IP'] ?? '192.185.222.27');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'paymen58_brasil_hilario');
define('DB_USER', $_ENV['DB_USER'] ?? 'paymen58');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// Validação de segurança
if (empty(DB_PASS)) {
    error_log('ERRO CRÍTICO: Senha do banco de dados não configurada');
    die('Erro de configuração do banco de dados. Verifique o arquivo .env');
}

$dsnLocal = "mysql:host=" . DB_HOST_LOCAL . ";dbname=" . DB_NAME . ";charset=utf8mb4";
$dsnIP = "mysql:host=" . DB_HOST_IP . ";dbname=" . DB_NAME . ";charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

try {
    // Tenta conectar no localhost
    $pdo = new PDO($dsnLocal, DB_USER, DB_PASS, $options);
    
    // Verifica se a conexão está ativa
    $pdo->query('SELECT 1');
} catch (PDOException $eLocal) {
    try {
        // Tenta conectar no IP se localhost falhar
        $pdo = new PDO($dsnIP, DB_USER, DB_PASS, $options);
        
        // Verifica conexão
        $pdo->query('SELECT 1');
    } catch (PDOException $eIP) {
        // Ambos falharam: loga erro e termina
        error_log("Erro na conexão com o banco de dados (localhost): " . $eLocal->getMessage());
        error_log("Erro na conexão com o banco de dados (IP): " . $eIP->getMessage());
        die("Erro na conexão com o banco de dados. Por favor, tente novamente mais tarde.");
    }
}
