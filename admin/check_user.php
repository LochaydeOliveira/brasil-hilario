<?php
require_once '../config/config.php';
require_once '../includes/db.php';

try {
    // Verificar se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
    echo "Tabela usuarios existe: " . ($stmt->rowCount() > 0 ? "Sim" : "Não") . "<br><br>";
    
    // Listar todos os usuários
    $stmt = $pdo->query("SELECT id, nome, email, status FROM usuarios");
    $usuarios = $stmt->fetchAll();
    
    echo "Total de usuários: " . count($usuarios) . "<br><br>";
    
    echo "Lista de usuários:<br>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Status</th></tr>";
    
    foreach ($usuarios as $usuario) {
        echo "<tr>";
        echo "<td>" . $usuario['id'] . "</td>";
        echo "<td>" . $usuario['nome'] . "</td>";
        echo "<td>" . $usuario['email'] . "</td>";
        echo "<td>" . $usuario['status'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Verificar usuário específico
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute(['admin@seusite.com']);
    $usuario = $stmt->fetch();
    
    echo "<br><br>Verificando usuário admin@seusite.com:<br>";
    if ($usuario) {
        echo "Usuário encontrado:<br>";
        echo "ID: " . $usuario['id'] . "<br>";
        echo "Nome: " . $usuario['nome'] . "<br>";
        echo "Email: " . $usuario['email'] . "<br>";
        echo "Status: " . $usuario['status'] . "<br>";
        
        // Testar senha
        $senha_teste = 'admin123';
        if (password_verify($senha_teste, $usuario['senha'])) {
            echo "<br>Senha 'admin123' está correta!";
        } else {
            echo "<br>Senha 'admin123' está incorreta!";
            echo "<br>Hash atual: " . $usuario['senha'];
        }
    } else {
        echo "Usuário não encontrado!";
    }
    
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
} 