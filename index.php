<?php

ob_start();

require_once 'includes/db.php';  // Deve definir $pdo
require_once 'config/config.php';

// Incluir novas classes
require_once 'includes/Logger.php';
require_once 'includes/CacheManager.php';
require_once 'includes/Validator.php';

// Inicializar sessão de forma segura
require_once 'includes/session_init.php';

// Inicializar classes
$logger = new Logger();
$cache = new CacheManager();
$validator = new Validator();

$request_uri = strtok($_SERVER["REQUEST_URI"], '?');
preg_match('/\/(\d+)$/', $request_uri, $matches);
$page_from_url = !empty($matches[1]) ? (int)$matches[1] : 0;
$page_from_get = isset($_GET['page']) ? (int)($_GET['page'][0] ?? $_GET['page']) : 0;

$page = max(1, $page_from_url, $page_from_get);
$offset = ($page - 1) * POSTS_PER_PAGE;

// Log da página acessada
$logger->info('Página inicial acessada', [
    'page' => $page,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
]);

include 'includes/header.php';

?>

<div class="row">

    <div class="col-lg-8">

        <?php
        try {
            $limit = POSTS_PER_PAGE;

            // Usar cache para posts
            $posts = $cache->cachePosts($page, $limit, function() use ($pdo, $limit, $offset, $logger) {
                // Consulta principal
                $sql = "
                    SELECT p.*, c.nome as categoria_nome, c.slug as categoria_slug, t_grouped.tags_data
                    FROM posts p 
                    JOIN categorias c ON p.categoria_id = c.id 
                    LEFT JOIN (
                        SELECT pt.post_id, GROUP_CONCAT(DISTINCT CONCAT(t.id, ':', t.nome, ':', t.slug) ORDER BY t.nome ASC SEPARATOR ',') as tags_data
                        FROM post_tags pt
                        JOIN tags t ON pt.tag_id = t.id
                        GROUP BY pt.post_id
                    ) as t_grouped ON p.id = t_grouped.post_id
                    WHERE p.publicado = 1
                    ORDER BY p.data_publicacao DESC 
                    LIMIT :limit OFFSET :offset
                ";

                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                
                $startTime = microtime(true);
                $stmt->execute();
                $executionTime = microtime(true) - $startTime;
                
                // Log da consulta
                $logger->logDatabaseQuery($sql, ['limit' => $limit, 'offset' => $offset], $executionTime);
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            });

            // Tratamento das tags
            foreach ($posts as $key => $post_item) {
                $posts[$key]['tags'] = [];
                if (!empty($post_item['tags_data'])) {
                    $tags_array = explode(',', $post_item['tags_data']);
                    foreach ($tags_array as $tag_data) {
                        list($id, $nome, $tag_slug) = explode(':', $tag_data);
                        $posts[$key]['tags'][] = [
                            'id' => $id,
                            'nome' => $nome,
                            'slug' => $tag_slug
                        ];
                    }
                }
                unset($posts[$key]['tags_data']);
            }

            // Contar total de posts para paginação (usar cache)
            $total_posts = $cache->get('total_posts_count');
            if ($total_posts === null) {
                $count_stmt = $pdo->prepare("SELECT COUNT(id) as total FROM posts WHERE publicado = 1");
                $count_stmt->execute();
                $total_posts = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
                $cache->set('total_posts_count', $total_posts, 3600); // Cache por 1 hora
            }
            
            $total_pages = ceil($total_posts / POSTS_PER_PAGE);

            if (empty($posts)) {
                echo '<div class="alert alert-info">Nenhum post encontrado.</div>';
            } else {
                $post_count = 0;
                foreach ($posts as $post): 
                    $post_count++;
                ?>
                    <article class="blog-post mb-4" data-aos="fade-up">
                        <?php if (!empty($post['imagem_destacada'])): ?>
                            <div class="post-image mb-3">
                                <a href="<?php echo BLOG_URL; ?>/post/<?php echo htmlspecialchars($post['slug']); ?>">
                                    <img src="<?php echo BLOG_URL; ?>/uploads/images/<?php echo htmlspecialchars($post['imagem_destacada']); ?>" 
                                         class="img-fluid" 
                                         alt="<?php echo htmlspecialchars($post['titulo']); ?>"
                                         loading="lazy">
                                </a>
                            </div>
                        <?php endif; ?>

                        <h2 class="display-6 fw-bold mb-3">
                            <a href="<?php echo BLOG_URL; ?>/post/<?php echo htmlspecialchars($post['slug']); ?>" class="text-decoration-none text-dark">
                                <?php echo htmlspecialchars($post['titulo']); ?>
                            </a>
                        </h2>
                        
                        <div class="post-meta mb-2">
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i> 
                                <?php echo date('d/m/Y', strtotime($post['data_publicacao'])); ?>
                                
                                <?php if (!empty($post['categoria_nome'])): ?>
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-folder"></i>
                                    <a href="<?php echo BLOG_URL; ?>/categoria/<?php echo htmlspecialchars($post['categoria_slug']); ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($post['categoria_nome']); ?>
                                    </a>
                                <?php endif; ?>
                            </small>
                        </div>

                        <div class="post-excerpt mb-3">
                            <?php 
                            $excerpt = strip_tags($post['conteudo']);
                            $excerpt = substr($excerpt, 0, EXCERPT_LENGTH);
                            if (strlen($post['conteudo']) > EXCERPT_LENGTH) {
                                $excerpt .= '...';
                            }
                            echo htmlspecialchars($excerpt);
                            ?>
                        </div>

                        <?php if (!empty($post['tags'])): ?>
                            <div class="post-tags mb-3">
                                <?php foreach ($post['tags'] as $tag): ?>
                                    <a href="<?php echo BLOG_URL; ?>/tag/<?php echo htmlspecialchars($tag['slug']); ?>" 
                                       class="badge bg-secondary text-decoration-none me-1">
                                        #<?php echo htmlspecialchars($tag['nome']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <a href="<?php echo BLOG_URL; ?>/post/<?php echo htmlspecialchars($post['slug']); ?>" 
                           class="btn btn-outline-primary">
                            Ler mais <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </article>
                <?php endforeach; ?>

                <!-- Paginação -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Navegação de páginas">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo BLOG_URL; ?>/<?php echo ($page - 1); ?>">
                                        <i class="fas fa-chevron-left"></i> Anterior
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);

                            for ($i = $start_page; $i <= $end_page; $i++):
                            ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo BLOG_URL; ?>/<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo BLOG_URL; ?>/<?php echo ($page + 1); ?>">
                                        Próxima <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php } ?>

        <?php } catch (Exception $e) {
            $logger->error('Erro ao carregar posts', [
                'error' => $e->getMessage(),
                'page' => $page,
                'trace' => $e->getTraceAsString()
            ]);
            echo '<div class="alert alert-danger">Erro ao carregar posts. Tente novamente mais tarde.</div>';
        } ?>

    </div>

    <div class="col-lg-4">
        <?php include 'includes/sidebar.php'; ?>
    </div>

</div>

<?php
include 'includes/footer.php';
ob_end_flush();
?>
