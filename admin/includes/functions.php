<?php
/**
 * Funções auxiliares do painel administrativo
 */

/**
 * Verifica se o usuário está logado
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Verifica se o usuário é administrador
 */
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

/**
 * Verifica se o usuário é editor
 */
function isEditor() {
    return isset($_SESSION['user_type']) && in_array($_SESSION['user_type'], ['admin', 'editor']);
}

/**
 * Verifica se o usuário tem permissão para acessar uma página
 */
function checkPermission($required_type = 'admin') {
    if (!isLoggedIn()) {
        redirect('login.php');
    }

    if ($required_type === 'admin' && !isAdmin()) {
        showError('Você não tem permissão para acessar esta página.');
        exit;
    }

    if ($required_type === 'editor' && !isEditor()) {
        showError('Você não tem permissão para acessar esta página.');
        exit;
    }
}

/**
 * Obtém o nome do usuário logado
 */
function getCurrentUserName() {
    return $_SESSION['user_name'] ?? 'Usuário';
}

/**
 * Obtém o tipo do usuário logado
 */
function getCurrentUserType() {
    return $_SESSION['user_type'] ?? 'autor';
}

/**
 * Obtém o ID do usuário logado
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? 0;
}

/**
 * Formata o tipo de usuário para exibição
 */
function formatUserType($type) {
    $types = [
        'admin' => '<span class="badge bg-danger">Administrador</span>',
        'editor' => '<span class="badge bg-warning">Editor</span>',
        'autor' => '<span class="badge bg-info">Autor</span>'
    ];
    return $types[$type] ?? $types['autor'];
}

/**
 * Formata o status do usuário para exibição
 */
function formatUserStatus($status) {
    $statuses = [
        'ativo' => '<span class="badge bg-success">Ativo</span>',
        'inativo' => '<span class="badge bg-secondary">Inativo</span>',
        'bloqueado' => '<span class="badge bg-danger">Bloqueado</span>'
    ];
    return $statuses[$status] ?? $statuses['inativo'];
}

/**
 * Obtém o menu do painel administrativo
 */
function getAdminMenu() {
    $menu = [
        [
            'title' => 'Dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'url' => 'index.php'
        ],
        [
            'title' => 'Posts',
            'icon' => 'fas fa-file-alt',
            'url' => 'posts.php'
        ],
        [
            'title' => 'Categorias',
            'icon' => 'fas fa-folder',
            'url' => 'categorias.php'
        ],
        [
            'title' => 'Tags',
            'icon' => 'fas fa-tags',
            'url' => 'tags.php'
        ],
        [
            'title' => 'Comentários',
            'icon' => 'fas fa-comments',
            'url' => 'comentarios.php'
        ]
    ];

    // Adiciona itens do menu apenas para administradores
    if (isAdmin()) {
        $menu[] = [
            'title' => 'Usuários',
            'icon' => 'fas fa-users',
            'url' => 'usuarios.php'
        ];
        $menu[] = [
            'title' => 'Configurações',
            'icon' => 'fas fa-cog',
            'url' => 'configuracoes.php'
        ];
    }

    return $menu;
}

/**
 * Obtém o título da página atual
 */
function getPageTitle() {
    $page = basename($_SERVER['PHP_SELF'], '.php');
    $titles = [
        'index' => 'Dashboard',
        'posts' => 'Posts',
        'novo-post' => 'Novo Post',
        'editar-post' => 'Editar Post',
        'categorias' => 'Categorias',
        'tags' => 'Tags',
        'comentarios' => 'Comentários',
        'usuarios' => 'Usuários',
        'configuracoes' => 'Configurações'
    ];
    return $titles[$page] ?? 'Painel Administrativo';
}

/**
 * Exibe uma mensagem de erro
 */
function showError($message) {
    return '<div class="alert alert-danger">' . $message . '</div>';
}

/**
 * Exibe uma mensagem de sucesso
 */
function showSuccess($message) {
    return '<div class="alert alert-success">' . $message . '</div>';
}

/**
 * Exibe uma mensagem de aviso
 */
function showWarning($message) {
    return '<div class="alert alert-warning">' . $message . '</div>';
}

/**
 * Exibe uma mensagem de informação
 */
function showInfo($message) {
    return '<div class="alert alert-info">' . $message . '</div>';
} 