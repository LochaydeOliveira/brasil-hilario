<?php
require_once '../config/config.php';
require_once '../includes/db.php';

try {
    // Verificar se a tabela de usuários existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
    if ($stmt->rowCount() === 0) {
        // Criar tabela de usuários
        $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            senha VARCHAR(255) NOT NULL,
            status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        echo "Tabela de usuários criada com sucesso!<br>";
    }
    
    // Verificar se existe o usuário padrão
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute(['admin@seusite.com']);
    if ($stmt->rowCount() === 0) {
        // Criar usuário padrão
        $senha_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, status) VALUES (?, ?, ?, 'ativo')");
        $stmt->execute(['Administrador', 'admin@seusite.com', $senha_hash]);
        
        echo "Usuário padrão criado com sucesso!<br>";
        echo "Email: admin@seusite.com<br>";
        echo "Senha: admin123<br>";
    } else {
        echo "Usuário padrão já existe!<br>";
    }
    
    echo "<br>Configuração concluída! <a href='login.php'>Ir para o login</a>";
    
} catch (PDOException $e) {
    die("Erro na configuração: " . $e->getMessage());
} 