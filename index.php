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


$categories = [];
try {
    $stmt = $conn->prepare("SELECT id, nome, slug FROM categorias ORDER BY nome ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    $categories = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {

    error_log("Erro ao carregar categorias para a barra de navegação: " . $e->getMessage());
}
?>



<div class="row">

    <div class="col-lg-8">
        <?php
        try {

            $limit = POSTS_PER_PAGE;
            $stmt = $conn->prepare("
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
                LIMIT ? OFFSET ?
            ");
            $stmt->bind_param("ii", $limit, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            $posts = $result->fetch_all(MYSQLI_ASSOC);


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


            $stmt = $conn->prepare("SELECT COUNT(id) as total FROM posts WHERE publicado = 1");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $total_posts = $row['total'];
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
                                         class="img-fluid" 
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
                        
                        <div class="post-excerpt mb-3">
                            <?php echo $post['resumo']; ?>
                        </div>
                        
                        <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>" class="lead">
                            Ler mais
                        </a>
                    </article>
                <?php endforeach;


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
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erro ao carregar posts: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>


    <div class="col-lg-4">
        <?php include 'includes/sidebar.php'; ?>
    </div>

</div>

<?php 
include 'includes/footer.php';
ob_end_flush();
?>
