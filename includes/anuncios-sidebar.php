<?php
// Exibir anúncios na sidebar apenas em posts específicos, com fallback robusto

$debug = isset($_GET['debug_sidebar']) && $_GET['debug_sidebar'] == '1';

// Determinar o ID do post atual de forma resiliente
$postId = null;
if (isset($post) && isset($post['id'])) {
    $postId = (int)$post['id'];
}

if ($postId === null) {
    // Tentar obter via slug na URL: /post/{slug}
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if (preg_match('#/post/([a-z0-9\-]+)#i', $uri, $m)) {
        $slug = $m[1];
        try {
            $stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND publicado = 1 LIMIT 1");
            $stmt->execute([$slug]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) { $postId = (int)$row['id']; }
        } catch (Exception $e) {
            if ($debug) error_log('SIDEBAR DEBUG: Erro ao obter postId por slug: ' . $e->getMessage());
        }
    }
}

if ($postId === null) {
    if ($debug) error_log('SIDEBAR DEBUG: postId indefinido. Não exibindo anúncios.');
    return;
}

require_once __DIR__ . '/GruposAnunciosManager.php';

try {
    $gruposManager = new GruposAnunciosManager($pdo);
    // 1) Tentar via grupos (modelo atual)
    $gruposSidebar = $gruposManager->getGruposPorLocalizacao('sidebar', $postId, false);

    $anunciosRender = [];
    if (!empty($gruposSidebar)) {
        foreach ($gruposSidebar as $grupo) {
            $anuncios = $gruposManager->getAnunciosDoGrupo($grupo['id']);
            foreach ($anuncios as $anuncio) {
                $anunciosRender[$anuncio['id']] = $anuncio;
            }
        }
    } else {
        if ($debug) error_log("SIDEBAR DEBUG: Nenhum grupo para postId={$postId}. Tentando fallback anuncios_posts...");
        // 2) Fallback legado: anuncios_posts
        $sql = "SELECT a.* FROM anuncios a
                INNER JOIN anuncios_posts ap ON ap.anuncio_id = a.id
                WHERE a.localizacao = 'sidebar' AND a.ativo = 1 AND ap.post_id = ?
                ORDER BY a.criado_em DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$postId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $anuncio) {
            $anunciosRender[$anuncio['id']] = $anuncio;
        }
    }

    if (empty($anunciosRender)) {
        if ($debug) error_log("SIDEBAR DEBUG: Nenhum anúncio para exibir no postId={$postId} (após grupos e fallback).");
        return;
    }

    foreach ($anunciosRender as $anuncio) {
        if ($debug) {
            error_log("SIDEBAR DEBUG: Renderizando anuncio id={$anuncio['id']} marca=" . ($anuncio['marca'] ?? ''));
        }
        echo '<li class="mb-3 anuncio-item">';
        echo '<div class="anuncio-card-sidebar">';
        echo '<div class="anuncio-patrocinado-badge-sidebar">Anúncio</div>';

        // Badge de marca
        if (!empty($anuncio['marca'])) {
            echo '<div class="marca-badge">';
            if ($anuncio['marca'] === 'shopee') {
                echo '<span class="brand-badge badge-shopee"><i class="fas fa-shopping-cart"></i> Shopee</span>';
            } elseif ($anuncio['marca'] === 'amazon') {
                echo '<span class="brand-badge badge-amazon"><i class="fab fa-amazon"></i> Amazon</span>';
            }
            echo '</div>';
        }

        if (!empty($anuncio['imagem'])) {
            echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" onclick="registrarCliqueAnuncio(' . (int)$anuncio['id'] . ', \'imagem\')">';
            echo '<img src="' . htmlspecialchars($anuncio['imagem']) . '" alt="' . htmlspecialchars($anuncio['titulo']) . '" class="anuncio-imagem-sidebar" loading="lazy" decoding="async">';
            echo '</a>';
        }

        echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" style="font-size: 15px!important;padding: 0 0.9rem!important;font-weight: 500!important;" class="anuncio-titulo-sidebar" onclick="registrarCliqueAnuncio(' . (int)$anuncio['id'] . ', \'titulo\')">' . htmlspecialchars($anuncio['titulo']) . '</a>';

        echo '</div>';
        echo '</li>';
    }
} catch (Exception $e) {
    error_log('Erro ao carregar anúncios da sidebar: ' . $e->getMessage());
}
?>