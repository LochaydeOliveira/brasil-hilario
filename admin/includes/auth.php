<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Função para verificar se o usuário está logado
function check_auth() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Função para verificar se é admin
function is_admin() {
    return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';
}

// Função para verificar se é editor
function is_editor() {
    return isset($_SESSION['usuario_tipo']) && 
           ($_SESSION['usuario_tipo'] === 'admin' || $_SESSION['usuario_tipo'] === 'editor');
}

// Função para fazer login
function do_login($email, $senha) {
    global $pdo;
    
    try {
        // Verificar se o usuário existe e está ativo
        $stmt = $pdo->prepare("
            SELECT * FROM usuarios 
            WHERE email = ? 
            AND status = 'ativo' 
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario) {
            // Verificar a senha
            if (password_verify($senha, $usuario['senha'])) {
                // Atualizar último login
                $stmt = $pdo->prepare("
                    UPDATE usuarios 
                    SET ultimo_login = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$usuario['id']]);
                
                // Definir dados da sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_tipo'] = $usuario['tipo'];
                $_SESSION['usuario_avatar'] = $usuario['avatar'];
                $_SESSION['usuario_biografia'] = $usuario['biografia'];
                $_SESSION['ultimo_acesso'] = time();
                
                return true;
            }
        }
        
        // Log de tentativa de login falha
        error_log("Tentativa de login falha para o email: " . $email);
        return false;
    } catch (PDOException $e) {
        error_log("Erro no login: " . $e->getMessage());
        return false;
    }
}

// Função para fazer logout
function do_logout() {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Função para verificar se é uma requisição AJAX
function is_ajax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Função para gerar token CSRF
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Função para verificar token CSRF
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

// Função para verificar timeout da sessão (30 minutos)
function check_session_timeout() {
    if (isset($_SESSION['ultimo_acesso']) && (time() - $_SESSION['ultimo_acesso'] > 1800)) {
        session_destroy();
        header("Location: login.php?msg=timeout");
        exit;
    }
    $_SESSION['ultimo_acesso'] = time();
}

// Função para obter dados do usuário
function get_user_data($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Erro ao obter dados do usuário: " . $e->getMessage());
        return false;
    }
} 