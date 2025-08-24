<?php
if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Logging de erros para diagnosticar problemas em produção
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) { @mkdir($logDir, 0755, true); }
$logFile = $logDir . '/app-' . date('Y-m-d') . '.log';
@ini_set('log_errors', '1');
@ini_set('error_log', $logFile);
@ini_set('display_errors', '0');
@error_reporting(E_ALL);

function app_log($message): void {
    $user = $_SESSION['user_email'] ?? 'guest';
    $url = $_SERVER['REQUEST_URI'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $line = '[' . date('Y-m-d H:i:s') . "] [$user][$ip] $url - $message";
    error_log($line);
}

set_exception_handler(function(Throwable $e){
    app_log('EXCEPTION: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
});

set_error_handler(function($severity, $message, $file, $line){
    // Converte todos os erros para log e continua execução quando possível
    app_log("PHP ERROR [$severity]: $message in $file:$line");
    return false; // deixa o PHP lidar se necessário
});

register_shutdown_function(function(){
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        app_log('FATAL: ' . $err['message'] . ' in ' . $err['file'] . ':' . $err['line']);
    }
});

// Segue o mesmo padrão de config/database.php (localhost -> IP)
define('DB_HOST_LOCAL_EBOOK', 'localhost');
define('DB_HOST_IP_EBOOK', '192.185.222.27');
define('DB_NAME_EBOOK', 'paymen58_db_libido');
define('DB_USER_EBOOK', 'paymen58');
define('DB_PASS_EBOOK', 'u4q7+B6ly)obP_gxN9sNe');

define('ADMIN_ALLOWED_EMAILS', json_encode(['lochaydeguerreiro@hotmail.com']));
define('ADMIN_SECRET', '100423Oliviah');

// Opções PDO
$pdoOptions = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Tenta primeiro localhost, depois o IP (como no config/database.php)
$dsnLocal = 'mysql:host=' . DB_HOST_LOCAL_EBOOK . ';dbname=' . DB_NAME_EBOOK . ';charset=utf8mb4';
$dsnIP    = 'mysql:host=' . DB_HOST_IP_EBOOK    . ';dbname=' . DB_NAME_EBOOK . ';charset=utf8mb4';

try {
    $pdo = new PDO($dsnLocal, DB_USER_EBOOK, DB_PASS_EBOOK, $pdoOptions);
    $pdo->query('SELECT 1');
} catch (PDOException $eLocal) {
    try {
        $pdo = new PDO($dsnIP, DB_USER_EBOOK, DB_PASS_EBOOK, $pdoOptions);
        $pdo->query('SELECT 1');
    } catch (PDOException $eIP) {
        error_log('Erro DB (localhost): ' . $eLocal->getMessage());
        error_log('Erro DB (IP): ' . $eIP->getMessage());
        http_response_code(500);
        die('Erro na conexão com o banco de dados.');
    }
}

function is_logged_in(): bool { return !empty($_SESSION['user_id']); }
function require_login(): void { if (!is_logged_in()) { header('Location: index.php'); exit; } }
function is_admin(): bool {
    if (empty($_SESSION['user_email'])) return false;
    $allowed = json_decode(ADMIN_ALLOWED_EMAILS, true);
    return is_array($allowed) && in_array(strtolower($_SESSION['user_email']), array_map('strtolower', $allowed), true);
}
function require_admin(): void { if (!is_logged_in() || !is_admin()) { header('Location: ../index.php'); exit; } }
