<?php
// Configurações da busca
define('SEARCH_RESULTS_PER_PAGE', 12);
define('SEARCH_EXCERPT_LENGTH', 150);
define('SEARCH_HISTORY_LIMIT', 5);
define('SEARCH_SUGGESTIONS_LIMIT', 5);

// Função para limpar o termo de busca
function clean_search_term($term) {
    $term = trim($term);
    $term = strip_tags($term);
    $term = htmlspecialchars($term, ENT_QUOTES, 'UTF-8');
    return $term;
}

// Função para gerar excerpt do conteúdo
function generate_excerpt($content, $length = SEARCH_EXCERPT_LENGTH) {
    $content = strip_tags($content);
    if (strlen($content) <= $length) {
        return $content;
    }
    return substr($content, 0, $length) . '...';
}

// Função para destacar o termo pesquisado no texto
function highlight_search_term($text, $term) {
    if (empty($term)) return $text;
    $pattern = '/(' . preg_quote($term, '/') . ')/i';
    return preg_replace($pattern, '<mark>$1</mark>', $text);
}

// Função para salvar termo de busca no histórico
function save_search_history($term) {
    if (empty($term)) return;
    
    $history = isset($_SESSION['search_history']) ? $_SESSION['search_history'] : [];
    array_unshift($history, $term);
    $history = array_unique($history);
    $history = array_slice($history, 0, SEARCH_HISTORY_LIMIT);
    $_SESSION['search_history'] = $history;
}

// Função para obter sugestões de busca
function get_search_suggestions($term) {
    global $conn;
    
    try {
        $term = '%' . $term . '%';
        $sql = "SELECT DISTINCT titulo 
                FROM posts 
                WHERE publicado = 1 
                AND titulo LIKE ? 
                LIMIT " . SEARCH_SUGGESTIONS_LIMIT;
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $term);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $suggestions = [];
        while ($row = $result->fetch_assoc()) {
            $suggestions[] = $row['titulo'];
        }
        
        return $suggestions;
    } catch (Exception $e) {
        error_log("Erro ao buscar sugestões: " . $e->getMessage());
        return [];
    }
}

// Função para gerar paginação
function generate_pagination($total_results, $current_page, $results_per_page) {
    $total_pages = ceil($total_results / $results_per_page);
    if ($total_pages <= 1) return '';
    
    $html = '<nav aria-label="Navegação da busca"><ul class="pagination justify-content-center">';
    
    // Botão anterior
    $prev_disabled = $current_page <= 1 ? ' disabled' : '';
    $html .= '<li class="page-item' . $prev_disabled . '">';
    $html .= '<a class="page-link" href="?q=' . urlencode($_GET['q']) . '&pagina=' . ($current_page - 1) . '" aria-label="Anterior">';
    $html .= '<span aria-hidden="true">&laquo;</span></a></li>';
    
    // Páginas
    for ($i = 1; $i <= $total_pages; $i++) {
        $active = $i == $current_page ? ' active' : '';
        $html .= '<li class="page-item' . $active . '">';
        $html .= '<a class="page-link" href="?q=' . urlencode($_GET['q']) . '&pagina=' . $i . '">' . $i . '</a></li>';
    }
    
    // Botão próximo
    $next_disabled = $current_page >= $total_pages ? ' disabled' : '';
    $html .= '<li class="page-item' . $next_disabled . '">';
    $html .= '<a class="page-link" href="?q=' . urlencode($_GET['q']) . '&pagina=' . ($current_page + 1) . '" aria-label="Próximo">';
    $html .= '<span aria-hidden="true">&raquo;</span></a></li>';
    
    $html .= '</ul></nav>';
    return $html;
} 