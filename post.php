<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1); 

ob_start();
session_start();

require_once 'config/config.php';
require_once 'includes/db.php';  // aqui deve instanciar $pdo
require_once 'config/search.php';
require_once 'vendor/autoload.php';

$post_slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_URL);

if (empty($post_slug)) {
    header('Location: ' . BLOG_URL);
    exit;
}

try {
    // Buscar post principal com tags e categoria
    $stmt = $pdo->prepare("
        SELECT p.*, c.nome as categoria_nome, c.slug as categoria_slug,
               GROUP_CONCAT(DISTINCT CONCAT(t.id, ':', t.nome, ':', t.slug)) as tags_data
        FROM posts p
        JOIN categorias c ON p.categoria_id = c.id
        LEFT JOIN post_tags pt ON p.id = pt.post_id
        LEFT JOIN tags t ON pt.tag_id = t.id
        WHERE p.slug = ? AND p.publicado = 1
        GROUP BY p.id
    ");
    $stmt->execute([$post_slug]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        header('Location: ' . BLOG_URL . '/404.php');
        exit;
    }

    // Buscar nome do autor
    $autor_nome = 'Autor desconhecido';
    if (!empty($post['autor_id'])) {
        $stmt_autor = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ? LIMIT 1");
        $stmt_autor->execute([$post['autor_id']]);
        $autor = $stmt_autor->fetch(PDO::FETCH_ASSOC);
        if ($autor) {
            $autor_nome = $autor['nome'];
        }
    }

    // Posts relacionados (mesma categoria, diferente post)
    $related_posts = [];
    if (isset($post['categoria_id'])) {
        $stmt_related = $pdo->prepare("
            SELECT p.titulo, p.slug, p.imagem_destacada, c.nome as categoria_nome, c.slug as categoria_slug
            FROM posts p
            JOIN categorias c ON p.categoria_id = c.id
            WHERE p.categoria_id = ? AND p.id != ? AND p.publicado = 1
            ORDER BY RAND()
            LIMIT 4
        ");
        $stmt_related->execute([$post['categoria_id'], $post['id']]);
        $related_posts = $stmt_related->fetchAll(PDO::FETCH_ASSOC);
    }

    // Últimos posts (exceto o atual)
    $latest_posts = [];
    $stmt_latest = $pdo->prepare("
        SELECT p.titulo, p.slug, p.imagem_destacada, c.nome as categoria_nome, c.slug as categoria_slug
        FROM posts p
        JOIN categorias c ON p.categoria_id = c.id
        WHERE p.id != ? AND p.publicado = 1
        ORDER BY p.data_publicacao DESC
        LIMIT 4
    ");
    $stmt_latest->execute([$post['id']]);
    $latest_posts = $stmt_latest->fetchAll(PDO::FETCH_ASSOC);

    // Processar tags para array
    $post['tags'] = [];
    if (!empty($post['tags_data'])) {
        $tags_array = explode(',', $post['tags_data']);
        foreach ($tags_array as $tag_data) {
            list($id, $nome, $tag_slug) = explode(':', $tag_data);
            $post['tags'][] = [
                'id' => $id,
                'nome' => $nome,
                'slug' => $tag_slug
            ];
        }
    }
    unset($post['tags_data']);

    $post['publicado'] = $post['publicado'] ?? 0;
    $post['editor_type'] = $post['editor_type'] ?? 'tinymce';

    function getUserIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    $visitor_ip = getUserIP();

    define('ADMIN_IP', '179.48.2.57');

    if ($visitor_ip !== ADMIN_IP) {
        $stmt_update = $pdo->prepare("UPDATE posts SET visualizacoes = visualizacoes + 1 WHERE id = ?");
        $stmt_update->execute([$post['id']]);
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
                        <?php echo htmlspecialchars($autor_nome); ?>
                    </a>
                </p>
            <?php else: ?>
                <p class="lead">por <?php echo htmlspecialchars($autor_nome); ?></p>
            <?php endif; ?>


            <hr>

            <div class="post-meta mb-3">
                <span class="me-3"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($post['data_publicacao'])); ?></span>
                <span class="me-3"><i class="far fa-folder"></i> <a href="<?php echo BLOG_URL; ?>/categoria/<?php echo htmlspecialchars($post['categoria_slug']); ?>"><?php echo htmlspecialchars($post['categoria_nome']); ?></a></span>
                <span><i class="far fa-eye"></i> <?php echo number_format($post['visualizacoes']); ?> visualizações</span>
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


            <div class="mb-4">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(BLOG_URL . '/post/' . $post['slug']); ?>"
                   target="_blank" class="btn btn-primary btn-sm me-1">
                    <i class="fab fa-facebook-f"></i> Compartilhar
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(BLOG_URL . '/post/' . $post['slug']); ?>&text=<?php echo urlencode($post['titulo']); ?>"
                   target="_blank" class="btn btn-info btn-sm me-1">
                    <i class="fab fa-twitter"></i> Tweetar
                </a>
                <a href="whatsapp://send?text=<?php echo urlencode($post['titulo'] . ' ' . BLOG_URL . '/post/' . $post['slug']); ?>"
                   data-action="share/whatsapp/share" target="_blank" class="btn btn-success btn-sm">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
            </div>


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