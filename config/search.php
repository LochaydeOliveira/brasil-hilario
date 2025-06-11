<?php
// Configurações da busca
define('SEARCH_RESULTS_PER_PAGE', 12);
define('SEARCH_EXCERPT_LENGTH', 150);

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