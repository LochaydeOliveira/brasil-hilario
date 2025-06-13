<?php
error_reporting(E_ALL); // Forçar a exibição de todos os erros
ini_set('display_errors', 1); // Forçar a exibição de erros no navegador

// Iniciar buffer de saída
ob_start();
session_start();
require_once 'config/config.php';
require_once 'includes/db.php';
require_once 'vendor/autoload.php'; // Usar o autoload do Composer

// Obter o slug do post da URL
$post_slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_URL);

if (empty($post_slug)) {
    header('Location: ' . BLOG_URL);
    exit;
}

try {
    // Buscar o post pelo slug
    $stmt = $pdo->prepare("
        SELECT p.*, c.nome as categoria_nome, c.slug as categoria_slug,
               GROUP_CONCAT(DISTINCT t.id, ':', t.nome, ':', t.slug) as tags_data
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
        header('Location: ' . BLOG_URL . '/404.php'); // Redireciona para uma página 404 se o post não for encontrado
        exit;
    }

    // Processar tags
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

    // Garante que as chaves 'publicado' e 'editor_type' existam com valores padrão
    $post['publicado'] = $post['publicado'] ?? 0;
    $post['editor_type'] = $post['editor_type'] ?? 'tinymce';

    // Incrementar visualizações
    $stmt = $pdo->prepare("UPDATE posts SET visualizacoes = visualizacoes + 1 WHERE id = ?");
    $stmt->execute([$post['id']]);

} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}

// Incluir o header
include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BLOG_URL; ?>">Home</a></li>
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
                por <a href="#">Admin</a>
                <span class="badge bg-secondary ms-2">
                    <?php echo htmlspecialchars($post['categoria_nome'] ?? 'Sem Categoria'); ?>
                </span>
            </p>

            <hr>

            <div class="post-meta mb-3">
                <span class="me-3"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($post['criado_em'])); ?></span>
                <span class="me-3"><i class="far fa-folder"></i> <a href="<?php echo BLOG_URL; ?>/categoria/<?php echo htmlspecialchars($post['categoria_slug']); ?>"><?php echo htmlspecialchars($post['categoria_nome']); ?></a></span>
                <span><i class="far fa-eye"></i> <?php echo number_format($post['visualizacoes']); ?> visualizações</span>
            </div>

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

            <?php if (!empty($post['imagem_destacada'])): ?>
                <img class="img-fluid rounded mb-4" src="<?php echo UPLOAD_URL . '/' . htmlspecialchars($post['imagem_destacada']); ?>" 
                     alt="<?php echo htmlspecialchars($post['titulo']); ?>">
                <hr>
            <?php endif; ?>

            <div class="post-content">
                <?php 
                if ($post['editor_type'] === 'markdown') {
                    // Conteúdo Markdown
                    echo '<div class="markdown-content">' . (new Parsedown())->text($post['conteudo']) . '</div>';
                } else {
                    // Conteúdo TinyMCE (HTML)
                    echo $post['conteudo'];
                }
                ?>
            </div>

            <hr>

            <!-- Tags -->
            <?php if (!empty($post['tags'])): ?>
                <div class="mb-3">
                    <?php foreach ($post['tags'] as $tag): ?>
                        <a href="<?php echo BLOG_PATH; ?>/tag/<?php echo htmlspecialchars($tag['slug']); ?>" 
                           class="badge bg-info text-dark me-1">
                            <?php echo htmlspecialchars($tag['nome']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Social Share Buttons -->
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

            <!-- Comments Section (Facebook Comments Plugin) -->
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