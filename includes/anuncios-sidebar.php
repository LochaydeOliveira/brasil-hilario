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

    if (empty($gruposSidebar)) {
        return;
    }

    foreach ($gruposSidebar as $grupo) {
        $anuncios = $gruposManager->getAnunciosDoGrupo($grupo['id']);
        if (empty($anuncios)) {
            continue;
        }
        foreach ($anuncios as $anuncio) {
            echo '<li class="mb-3 anuncio-item">';
            echo '<div class="anuncio-card-sidebar">';
            echo '<div class="anuncio-patrocinado-badge-sidebar">Anúncio</div>';

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