<?php
/**
 * Arquivo: config.php
 * Descrição: Configurações do sistema
 * Funcionalidades:
 * - Define constantes do sistema
 * - Configura conexão com banco de dados
 * - Define funções utilitárias
 */

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'brasil_hilario');

// Configurações do site
define('SITE_NAME', 'Brasil Hilário');
define('SITE_URL', 'http://localhost/brasil-hilario');
define('ADMIN_URL', SITE_URL . '/admin');

// Configurações de upload
define('UPLOAD_DIR', '../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Configurações de segurança
define('HASH_COST', 12); // Custo do algoritmo de hash para senhas

// Função para conectar ao banco de dados
function connectDB() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $conn;
    } catch (PDOException $e) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
}

// Função para sanitizar entrada de dados
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Função para gerar slug
function generateSlug($text) {
    // Converte para minúsculas
    $text = strtolower($text);
    
    // Remove acentos
    $text = preg_replace('/[áàãâä]/ui', 'a', $text);
    $text = preg_replace('/[éèêë]/ui', 'e', $text);
    $text = preg_replace('/[íìîï]/ui', 'i', $text);
    $text = preg_replace('/[óòõôö]/ui', 'o', $text);
    $text = preg_replace('/[úùûü]/ui', 'u', $text);
    $text = preg_replace('/[ç]/ui', 'c', $text);
    
    // Remove caracteres especiais
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    
    // Substitui espaços por hífens
    $text = preg_replace('/[\s-]+/', '-', $text);
    
    // Remove hífens do início e fim
    $text = trim($text, '-');
    
    return $text;
}

// Função para formatar data
function formatDate($date, $format = 'd/m/Y H:i') {
    return date($format, strtotime($date));
}

// Função para verificar extensão de arquivo
function isAllowedExtension($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ALLOWED_EXTENSIONS);
}

// Função para verificar tamanho do arquivo
function isAllowedSize($filesize) {
    return $filesize <= MAX_FILE_SIZE;
}

// Função para gerar nome único de arquivo
function generateUniqueFilename($filename) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    return uniqid() . '.' . $ext;
}

// Função para redirecionar
function redirect($url) {
    header("Location: $url");
    exit;
}

// Função para exibir mensagem de erro
function setError($message) {
    $_SESSION['error'] = $message;
}

// Função para exibir mensagem de sucesso
function setSuccess($message) {
    $_SESSION['success'] = $message;
} 