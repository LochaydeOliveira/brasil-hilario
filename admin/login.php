<?php
/**
 * Arquivo: login.php
 * Descrição: Página de login do painel administrativo
 * Funcionalidades:
 * - Formulário de login
 * - Validação de credenciais
 * - Redirecionamento após login
 * - Mensagens de erro/sucesso
 */

// Habilitar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Função para log
function log_error($message) {
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] $message\n", 3, __DIR__ . '/error.log');
}

// Inclui arquivos necessários
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Se já estiver logado, redireciona para o painel
if (isLoggedIn()) {
    redirect('index.php');
}

// Processa o formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitiza os dados do formulário
    $email = sanitize($_POST['email']);
    $senha = $_POST['senha'];
    
    // Valida os campos obrigatórios
    if (empty($email) || empty($senha)) {
        setError('Email e senha são obrigatórios.');
    } else {
        try {
            // Conecta ao banco de dados
            $conn = connectDB();
            
            // Busca o usuário pelo email
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();
            
            // Verifica se o usuário existe e a senha está correta
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Inicia a sessão
                session_start();
                
                // Armazena dados do usuário na sessão
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_nome'] = $usuario['nome'];
                $_SESSION['user_email'] = $usuario['email'];
                $_SESSION['user_tipo'] = $usuario['tipo'];
                
                // Redireciona para o painel
                redirect('index.php');
            } else {
                setError('Email ou senha inválidos.');
            }
        } catch (PDOException $e) {
            setError('Erro ao processar o login.');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel Administrativo</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 15px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background: none;
            border-bottom: none;
            text-align: center;
            padding: 20px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 5px;
            padding: 10px 15px;
        }
        .btn-primary {
            padding: 10px;
            border-radius: 5px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <img src="../assets/img/logo-brasil-hilario-quadrada-svg.svg" alt="Logo" class="logo">
                <h4>Painel Administrativo</h4>
            </div>
            <div class="card-body">
                <?php showMessages(); ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Entrar</button>
                    <a style="font-size: 14px;display: flex;justify-content: center;margin: 10px 0;color: #878787;" href="https://www.brasilhilario.com.br/">Voltar ao Blog</a>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 