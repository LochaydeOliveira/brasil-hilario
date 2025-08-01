<?php
// Carregar anúncios para o conteúdo principal
try {
    $anunciosManager = new AnunciosManager($pdo);
    $anunciosConteudo = $anunciosManager->getAnunciosPorLocalizacao('conteudo');
    
    if (!empty($anunciosConteudo)) {
        foreach ($anunciosConteudo as $anuncio) {
            echo '<article class="blog-post mb-4 anuncio-sponsorizado">';
            echo '<div style="border: 2px solid #ff6b6b; padding: 15px; margin: 15px 0; background: #fff3cd;">';
            echo '<div style="background: #ff6b6b; color: white; padding: 2px 8px; font-size: 12px; display: inline-block; margin-bottom: 10px;">PATROCINADO</div>';
            echo '<h2 style="margin: 0 0 10px 0; font-size: 18px;">' . htmlspecialchars($anuncio['titulo']) . '</h2>';
            if (!empty($anuncio['imagem']) && file_exists('.' . $anuncio['imagem'])) {
                echo '<img src="' . htmlspecialchars($anuncio['imagem']) . '" alt="' . htmlspecialchars($anuncio['titulo']) . '" style="max-width: 100%; height: auto; margin-bottom: 10px;">';
            }
            echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" style="color: #007bff; text-decoration: none;">Ver mais</a>';
            echo '</div>';
            echo '</article>';
        }
    }
} catch (Exception $e) {
    // Silenciar erros para não afetar o site
    error_log("Erro ao carregar anúncios do conteúdo: " . $e->getMessage());
}
?> 