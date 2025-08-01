<?php
// Carregar grupos de anúncios para o conteúdo principal
require_once __DIR__ . '/GruposAnunciosManager.php';

try {
    $gruposManager = new GruposAnunciosManager($pdo);
    $gruposConteudo = $gruposManager->getGruposPorLocalizacao('conteudo');
    
    if (!empty($gruposConteudo)) {
        foreach ($gruposConteudo as $grupo) {
            $anuncios = $gruposManager->getAnunciosDoGrupo($grupo['id']);
            
            if (empty($anuncios)) continue;
            
            // Limitar a 8 anúncios para grade
            if ($grupo['layout'] === 'grade') {
                $anuncios = array_slice($anuncios, 0, 8);
            }
            
            if ($grupo['layout'] === 'grade') {
                // Layout de Grade (máximo 8 anúncios)
                echo '<div class="grupo-anuncios-grade" data-grupo-id="' . $grupo['id'] . '">';
                echo '<div class="anuncios-grade-grid">';
                foreach ($anuncios as $anuncio) {
                    echo '<div class="anuncio-card-grade">';
                    echo '<div class="anuncio-patrocinado-badge">Patrocinado</div>';
                    
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
                    
                    if (!empty($anuncio['imagem']) && file_exists('.' . $anuncio['imagem'])) {
                        echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" class="anuncio-imagem-link">';
                        echo '<img src="' . htmlspecialchars($anuncio['imagem']) . '" alt="' . htmlspecialchars($anuncio['titulo']) . '" class="anuncio-imagem-grade">';
                        echo '</a>';
                    }
                    echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" class="anuncio-titulo-grade">' . htmlspecialchars($anuncio['titulo']) . '</a>';
                    if ($anuncio['cta_ativo']) {
                        echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" class="anuncio-link-grade">' . htmlspecialchars($anuncio['cta_texto']) . '</a>';
                    }
                    echo '</div>';
                }
                echo '</div>';
                echo '</div>';
                
            } else {
                // Layout de Carrossel (ilimitado)
                echo '<div class="grupo-anuncios-carrossel" data-grupo-id="' . $grupo['id'] . '">';
                echo '<div class="anuncios-carrossel-wrapper">';
                echo '<div class="anuncios-carrossel">';
                foreach ($anuncios as $anuncio) {
                    echo '<div class="anuncio-card-carrossel">';
                    echo '<div class="anuncio-patrocinado-badge">Patrocinado</div>';
                    
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
                    
                    if (!empty($anuncio['imagem']) && file_exists('.' . $anuncio['imagem'])) {
                        echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" class="anuncio-imagem-link">';
                        echo '<img src="' . htmlspecialchars($anuncio['imagem']) . '" alt="' . htmlspecialchars($anuncio['titulo']) . '" class="anuncio-imagem-carrossel">';
                        echo '</a>';
                    }
                    echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" class="anuncio-titulo-carrossel">' . htmlspecialchars($anuncio['titulo']) . '</a>';
                    if ($anuncio['cta_ativo']) {
                        echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" class="anuncio-link-carrossel">' . htmlspecialchars($anuncio['cta_texto']) . '</a>';
                    }
                    echo '</div>';
                }
                echo '</div>';
                echo '<button class="carrossel-btn carrossel-btn-prev" onclick="scrollCarrossel(' . $grupo['id'] . ', \'left\')">‹</button>';
                echo '<button class="carrossel-btn carrossel-btn-next" onclick="scrollCarrossel(' . $grupo['id'] . ', \'right\')">›</button>';
                echo '</div>';
                echo '</div>';
            }
        }
    }
} catch (Exception $e) {
    // Silenciar erros para não afetar o site
    error_log("Erro ao carregar grupos de anúncios: " . $e->getMessage());
}
?> 