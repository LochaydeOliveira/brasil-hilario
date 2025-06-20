<?php
    error_reporting(E_ALL); 
    ini_set('display_errors', 1); 


    ob_start();
    session_start();
    require_once 'config/config.php';
    require_once 'includes/db.php';
    require_once 'config/search.php';
    require_once 'vendor/autoload.php';


    $post_slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_URL);

    if (empty($post_slug)) {
        header('Location: ' . BLOG_URL);
        exit;
    }

    try {

        $stmt = $conn->prepare("
            SELECT p.*, c.nome as categoria_nome, c.slug as categoria_slug,
                GROUP_CONCAT(DISTINCT t.id, ':', t.nome, ':', t.slug) as tags_data
            FROM posts p 
            JOIN categorias c ON p.categoria_id = c.id 
            LEFT JOIN post_tags pt ON p.id = pt.post_id
            LEFT JOIN tags t ON pt.tag_id = t.id
            WHERE p.slug = ? AND p.publicado = 1
            GROUP BY p.id
        ");
        $stmt->bind_param("s", $post_slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $post = $result->fetch_assoc();

        if (!$post) {
            header('Location: ' . BLOG_URL . '/404.php');
        }


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
                // Pode conter múltiplos IPs, pega o primeiro
                return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            } else {
                return $_SERVER['REMOTE_ADDR'];
            }
        }
        $visitor_ip = getUserIP();

        define('ADMIN_IP', '179.48.2.57'); 


        if ($visitor_ip !== ADMIN_IP) {
            $stmt = $conn->prepare("UPDATE posts SET visualizacoes = visualizacoes + 1 WHERE id = ?");
            $stmt->bind_param("i", $post['id']);
            $stmt->execute();
        }


        $og_title = htmlspecialchars($post['titulo']);
        $og_description = !empty($post['resumo']) ? htmlspecialchars($post['resumo']) : htmlspecialchars(generate_excerpt($post['conteudo'], 200));
        $og_url = BLOG_URL . '/post/' . htmlspecialchars($post['slug']);
        $og_image = !empty($post['imagem_destacada']) ? BLOG_URL . '/uploads/images/' . htmlspecialchars($post['imagem_destacada']) : BLOG_URL . '/assets/img/logo-brasil-hilario-quadrada-svg.svg';
        $meta_description = $og_description;
        $meta_keywords = implode(', ', array_column($post['tags'], 'nome')) . ', ' . META_KEYWORDS;

    } catch (Exception $e) {
        die("Erro: " . $e->getMessage());
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

            <p class="lead">
                por <a href="#">Hilário Brasileiro</a>
            </p>

            <hr>

            <div class="post-meta mb-3">
                <span class="me-3"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($post['data_publicacao'])); ?></span>
                <span class="me-3"><i class="far fa-folder"></i> <a href="<?php echo BLOG_URL; ?>/categoria/<?php echo htmlspecialchars($post['categoria_slug']); ?>"><?php echo htmlspecialchars($post['categoria_nome']); ?></a></span>
                <span><i class="far fa-eye"></i> <?php echo number_format($post['visualizacoes']); ?> visualizações</span>
            </div>

            <div class="post-content">
                <?php 
                if ($post['editor_type'] === 'markdown') {
                    
                    echo '<div class="markdown-content">' . (new Parsedown())->text($post['conteudo']) . '</div>';
                } else {
                    
                    echo $post['conteudo'];
                }
                ?>
            </div>

            <hr>


            <?php if (!empty($post['tags'])): ?>
            <div class="post-tags mb-3">
                <i class="fas fa-tags"></i>
                <?php foreach ($post['tags'] as $tag): ?>
                <a href="<?php echo BLOG_URL; ?>/tag/<?php echo htmlspecialchars($tag['slug']); ?>" class="badge bg-secondary text-decoration-none me-1">
                    <?php echo htmlspecialchars($tag['nome']); ?>
                </a>
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

        <?php include 'includes/sidebar.php'; ?>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php ob_end_flush(); ?> 