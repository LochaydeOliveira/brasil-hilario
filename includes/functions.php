<?php
/**
 * Funções auxiliares do sistema
 */

/**
 * Verifica se o usuário está logado
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Gera um slug a partir de um texto
 */
function generateSlug($text) {
    // Converte para minúsculas
    $text = mb_strtolower($text, 'UTF-8');
    
    // Remove acentos
    $text = preg_replace('/[áàãâä]/ui', 'a', $text);
    $text = preg_replace('/[éèêë]/ui', 'e', $text);
    $text = preg_replace('/[íìîï]/ui', 'i', $text);
    $text = preg_replace('/[óòõôö]/ui', 'o', $text);
    $text = preg_replace('/[úùûü]/ui', 'u', $text);
    $text = preg_replace('/[ç]/ui', 'c', $text);
    
    // Remove caracteres especiais
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    
    // Substitui espaços por hífens
    $text = preg_replace('/[\s-]+/', '-', $text);
    
    // Remove hífens do início e fim
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Limpa e valida dados de entrada
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Formata uma data
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    return date($format, strtotime($date));
}

/**
 * Obtém o status formatado de um post
 */
function getPostStatus($status) {
    $statuses = [
        0 => '<span class="badge bg-warning">Rascunho</span>',
        1 => '<span class="badge bg-success">Publicado</span>'
    ];
    return $statuses[$status] ?? $statuses[0];
}

/**
 * Obtém o nome de uma categoria pelo ID
 */
function getCategoryName($pdo, $category_id) {
    $stmt = $pdo->prepare("SELECT nome FROM categorias WHERE id = ?");
    $stmt->execute([$category_id]);
    return $stmt->fetchColumn() ?: 'Sem categoria';
}

/**
 * Obtém as tags de um post
 */
function getPostTags($pdo, $post_id) {
    $stmt = $pdo->prepare("
        SELECT t.* 
        FROM tags t 
        JOIN post_tags pt ON t.id = pt.tag_id 
        WHERE pt.post_id = ?
    ");
    $stmt->execute([$post_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtém o nome de um usuário pelo ID
 */
function getUserName($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn() ?: 'Usuário desconhecido';
}

/**
 * Verifica se uma string é um JSON válido
 */
function isJson($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Formata um número para exibição
 */
function formatNumber($number) {
    return number_format($number, 0, ',', '.');
}

/**
 * Obtém a URL base do site
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'];
}

/**
 * Redireciona para uma URL
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
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

/**
 * Busca uma categoria pelo slug
 */
function get_categoria_by_slug($slug) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM categorias WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

/**
 * Busca posts por categoria com paginação
 */
function get_posts_by_categoria($categoria_id, $limit, $offset) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT p.*, u.nome as autor_nome 
        FROM posts p 
        LEFT JOIN usuarios u ON p.autor_id = u.id 
        WHERE p.categoria_id = ? AND p.status = 'publicado' 
        ORDER BY p.data_publicacao DESC 
        LIMIT ? OFFSET ?
    ");
    
    $stmt->bind_param("iii", $categoria_id, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    
    return $posts;
}

/**
 * Conta o total de posts em uma categoria
 */
function get_total_posts_by_categoria($categoria_id) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total 
        FROM posts 
        WHERE categoria_id = ? AND status = 'publicado'
    ");
    
    $stmt->bind_param("i", $categoria_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'];
} 