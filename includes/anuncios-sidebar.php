<?php
// Exibir anúncios na sidebar apenas em posts específicos, baseando-se na configuração de grupos

if (!isset($post) || !isset($post['id'])) {
    return;
}

$postId = (int)$post['id'];

require_once __DIR__ . '/GruposAnunciosManager.php';

try {
    $gruposManager = new GruposAnunciosManager($pdo);
    $gruposSidebar = $gruposManager->getGruposPorLocalizacao('sidebar', $postId, false);
    $debug = isset($_GET['debug_sidebar']) && $_GET['debug_sidebar'] == '1';

    if (empty($gruposSidebar)) {
        if ($debug) {
            error_log("SIDEBAR DEBUG: Nenhum grupo para postId={$postId}. Verifique se o grupo está ativo, localizacao='sidebar' e associado em grupos_anuncios_posts.");
        }
        return;
    }

    foreach ($gruposSidebar as $grupo) {
        $anuncios = $gruposManager->getAnunciosDoGrupo($grupo['id']);
        if (empty($anuncios)) {
            if ($debug) {
                error_log("SIDEBAR DEBUG: Grupo {$grupo['id']} sem anúncios ativos associados (grupos_anuncios_items).");
            }
            continue;
        }
        foreach ($anuncios as $anuncio) {
            if ($debug) {
                error_log("SIDEBAR DEBUG: Renderizando anuncio id={$anuncio['id']} marca=" . ($anuncio['marca'] ?? '')); 
            }
            echo '<li class="mb-3 anuncio-item">';
            echo '<div class="anuncio-card-sidebar">';
            echo '<div class="anuncio-patrocinado-badge-sidebar">Anúncio</div>';

            // Badge de marca por anúncio
            if (!empty($anuncio['marca'])) {
                echo '<div class="marca-badge" style="position:absolute;right:8px;top:8px;">';
                if ($anuncio['marca'] === 'shopee') {
                    echo '<span class="badge badge-shopee" style="font-size:10px;padding:2px 6px;"><i class="fas fa-shopping-cart"></i> Shopee</span>';
                } elseif ($anuncio['marca'] === 'amazon') {
                    echo '<span class="badge badge-amazon" style="font-size:10px;padding:2px 6px;"><i class="fab fa-amazon"></i> Amazon</span>';
                }
                echo '</div>';
            }

            if (!empty($anuncio['imagem'])) {
                echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" onclick="registrarCliqueAnuncio(' . (int)$anuncio['id'] . ', \'imagem\')">';
                echo '<img src="' . htmlspecialchars($anuncio['imagem']) . '" alt="' . htmlspecialchars($anuncio['titulo']) . '" class="anuncio-imagem-sidebar">';
                echo '</a>';
            }

            echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" style="font-size: 15px!important;padding: 0 0.9rem!important;font-weight: 500!important;" class="anuncio-titulo-sidebar" onclick="registrarCliqueAnuncio(' . (int)$anuncio['id'] . ', \'titulo\')">' . htmlspecialchars($anuncio['titulo']) . '</a>';

            echo '</div>';
            echo '</li>';
        }
    }
} catch (Exception $e) {
    error_log('Erro ao carregar anúncios da sidebar: ' . $e->getMessage());
}
?>