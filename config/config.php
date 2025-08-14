<?php
// Configurações do Blog
define('BLOG_TITLE', 'Brasil Hilário');
define('BLOG_DESCRIPTION', 'Conteúdo diário sobre política, futebol, tecnologia, culinária, mundo animal e muito mais.');
define('BLOG_URL', 'https://www.brasilhilario.com.br');
define('BLOG_PATH', ''); // Caminho relativo vazio para a raiz

// Configurações de SEO
define('META_KEYWORDS', 'humor, piadas, memes, vídeos engraçados, notícias engraçadas, brasil hilário');
define('META_DESCRIPTION', 'O melhor conteúdo de humor do Brasil. Piadas, memes, vídeos engraçados e muito mais!');

// Configurações de Cache
define('CACHE_ENABLED', true);
define('CACHE_TIME', 3600); // 1 hora
define('CACHE_DIR', __DIR__ . '/../cache');

// Configurações de Logs
define('LOG_ENABLED', true);
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR, CRITICAL
define('LOG_DIR', __DIR__ . '/../logs');

// Configurações de Posts
define('POSTS_PER_PAGE', 10);
define('EXCERPT_LENGTH', 200);

// Configurações de Mídia
define('UPLOAD_MAX_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('UPLOAD_PATH', 'uploads');
define('UPLOAD_URL', BLOG_URL . '/uploads');

// Configurações de Segurança
define('ADMIN_EMAIL', 'admin@brasilhilario.com.br');
define('SECURE_AUTH_KEY', $_ENV['SECURE_AUTH_KEY'] ?? bin2hex(random_bytes(32)));

// Configurações de Cache do AdSense
define('ADSENSE_CLIENT_ID', 'ca-pub-8313157699231074');
define('ADSENSE_SLOT_ID', 'XXXXXXXXXX');

// Configurações de URLs
define('ADMIN_URL', BLOG_URL . '/admin');
define('API_URL', BLOG_URL . '/api');
define('ASSETS_URL', BLOG_URL . '/assets');

// Configurações de Diretórios
define('ROOT_PATH', '');
define('INCLUDES_PATH', 'includes');
define('ADMIN_PATH', 'admin');
define('API_PATH', 'api');
define('ASSETS_PATH', 'assets');

// Configurações de .htaccess
define('ENABLE_HTACCESS', true);
define('HTACCESS_BASE', '');

// Configurações de Sitemap
define('SITEMAP_PATH', 'sitemap.xml');
define('SITEMAP_URL', BLOG_URL . '/sitemap.xml');

// Configurações de Robots.txt
define('ROBOTS_PATH', 'robots.txt');
define('ROBOTS_URL', BLOG_URL . '/robots.txt');

// Configurações de Backup
define('BACKUP_ENABLED', true);
define('BACKUP_DIR', __DIR__ . '/../backups');
define('BACKUP_KEEP_COUNT', 10); // Manter apenas os últimos 10 backups

// Configurações de Newsletter
define('NEWSLETTER_ENABLED', true);
define('NEWSLETTER_FROM_EMAIL', 'noreply@brasilhilario.com.br');
define('NEWSLETTER_FROM_NAME', 'Brasil Hilário');

// Configurações de Validação
define('VALIDATION_ENABLED', true);
define('CSRF_ENABLED', true);
define('RECAPTCHA_ENABLED', false);
define('RECAPTCHA_SITE_KEY', '');
define('RECAPTCHA_SECRET_KEY', '');

// Configurações de Performance
define('MINIFY_CSS', true);
define('MINIFY_JS', true);
define('COMPRESS_IMAGES', true);
define('LAZY_LOADING', true);

// Configurações de Monitoramento
define('MONITORING_ENABLED', true);
define('ERROR_REPORTING', E_ALL);
define('DISPLAY_ERRORS', false);

// Configurações de Páginas
define('PAGES', [
    'sobre' => [
        'title' => 'Sobre Nós',
        'slug' => 'sobre',
        'url' => BLOG_URL . '/sobre'
    ],
    'contato' => [
        'title' => 'Contato',
        'slug' => 'contato',
        'url' => BLOG_URL . '/contato'
    ]
]);

// Configurações de Páginas Legais
define('LEGAL_PAGES', [
    'privacidade' => [
        'title' => 'Política de Privacidade',
        'slug' => 'privacidade',
        'url' => BLOG_URL . '/privacidade'
    ],
    'termos' => [
        'title' => 'Termos de Uso',
        'slug' => 'termos',
        'url' => BLOG_URL . '/termos'
    ]
]);

// Configurações de Redes Sociais
define('SOCIAL_MEDIA', [
    'facebook' => 'https://facebook.com/brasilhilario',
    'twitter' => 'https://twitter.com/brasilhilario',
    'instagram' => 'https://instagram.com/brasilhilario',
    'youtube' => 'https://youtube.com/brasilhilario'
]);

// Configurações de Analytics
define('GOOGLE_ANALYTICS_ID', '');
define('FACEBOOK_PIXEL_ID', '');

// Configurações de Integração
define('INTEGRATION_ENABLED', true);
define('API_RATE_LIMIT', 100); // Requisições por hora
define('API_TIMEOUT', 30); // Segundos

// Configurações de Ambiente
define('ENVIRONMENT', $_ENV['ENVIRONMENT'] ?? 'production'); // development, staging, production
define('DEBUG_MODE', ENVIRONMENT === 'development');

// Configurações de Timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de Sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Configurações de Headers de Segurança
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
}

// Configurações de Erro
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Configurações de Log
if (LOG_ENABLED) {
    ini_set('log_errors', 1);
    ini_set('error_log', LOG_DIR . '/php_errors.log');
}

// Configurações de Cache
if (CACHE_ENABLED && !is_dir(CACHE_DIR)) {
    mkdir(CACHE_DIR, 0755, true);
}

// Configurações de Logs
if (LOG_ENABLED && !is_dir(LOG_DIR)) {
    mkdir(LOG_DIR, 0755, true);
}

// Configurações de Backup
if (BACKUP_ENABLED && !is_dir(BACKUP_DIR)) {
    mkdir(BACKUP_DIR, 0755, true);
} 