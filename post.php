<?php
session_start();
require_once 'includes/db.php';
require_once 'config/config.php';
require_once 'config/admin_ips.php';
require_once 'config/search.php';
require_once 'vendor/autoload.php';

try {
    $slug = $_GET['slug'] ?? '';
    
    if (empty($slug)) {
        header('Location: ' . BLOG_URL);
        exit;
    }

    // Buscar o post
    $stmt = $pdo->prepare("
        SELECT p.*, c.nome as categoria_nome, c.slug as categoria_slug, u.nome as autor_nome
        FROM posts p 
        JOIN categorias c ON p.categoria_id = c.id 
        LEFT JOIN usuarios u ON p.autor_id = u.id 
        WHERE p.slug = ? AND p.publicado = 1
    ");
    $stmt->execute([$slug]);
    $post = $stmt->fetch();

    if (!$post) {
        header('HTTP/1.0 404 Not Found');
        include '404.php';
        exit;
    }

    // Buscar tags do post
    $stmt_tags = $pdo->prepare("
        SELECT t.id, t.nome, t.slug 
        FROM post_tags pt 
        JOIN tags t ON pt.tag_id = t.id 
        WHERE pt.post_id = ?
        ORDER BY t.nome ASC
    ");
    $stmt_tags->execute([$post['id']]);
    $post['tags'] = $stmt_tags->fetchAll();

    // Buscar posts relacionados (mesma categoria, excluindo o post atual)
    $stmt_related = $pdo->prepare("
        SELECT p.id, p.titulo, p.slug, p.imagem_destacada, c.nome as categoria_nome
        FROM posts p 
        JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.categoria_id = ? AND p.id != ? AND p.publicado = 1
        ORDER BY p.data_publicacao DESC 
        LIMIT 4
    ");
    $stmt_related->execute([$post['categoria_id'], $post['id']]);
    $related_posts = $stmt_related->fetchAll();

    // Buscar últimas notícias (posts mais recentes)
    $stmt_latest = $pdo->prepare("
        SELECT p.id, p.titulo, p.slug, p.imagem_destacada, c.nome as categoria_nome
        FROM posts p 
        JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.publicado = 1 AND p.id != ?
        ORDER BY p.data_publicacao DESC 
        LIMIT 4
    ");
    $stmt_latest->execute([$post['id']]);
    $latest_posts = $stmt_latest->fetchAll();

    // Verificar se deve contar a visualização
    if (shouldCountView($post['id'])) {
        // Incrementar visualizações
        $stmt_update = $pdo->prepare("UPDATE posts SET visualizacoes = visualizacoes + 1 WHERE id = ?");
        $stmt_update->execute([$post['id']]);
        
        // Definir cookie para evitar contagem duplicada
        setViewCookie($slug);
    }

    $og_title = htmlspecialchars($post['titulo']);
    $og_description = !empty($post['resumo']) ? htmlspecialchars($post['resumo']) : htmlspecialchars(generate_excerpt($post['conteudo'], 200));
    $og_url = BLOG_URL . '/post/' . htmlspecialchars($post['slug']);
    $og_image = !empty($post['imagem_destacada']) ? BLOG_URL . '/uploads/images/' . htmlspecialchars($post['imagem_destacada']) : BLOG_URL . '/assets/img/logo-brasil-hilario-quadrada-svg.svg';
    $meta_description = $og_description;
    $meta_keywords = implode(', ', array_column($post['tags'], 'nome')) . ', ' . META_KEYWORDS;

} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}

function buildPostsSectionHtml($title, $posts) {
    if (empty($posts)) {
        return '';
    }

    $section_html = '<section class="related-posts-block my-5">';
    $section_html .= '<h4 class="related-posts-title">' . htmlspecialchars($title) . '</h4>';
    $section_html .= '<div class="row">';

    foreach ($posts as $p) {
        $post_url = BLOG_URL . '/post/' . htmlspecialchars($p['slug']);
        $image_path = !empty($p['imagem_destacada']) ? BLOG_URL . '/uploads/images/' . htmlspecialchars($p['imagem_destacada']) : BLOG_URL . '/assets/img/logo-brasil-hilario-para-og.png';

        $section_html .= '<div class="col-lg-3 col-md-6 mb-4">';
        $section_html .= '<a href="' . $post_url . '" class="related-post-link">';
        $section_html .= '<div class="card h-100 related-post-card">';
        $section_html .= '<img src="' . $image_path . '" class="related-post-img" alt="' . htmlspecialchars($p['titulo']) . '">';
        $section_html .= '<div class="pad-01 d-flex flex-column ">';
        $section_html .= '<h6 class="card-title related-post-title mt-auto">' . htmlspecialchars($p['titulo']) . '</h6>';
        $section_html .= '<div><span class="badge mb-2">' . htmlspecialchars($p['categoria_nome']) . '</span></div>';
        $section_html .= '</div>';
        $section_html .= '</div>';
        $section_html .= '</a>';
        $section_html .= '</div>';
    }

    $section_html .= '</div>';
    $section_html .= '</section>';

    // Bloco Google AdSense logo após a seção
    $section_html .= <<<HTML
<div class="adsense-block my-4 text-center">
    <ins class="adsbygoogle"
         style="display:block"
         data-ad-format="autorelaxed"
         data-ad-client="ca-pub-8313157699231074"
         data-ad-slot="2883155880">
    </ins>
    <script>
         (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
</div>
HTML;

    return $section_html;
}

function injectSections($content, $sections) {
    if (empty($sections)) {
        return $content;
    }

    if (stripos($content, '</p>') === false) {
        return $content;
    }

    $paragraphs = explode('</p>', $content);

    usort($sections, function($a, $b) {
        return $a['point'] <=> $b['point'];
    });

    $offset = 0;
    foreach ($sections as $section) {
        $injection_point = $section['point'] + $offset;
        $section_html = buildPostsSectionHtml($section['title'], $section['posts']);
        if (empty($section_html) || count($paragraphs) < $injection_point + 1) {
            continue;
        }
        array_splice($paragraphs, $injection_point, 0, $section_html);
        $offset++;
    }

    return implode('</p>', $paragraphs);
}

include 'includes/header.php';
?>


<div class="container">
    <div class="row">
        <div class="col-md-8">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BLOG_URL; ?>">Início</a></li>
                    <?php if (isset($post['categoria_nome'])): ?>
                        <li class="breadcrumb-item"><a href="<?php echo BLOG_PATH; ?>/categoria/<?php echo htmlspecialchars($post['categoria_nome']); ?>">
                            <?php echo htmlspecialchars($post['categoria_nome']); ?>
                        </a></li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?php echo htmlspecialchars($post['titulo']); ?>
                    </li>
                </ol>
            </nav>

            <h1 class="mt-4 mb-3 title-posts"><?php echo htmlspecialchars($post['titulo']); ?></h1>



            <?php if (!empty($post['autor_id'])): ?>
                <p class="lead">
                    por <a href="<?php echo BLOG_URL; ?>/autor/<?php echo $post['autor_id']; ?>">
                        <?php echo htmlspecialchars($post['autor_nome']); ?>
                    </a>
                </p>
            <?php else: ?>
                <p class="lead">por <?php echo htmlspecialchars($post['autor_nome']); ?></p>
            <?php endif; ?>


            <hr>

            <div class="post-meta mb-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="meta-info">
                        <span class="me-3"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($post['data_publicacao'])); ?></span>
                        <span class="me-3"><i class="far fa-folder"></i> <a href="<?php echo BLOG_URL; ?>/categoria/<?php echo htmlspecialchars($post['categoria_slug']); ?>"><?php echo htmlspecialchars($post['categoria_nome']); ?></a></span>
                        <span><i class="far fa-eye"></i> <?php echo number_format($post['visualizacoes']); ?> visualizações</span>
                    </div>

                    <!-- Botões de Compartilhamento -->
                    <div class="social-sharing-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(BLOG_URL . '/post/' . $post['slug']); ?>"
                        target="_blank" class="social-share-btn facebook-share" aria-label="Compartilhar no Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
                            </svg>
                        </a>
                        
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(BLOG_URL . '/post/' . $post['slug']); ?>&text=<?php echo urlencode($post['titulo']); ?>"
                        target="_blank" class="social-share-btn twitter-share" aria-label="Compartilhar no Twitter">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>
                            </svg>
                        </a>
                        
                        <a href="https://wa.me/?text=<?php echo urlencode($post['titulo'] . ' ' . BLOG_URL . '/post/' . $post['slug']); ?>"
                        target="_blank" class="social-share-btn whatsapp-share" aria-label="Compartilhar no WhatsApp">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1 1.007-3.505c.176-.341.376-.65.596-.918a6.545 6.545 0 0 1 1.48-1.168 6.56 6.56 0 0 1 3.842-1.195c1.747 0 3.386.636 4.6 1.792a6.574 6.574 0 0 1 1.601 4.158c-.004 1.697-.66 3.296-1.843 4.5a6.59 6.59 0 0 1-4.6 1.92z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="post-content">
                <?php 
                $content_to_display = '';
                if ($post['editor_type'] === 'markdown') {
                    $parsedown = new Parsedown();
                    $content_to_display = $parsedown->text($post['conteudo']);
                } else {
                    $content_to_display = $post['conteudo'];
                }
                
                $sections_to_inject = [];

                $first_injection_point = 5;
                $second_injection_point = 11;

                if (!empty($related_posts)) {
                    $sections_to_inject[] = [
                        'title' => 'Leia Também',
                        'posts' => $related_posts,
                        'point' => $first_injection_point
                    ];
                }

                if (!empty($latest_posts)) {
                    $sections_to_inject[] = [
                        'title' => 'Últimas do Portal',
                        'posts' => $latest_posts,
                        'point' => $second_injection_point
                    ];
                }

                echo injectSections($content_to_display, $sections_to_inject);
                ?>
            </div>

            <hr>

            <?php 
            // Incluir grupos de anúncios do conteúdo principal
            include 'includes/grupos-anuncios-conteudo.php';
            ?>

            <?php if (!empty($post['tags'])): ?>
            <div class="post-tags mb-3">
                <i class="fas fa-tags"></i>
                <?php foreach ($post['tags'] as $tag): ?>
                <span class="badge bg-secondary text-decoration-none me-1">
                    <?php echo htmlspecialchars($tag['nome']); ?>
                </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>


            <div class="card my-4">
                <h5 class="card-header">Deixe um Comentário:</h5>
                <div class="card-body">
                    <div id="fb-root"></div>
                    <script async defer crossorigin="anonymous" 
                            src="https://connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v10.0&appId=YOUR_FACEBOOK_APP_ID&autoLogAppEvents=1">
                    </script>
                    <div class="fb-comments" 
                         data-href="<?php echo BLOG_URL . '/post/' . $post['slug']; ?>" 
                         data-width="100%" data-numposts="5">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <?php include 'includes/sidebar.php'; ?>
        </div>

    </div>

    <ins class="adsbygoogle"
        style="display:block; text-align:center;"
        data-ad-layout="in-article"
        data-ad-format="fluid"
        data-ad-client="ca-pub-8313157699231074"
        data-ad-slot="7748469758">
    </ins>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
</div>

<?php include 'includes/footer.php'; ?>
<?php ob_end_flush(); ?> 