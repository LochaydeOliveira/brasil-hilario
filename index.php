<?php
// Iniciar buffer de saída
ob_start();

require_once 'includes/db.php';
require_once 'config/config.php';

// Definir o offset para paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * POSTS_PER_PAGE;

// Incluir o header
include 'includes/header.php';
?>

<div class="row">
    <!-- Conteúdo Principal -->
    <div class="col-lg-8">
        <?php
        try {
            // Buscar posts paginados
            $stmt = $pdo->prepare("
                SELECT p.*, u.nome as autor_nome, 
                       GROUP_CONCAT(DISTINCT CONCAT(c.id, ':', c.nome, ':', c.slug) SEPARATOR ',') as categorias_data,
                       t_grouped.tags_data
                FROM posts p 
                LEFT JOIN usuarios u ON p.autor_id = u.id
                LEFT JOIN posts_categorias pc ON p.id = pc.post_id
                LEFT JOIN categorias c ON pc.categoria_id = c.id
                LEFT JOIN (
                    SELECT pt.post_id, GROUP_CONCAT(DISTINCT CONCAT(t.id, ':', t.nome, ':', t.slug) ORDER BY t.nome ASC SEPARATOR ',') as tags_data
                    FROM post_tags pt
                    JOIN tags t ON pt.tag_id = t.id
                    GROUP BY pt.post_id
                ) as t_grouped ON p.id = t_grouped.post_id
                WHERE p.publicado = 1
                GROUP BY p.id
                ORDER BY p.data_publicacao DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([POSTS_PER_PAGE, $offset]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Processar categorias e tags para cada post
            foreach ($posts as $key => $post_item) {
                // Processar categorias
                $posts[$key]['categorias'] = [];
                if (!empty($post_item['categorias_data'])) {
                    $categorias_array = explode(',', $post_item['categorias_data']);
                    foreach ($categorias_array as $cat_data) {
                        list($id, $nome, $slug) = explode(':', $cat_data);
                        $posts[$key]['categorias'][] = [
                            'id' => $id,
                            'nome' => $nome,
                            'slug' => $slug
                        ];
                    }
                }
                unset($posts[$key]['categorias_data']);

                // Processar tags
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

            // Buscar o total de posts para paginação
            $stmt = $pdo->query("SELECT COUNT(id) FROM posts WHERE publicado = 1");
            $total_posts = $stmt->fetchColumn();
            $total_pages = ceil($total_posts / POSTS_PER_PAGE);

            if (empty($posts)) {
                echo '<div class="alert alert-info">Nenhum post encontrado.</div>';
            } else {
                foreach ($posts as $post): ?>
                    <article class="blog-post mb-4" data-aos="fade-up">
                        <?php if (!empty($post['imagem_destacada'])): ?>
                            <div class="post-image mb-3">
                                <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>">
                                    <img src="<?php echo BLOG_URL; ?>/uploads/images/<?php echo htmlspecialchars($post['imagem_destacada']); ?>" 
                                         class="img-fluid rounded" 
                                         alt="<?php echo htmlspecialchars($post['titulo']); ?>"
                                         style="width: 100%; height: 300px; object-fit: cover;">
                                </a>
                            </div>
                        <?php endif; ?>

                        <h2 class="display-6 fw-bold mb-3">
                            <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>" class="text-decoration-none text-dark">
                                <?php echo htmlspecialchars($post['titulo']); ?>
                            </a>
                        </h2>
                        
                        <div class="post-meta mb-2">
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($post['data_publicacao'])); ?>
                                <i class="fas fa-user ms-2"></i> <?php echo htmlspecialchars($post['autor_nome']); ?>
                            </small>
                        </div>
                        
                        <?php if (!empty($post['categorias'])): ?>
                            <div class="post-categories mb-2">
                                <?php foreach ($post['categorias'] as $categoria): ?>
                                    <a href="<?php echo BLOG_URL; ?>/categoria/<?php echo htmlspecialchars($categoria['slug']); ?>" 
                                       class="badge bg-primary text-white me-1">
                                        <i class="fas fa-folder"></i> <?php echo htmlspecialchars($categoria['nome']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($post['tags'])): ?>
                            <div class="post-tags mb-3">
                                <?php foreach ($post['tags'] as $tag): ?>
                                    <a href="<?php echo BLOG_PATH; ?>/tag/<?php echo htmlspecialchars($tag['slug']); ?>" 
                                       class="badge bg-info text-dark me-1">
                                        <i class="fas fa-tag"></i> <?php echo htmlspecialchars($tag['nome']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-excerpt mb-3">
                            <?php echo $post['resumo']; ?>
                        </div>
                        
                        <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>" class="lead">
                            Ler mais
                        </a>
                    </article>
                <?php endforeach;

                // Paginação
                if ($total_pages > 1):
                ?>
                <nav aria-label="Navegação de posts" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">Anterior</a>
                        </li>
                        <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Anterior</a>
                        </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Próximo</a>
                        </li>
                        <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Próximo</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php
            }
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger">Erro ao carregar posts: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <?php include 'includes/sidebar.php'; ?>
    </div>

</div>

<?php 
include 'includes/footer.php';
// Enviar o buffer de saída
ob_end_flush();
?>

