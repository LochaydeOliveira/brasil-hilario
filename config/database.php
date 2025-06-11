<?php
// Configurações do Banco de Dados
define('DB_HOST', '192.185.222.27'); // Usando o IP do servidor
define('DB_NAME', 'paymen58_blog_adsense');
define('DB_USER', 'paymen58');
define('DB_PASS', 'u4q7+B6ly)obP_gxN9sNe');

// Conexão com o banco de dados
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verifica se houve erro na conexão
    if ($conn->connect_error) {
        throw new Exception("Erro na conexão com o banco de dados: " . $conn->connect_error);
    }
    
    // Define o charset para utf8
    $conn->set_charset("utf8");
    
} catch (Exception $e) {
    error_log("Erro na conexão com o banco de dados: " . $e->getMessage());
    die("Erro na conexão com o banco de dados. Por favor, tente novamente mais tarde.");
} 