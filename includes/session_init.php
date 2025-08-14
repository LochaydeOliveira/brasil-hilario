<?php
/**
 * Inicialização de Sessão Segura
 * 
 * Este arquivo deve ser incluído ANTES de qualquer session_start()
 * para configurar as sessões de forma segura.
 */

// Verificar se a sessão ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    // Configurar sessão antes de iniciar
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    
    // Iniciar sessão
    session_start();
    
    // Gerar CSRF token se não existir
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    // Registrar início de sessão no log se disponível
    if (class_exists('Logger')) {
        $logger = new Logger();
        $logger->info('Sessão iniciada', [
            'session_id' => session_id(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    }
} else {
    // Sessão já está ativa, apenas verificar CSRF token
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
?> 