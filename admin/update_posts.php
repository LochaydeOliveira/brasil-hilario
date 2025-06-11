<?php
require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';

// Verificar se o usuário está logado
check_auth();

try {
    // Atualizar posts sem data de criação
    $stmt = $pdo->prepare("UPDATE posts SET data_criacao = CURRENT_TIMESTAMP WHERE data_criacao IS NULL");
    $stmt->execute();
    
    echo "Posts atualizados com sucesso!<br>";
    echo "<a href='index.php'>Voltar para o Dashboard</a>";
    
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
} 