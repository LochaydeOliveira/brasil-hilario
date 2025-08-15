<?php
// Carregar anúncios para a sidebar
require_once __DIR__ . '/GruposAnunciosManager.php';

try {
    $gruposManager = new GruposAnunciosManager($pdo);
    
    // Determinar se estamos na página inicial ou em um post específico
    // Verificar se estamos na página inicial de forma mais robusta
    $current_url = $_SERVER['REQUEST_URI'];
    $isHomePage = (
        $current_url === '/' || 
        $current_url === '/index.php' || 
        preg_match('/^\/\d+$/', $current_url) || // Páginas numeradas como /1, /2, etc.
        (basename($_SERVER['PHP_SELF']) === 'index.php' && !isset($_GET['slug']))
    );
    $postId = isset($post['id']) ? $post['id'] : null;
    
    $gruposSidebar = $gruposManager->getGruposPorLocalizacao('sidebar', $postId, $isHomePage);
    
    if (!empty($gruposSidebar)) {
        foreach ($gruposSidebar as $grupo) {
            $anuncios = $gruposManager->getAnunciosDoGrupo($grupo['id']);
            
            if (empty($anuncios)) continue;
            
            foreach ($anuncios as $anuncio) {
                echo '<li class="mb-3 anuncio-item">';
                echo '<div class="anuncio-card-sidebar">';
                echo '<div class="anuncio-patrocinado-badge-sidebar">Anúncio</div>';
                
                // Badge da marca
                if (!empty($grupo['marca'])) {
                    echo '<div class="marca-badge">';
                    if ($grupo['marca'] === 'shopee') {
                        echo '<img src="https://brasilhilario.com.br/assets/img/logo-shopee.png" alt="Shopee">';
                    } elseif ($grupo['marca'] === 'amazon') {
                        echo '<img src="https://brasilhilario.com.br/assets/img/logo-amazon.png" alt="Amazon">';
                    }
                    echo '</div>';
                }
                
                if (!empty($anuncio['imagem'])) {
                    echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" onclick="registrarCliqueAnuncio(' . $anuncio['id'] . ', \'imagem\')">';
                    echo '<img src="' . htmlspecialchars($anuncio['imagem']) . '" alt="' . htmlspecialchars($anuncio['titulo']) . '" class="anuncio-imagem-sidebar">';
                    echo '</a>';
                }
                echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" style="font-size: 15px!important;padding: 0 0.9rem!important;font-weight: 500!important;" class="anuncio-titulo-sidebar" onclick="registrarCliqueAnuncio(' . $anuncio['id'] . ', \'titulo\')">' . htmlspecialchars($anuncio['titulo']) . '</a>';
                echo '</div>';
                echo '</li>';
            }
        }
    }
    
} catch (Exception $e) {
    // Silenciar erros para não afetar o site
    error_log("Erro ao carregar anúncios da sidebar: " . $e->getMessage());
}
?> 