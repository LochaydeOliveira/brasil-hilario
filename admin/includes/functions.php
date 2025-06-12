<?php
/**
 * Funções utilitárias para o painel administrativo
 */

/**
 * Define uma mensagem de erro na sessão
 */
function setError($message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['error'] = $message;
}

/**
 * Define uma mensagem de sucesso na sessão
 */
function setSuccess($message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['success'] = $message;
}

/**
 * Retorna e limpa a mensagem de erro da sessão
 */
function getError() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
    unset($_SESSION['error']);
    return $error;
}

/**
 * Retorna e limpa a mensagem de sucesso da sessão
 */
function getSuccess() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
    unset($_SESSION['success']);
    return $success;
}

/**
 * Retorna o HTML de uma mensagem de erro
 */
function getErrorHtml($message) {
    if ($message) {
        return '<div class="alert alert-danger alert-dismissible fade show" role="alert">' .
               '<i class="fas fa-exclamation-circle me-2"></i>' . htmlspecialchars($message) .
               '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>' .
               '</div>';
    }
    return '';
}

/**
 * Retorna o HTML de uma mensagem de sucesso
 */
function getSuccessHtml($message) {
    if ($message) {
        return '<div class="alert alert-success alert-dismissible fade show" role="alert">' .
               '<i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($message) .
               '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>' .
               '</div>';
    }
    return '';
}

/**
 * Exibe uma mensagem de erro em um alerta Bootstrap
 */
function showError($message) {
    echo getErrorHtml($message);
}

/**
 * Exibe uma mensagem de sucesso em um alerta Bootstrap
 */
function showSuccess($message) {
    echo getSuccessHtml($message);
}

/**
 * Verifica se o usuário tem permissão para acessar uma página
 */
function checkPermission($required_role = 'admin') {
    if (!isLoggedIn()) {
        setError('Você precisa estar logado para acessar esta página.');
        header('Location: login.php');
        exit;
    }

    if ($required_role === 'admin' && !isAdmin()) {
        setError('Você não tem permissão para acessar esta página.');
        header('Location: index.php');
        exit;
    }
}

/**
 * Verifica se o usuário é administrador
 */
function isAdmin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Formata uma data para o padrão brasileiro
 */
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

/**
 * Gera um slug a partir de um texto
 */
function generateSlug($text) {
    return strtolower(
        preg_replace(
            '/[^a-z0-9]+/',
            '-',
            preg_replace(
                '/[\u0300-\u036f]/',
                '',
                iconv('UTF-8', 'ASCII//TRANSLIT', $text)
            )
        )
    );
}
?> 