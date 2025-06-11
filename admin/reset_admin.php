<?php
require_once '../config/config.php';
require_once '../includes/db.php';

try {
    // Remover usuário antigo
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE email = ?");
    $stmt->execute(['admin@seusite.com']);
    
    // Gerar novo hash da senha
    $senha = 'admin123';
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
    // Criar novo usuário
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, status) VALUES (?, ?, ?, 'ativo')");
    $stmt->execute(['Administrador', 'admin@seusite.com', $senha_hash]);
    
    echo "Usuário administrador recriado com sucesso!<br>";
    echo "Email: admin@seusite.com<br>";
    echo "Senha: admin123<br>";
    echo "Hash gerado: " . $senha_hash . "<br>";
    
    // Verificar se a senha está correta
    if (password_verify($senha, $senha_hash)) {
        echo "<br>Verificação: A senha está correta!";
    } else {
        echo "<br>Verificação: ERRO - A senha está incorreta!";
    }
    
    echo "<br><br><a href='login.php'>Ir para o login</a>";
    
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
} 