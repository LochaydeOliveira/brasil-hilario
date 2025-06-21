<?php
$host = "localhost";
$db = "paymen58_blog_adsense";
$user = "paymen58";
$pass = "u4q7+B6ly)obP_gxN9sNe";

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        throw new Exception("Erro na conexão: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    error_log("Erro na conexão com o banco de dados: " . $e->getMessage());
    die("Erro na conexão com o banco de dados. Por favor, tente novamente mais tarde.");
} 