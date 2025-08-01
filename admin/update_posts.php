<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';

// Verificar se é POST

try {
    // Atualizar posts sem data de criação
    $stmt = $pdo->prepare("UPDATE posts SET data_criacao = CURRENT_TIMESTAMP WHERE data_criacao IS NULL");
    $stmt->execute();

    echo "Posts atualizados com sucesso!<br>";
    echo "<a href='index.php'>Voltar para o Dashboard</a>";

} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}
