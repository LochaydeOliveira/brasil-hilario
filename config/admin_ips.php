<?php
/**
 * Configuração de IPs de Administradores
 * 
 * Este arquivo contém os IPs que devem ser excluídos da contagem de visualizações
 * para evitar distorção das estatísticas reais.
 */

// IPs de administradores que não devem contar visualizações
$ADMIN_IPS = [
    '179.48.2.57', // IP atual do admin
    '127.0.0.1',   // Localhost
    '::1',         // Localhost IPv6
    '192.168.1.1', // IP local comum
    '10.0.0.1',    // IP local comum
    '172.16.0.1',  // IP local comum
];

// Padrões de User-Agent para bots que devem ser filtrados
$BOT_PATTERNS = [
    'bot', 'crawler', 'spider', 'scraper', 
    'googlebot', 'bingbot', 'yandex', 'baiduspider',
    'facebookexternalhit', 'twitterbot', 'linkedinbot', 
    'whatsapp', 'telegrambot', 'slackbot', 'discordbot', 
    'skypebot', 'slurp', 'duckduckbot', 'ia_archiver',
    'archive.org_bot', 'wget', 'curl', 'python-requests',
    'apache-httpclient', 'okhttp', 'java-http-client'
];

// Função para verificar se um IP é de administrador
function isAdminIP($ip) {
    global $ADMIN_IPS;
    return in_array($ip, $ADMIN_IPS);
}

// Função para verificar se o User-Agent é de bot
function isBotUserAgent($user_agent) {
    global $BOT_PATTERNS;
    
    foreach ($BOT_PATTERNS as $pattern) {
        if (stripos($user_agent, $pattern) !== false) {
            return true;
        }
    }
    
    return false;
}

// Função para obter IP do usuário
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Função principal para verificar se deve contar visualização
function shouldCountView($post_id = null) {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $visitor_ip = getUserIP();
    
    // 1. Verificar se é um administrador logado
    if (isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_tipo'])) {
        if ($_SESSION['usuario_tipo'] === 'admin' || $_SESSION['usuario_tipo'] === 'editor') {
            if ($post_id) {
                require_once __DIR__ . '/view_logger.php';
                logFilteredView($post_id, 'admin_logged', $user_agent, $visitor_ip);
            }
            return false;
        }
    }
    
    // 2. Verificar User-Agent para bots
    if (isBotUserAgent($user_agent)) {
        if ($post_id) {
            require_once __DIR__ . '/view_logger.php';
            logFilteredView($post_id, 'bot_detected', $user_agent, $visitor_ip);
        }
        return false;
    }
    
    // 3. Verificar se é uma requisição AJAX (pode ser um bot)
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        if ($post_id) {
            require_once __DIR__ . '/view_logger.php';
            logFilteredView($post_id, 'ajax_request', $user_agent, $visitor_ip);
        }
        return false;
    }
    
    // 4. Verificar se já foi contado recentemente (cookie)
    $cookie_name = 'viewed_post_' . ($_GET['slug'] ?? '');
    if (isset($_COOKIE[$cookie_name])) {
        if ($post_id) {
            require_once __DIR__ . '/view_logger.php';
            logFilteredView($post_id, 'cookie_exists', $user_agent, $visitor_ip);
        }
        return false;
    }
    
    // 5. Verificar IPs de administradores
    if (isAdminIP($visitor_ip)) {
        if ($post_id) {
            require_once __DIR__ . '/view_logger.php';
            logFilteredView($post_id, 'admin_ip', $user_agent, $visitor_ip);
        }
        return false;
    }
    
    // Se chegou até aqui, deve contar a visualização
    if ($post_id) {
        require_once __DIR__ . '/view_logger.php';
        logCountedView($post_id, $visitor_ip);
    }
    
    return true;
}

// Função para definir cookie de visualização
function setViewCookie($post_slug) {
    $cookie_name = 'viewed_post_' . $post_slug;
    $cookie_value = '1';
    $cookie_expire = time() + (24 * 60 * 60); // 24 horas
    
    setcookie($cookie_name, $cookie_value, $cookie_expire, '/', '', false, true);
} 