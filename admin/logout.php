<?php
/**
 * Arquivo: logout.php
 * Descrição: Encerramento de sessão do painel administrativo
 * Funcionalidades:
 * - Destrói a sessão atual
 * - Limpa todas as variáveis de sessão
 * - Redireciona para a página de login
 */

// Inclui arquivos necessários
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Destrói o cookie da sessão se existir
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destrói a sessão
session_destroy();

// Redireciona para a página de login
redirect('login.php');
?> 