<?php
// Carregar anúncios para a sidebar
require_once __DIR__ . '/AnunciosManager.php';

try {
    $anunciosManager = new AnunciosManager($pdo);
    $anunciosSidebar = $anunciosManager->getAnunciosPorLocalizacao('sidebar');
    
    if (!empty($anunciosSidebar)) {
        foreach ($anunciosSidebar as $anuncio) {
            echo '<li class="mb-3 anuncio-item">';
            echo '<div class="anuncio-card-sidebar">';
            echo '<div class="anuncio-patrocinado-badge">PATROCINADO</div>';
            echo '<h4 class="anuncio-titulo-sidebar">' . htmlspecialchars($anuncio['titulo']) . '</h4>';
            if (!empty($anuncio['imagem']) && file_exists('.' . $anuncio['imagem'])) {
                echo '<img src="' . htmlspecialchars($anuncio['imagem']) . '" alt="' . htmlspecialchars($anuncio['titulo']) . '" class="anuncio-imagem-sidebar">';
            }
            echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" class="anuncio-link-sidebar">Ver mais</a>';
            echo '</div>';
            echo '</li>';
        }
    }
} catch (Exception $e) {
    // Silenciar erros para não afetar o site
    error_log("Erro ao carregar anúncios da sidebar: " . $e->getMessage());
}
?> 