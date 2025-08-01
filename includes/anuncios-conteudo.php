<?php
// Carregar anúncios para o conteúdo principal
require_once __DIR__ . '/AnunciosManager.php';

try {
    $anunciosManager = new AnunciosManager($pdo);
    $anunciosConteudo = $anunciosManager->getAnunciosPorLocalizacao('conteudo');
    
    if (!empty($anunciosConteudo)) {
        foreach ($anunciosConteudo as $anuncio) {
            echo '<article class="blog-post mb-4 anuncio-sponsorizado">';
            echo '<div class="anuncio-card-conteudo">';
            echo '<div class="anuncio-patrocinado-badge">PATROCINADO</div>';
            echo '<h3 class="anuncio-titulo-conteudo">' . htmlspecialchars($anuncio['titulo']) . '</h3>';
            if (!empty($anuncio['imagem']) && file_exists('.' . $anuncio['imagem'])) {
                echo '<img src="' . htmlspecialchars($anuncio['imagem']) . '" alt="' . htmlspecialchars($anuncio['titulo']) . '" class="anuncio-imagem-conteudo">';
            }
            echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" class="anuncio-link-conteudo">Ver mais</a>';
            echo '</div>';
            echo '</article>';
        }
    }
} catch (Exception $e) {
    // Silenciar erros para não afetar o site
    error_log("Erro ao carregar anúncios do conteúdo: " . $e->getMessage());
}
?> 