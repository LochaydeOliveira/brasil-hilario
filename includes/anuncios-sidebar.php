<?php
// Carregar grupos de anúncios para a sidebar
require_once __DIR__ . '/GruposAnunciosManager.php';

try {
    $gruposManager = new GruposAnunciosManager($pdo);
    $gruposSidebar = $gruposManager->getGruposPorLocalizacao('sidebar');
    
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
                        echo '<img src="assets/img/logo-shopee.png" alt="Shopee">';
                    } elseif ($grupo['marca'] === 'amazon') {
                        echo '<img src="assets/img/logo-amazon.png" alt="Amazon">';
                    }
                    echo '</div>';
                }
                
                if (!empty($anuncio['imagem']) && file_exists('.' . $anuncio['imagem'])) {
                    echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank">';
                    echo '<img src="' . htmlspecialchars($anuncio['imagem']) . '" alt="' . htmlspecialchars($anuncio['titulo']) . '" class="anuncio-imagem-sidebar">';
                    echo '</a>';
                }
                echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" class="anuncio-titulo-sidebar">' . htmlspecialchars($anuncio['titulo']) . '</a>';
                if ($anuncio['cta_ativo']) {
                    echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" class="anuncio-link-sidebar">' . htmlspecialchars($anuncio['cta_texto']) . '</a>';
                }
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