<?php
// Configurações do Banco de Dados
define('DB_HOST', 'localhost'); // Tentando com localhost primeiro
define('DB_NAME', 'paymen58_blog_adsense');
define('DB_USER', 'paymen58');
define('DB_PASS', 'u4q7+B6ly)obP_gxN9sNe');

// Conexão com o banco de dados
try {
    // Tenta conectar com o host local
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Se falhar com localhost, tenta com o IP
    if ($conn->connect_error) {
        $conn = new mysqli('192.185.222.27', DB_USER, DB_PASS, DB_NAME);
    }
    
    // Verifica se houve erro na conexão
    if ($conn->connect_error) {
        throw new Exception("Erro na conexão com o banco de dados: " . $conn->connect_error . 
                          " (Erro #" . $conn->connect_errno . ")");
    }
    
    // Define o charset para utf8
    if (!$conn->set_charset("utf8")) {
        throw new Exception("Erro ao definir charset: " . $conn->error);
    }
    
    // Verifica se a conexão está ativa
    if (!$conn->ping()) {
        throw new Exception("Conexão com o banco de dados perdida");
    }
    
} catch (Exception $e) {
    error_log("Erro na conexão com o banco de dados: " . $e->getMessage());
    die("Erro na conexão com o banco de dados. Por favor, tente novamente mais tarde. Detalhes: " . $e->getMessage());
} 