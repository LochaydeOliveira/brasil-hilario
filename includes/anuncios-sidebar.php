<?php
// Carregar anúncios para a sidebar
try {
    $anunciosManager = new AnunciosManager($pdo);
    $anunciosSidebar = $anunciosManager->getAnunciosPorLocalizacao('sidebar');
    
    if (!empty($anunciosSidebar)) {
        foreach ($anunciosSidebar as $anuncio) {
            echo '<li class="mb-3 anuncio-item">';
            echo '<div style="border: 2px solid #ff6b6b; padding: 10px; margin: 10px 0; background: #fff3cd;">';
            echo '<div style="background: #ff6b6b; color: white; padding: 2px 8px; font-size: 12px; display: inline-block; margin-bottom: 5px;">PATROCINADO</div>';
            echo '<h4 style="margin: 0 0 5px 0; font-size: 14px;">' . htmlspecialchars($anuncio['titulo']) . '</h4>';
            if (!empty($anuncio['imagem']) && file_exists('.' . $anuncio['imagem'])) {
                echo '<img src="' . htmlspecialchars($anuncio['imagem']) . '" alt="' . htmlspecialchars($anuncio['titulo']) . '" style="max-width: 100%; height: auto; margin-bottom: 5px;">';
            }
            echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" style="color: #007bff; text-decoration: none;">Ver mais</a>';
            echo '</div>';
            echo '</li>';
        }
    }
} catch (Exception $e) {
    // Silenciar erros para não afetar o site
    error_log("Erro ao carregar anúncios da sidebar: " . $e->getMessage());
}
?> 