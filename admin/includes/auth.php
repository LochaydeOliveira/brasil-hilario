<?php
/**
 * Arquivo: auth.php
 * Descrição: Funções de autenticação e controle de acesso
 * Funcionalidades:
 * - Verifica login do usuário
 * - Controla permissões de acesso
 * - Gerencia sessões
 * - Protege contra acesso não autorizado
 */

// Inicia a sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica se o usuário está logado
 * Redireciona para a página de login se não estiver
 */
function check_login() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Verifica se o usuário está logado
 * @return bool True se estiver logado, False caso contrário
 */
function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

/**
 * Verifica se o usuário é administrador
 * @return bool True se for admin, False caso contrário
 */
function is_admin() {
    return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';
}

/**
 * Verifica se o usuário tem permissão para acessar uma página
 * @param string $required_type Tipo de usuário necessário (admin, editor, etc)
 * @return bool True se tiver permissão, False caso contrário
 */
function check_permission($required_type) {
    if (!isLoggedIn()) {
        return false;
    }
    
    // Admin tem acesso a tudo
    if (is_admin()) {
        return true;
    }
    
    // Verifica o tipo específico de permissão
    return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === $required_type;
}

/**
 * Gera um token CSRF para proteção contra ataques
 * @return string Token CSRF
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica se o token CSRF é válido
 * @param string $token Token a ser verificado
 * @return bool True se o token for válido, False caso contrário
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Limpa a sessão do usuário (logout)
 */
function logout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

// Verificar se o usuário é editor
function is_editor() {
    return isset($_SESSION['usuario_tipo']) && ($_SESSION['usuario_tipo'] === 'admin' || $_SESSION['usuario_tipo'] === 'editor');
}

// Verificar timeout da sessão (30 minutos)
function check_session_timeout() {
    if (isset($_SESSION['ultimo_acesso'])) {
        $timeout = 30 * 60; // 30 minutos em segundos
        if (time() - $_SESSION['ultimo_acesso'] > $timeout) {
            session_destroy();
            header('Location: login.php?msg=timeout');
            exit;
        }
    }
    $_SESSION['ultimo_acesso'] = time();
}

// Obter dados do usuário atual
function get_logged_user() {
    global $pdo;
    if (!isset($_SESSION['usuario_id'])) {
        return null;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['usuario_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Erro ao obter dados do usuário: " . $e->getMessage());
        return null;
    }
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

// Função para limpar mensagens da sessão
function clear_session_messages() {
    unset($_SESSION['error']);
    unset($_SESSION['success']);
}

// Função para verificar se o usuário tem permissão para editar um post
function can_edit_post($post_author_id) {
    // Se for admin, pode editar qualquer post
    if (is_admin()) {
        return true;
    }
    
    // Se for editor, só pode editar seus próprios posts
    return isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $post_author_id;
}

// Função para obter dados do usuário
function get_user_data($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id, nome, email, tipo FROM usuarios WHERE id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Erro ao obter dados do usuário: " . $e->getMessage());
        return false;
    }
} 