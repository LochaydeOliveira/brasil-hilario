<?php
// Habilitar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
                SELECT p.*, c.nome as categoria_nome, c.slug as categoria_slug,
                       GROUP_CONCAT(DISTINCT t.id, ':', t.nome, ':', t.slug) as tags_data
                FROM posts p 
                JOIN categorias c ON p.categoria_id = c.id 
                LEFT JOIN post_tags pt ON p.id = pt.post_id
                LEFT JOIN tags t ON pt.tag_id = t.id
                WHERE p.publicado = 1
                GROUP BY p.id
                ORDER BY p.criado_em DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([POSTS_PER_PAGE, $offset]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Processar tags para cada post
            foreach ($posts as &$post) {
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
            }

            // Buscar o total de posts para paginação
            $stmt = $pdo->query("SELECT COUNT(*) FROM posts WHERE publicado = 1");
            $total_posts = $stmt->fetchColumn();
            $total_pages = ceil($total_posts / POSTS_PER_PAGE);

            if (empty($posts)) {
                echo '<div class="alert alert-info">Nenhum post encontrado.</div>';
            } else {
                foreach ($posts as $post):
                ?>
                <article class="blog-post mb-4" data-aos="fade-up">
                    <h2 class="display-6 fw-bold mb-3">
                        <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>" class="text-decoration-none text-dark">
                            <?php echo htmlspecialchars($post['titulo']); ?>
                        </a>
                    </h2>
                    
                    <div class="post-meta mb-2">
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($post['criado_em'])); ?>
                            <i class="fas fa-folder ms-2"></i> 
                            <a href="<?php echo BLOG_PATH; ?>/categoria/<?php echo htmlspecialchars($post['categoria_slug']); ?>" class="text-muted">
                                <?php echo htmlspecialchars($post['categoria_nome']); ?>
                            </a>
                        </small>
                    </div>
                    
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
                    
                    <?php if ($post['imagem_destacada']): ?>
                    <img src="<?php echo BLOG_URL; ?>/uploads/<?php echo $post['imagem_destacada']; ?>" 
                         class="img-fluid rounded mb-3" 
                         alt="<?php echo htmlspecialchars($post['titulo']); ?>"
                         loading="lazy">
                    <?php endif; ?>
                    
                    <div class="post-excerpt mb-3">
                        <?php echo $post['resumo']; ?>
                    </div>
                    
                    <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>" class="read-more">
                        Ler mais
                    </a>
                </article>
                <?php 
                endforeach;

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
