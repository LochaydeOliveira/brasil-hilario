<?php
// Iniciar buffer de saída
ob_start();

require_once 'includes/db.php';
require_once 'config/config.php';


// Incluir o header
include 'includes/header.php';
?>

<div class="row">
    <!-- Conteúdo Principal -->
    <div class="col-lg-8">
        <?php
        // Buscar posts
        $stmt = $pdo->query("
            SELECT p.*, c.nome as categoria_nome, c.slug as categoria_slug 
            FROM posts p 
            JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.publicado = 1 
            ORDER BY p.criado_em DESC 
            LIMIT " . POSTS_PER_PAGE
        );
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($posts as $post):
        ?>
        <article class="blog-post mb-4" data-aos="fade-up">
            <h2 class="display-6 fw-bold mb-3">
                <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>" class="text-decoration-none text-dark">
                    <?php echo htmlspecialchars($post['titulo']); ?>
                </a>
            </h2>
            
            <div class="post-meta mb-3">
                <span class="me-3"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($post['criado_em'])); ?></span>
                <span class="me-3"><i class="far fa-folder"></i> <a href="<?php echo BLOG_URL; ?>/categoria/<?php echo htmlspecialchars($post['categoria_slug']); ?>"><?php echo htmlspecialchars($post['categoria_nome']); ?></a></span>
                <span><i class="far fa-eye"></i> <?php echo number_format($post['visualizacoes']); ?> visualizações</span>
            </div>
            
            <?php if ($post['imagem_destacada']): ?>
            <img src="<?php echo BLOG_URL; ?>/uploads/<?php echo $post['imagem_destacada']; ?>" 
                 class="img-fluid rounded mb-3" 
                 alt="<?php echo htmlspecialchars($post['titulo']); ?>"
                 loading="lazy">
            <?php endif; ?>
            
            <div class="post-excerpt mb-3">
                <?php echo $post['resumo']; ?>
            </div>
            
            <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>" class="btn btn-primary">
                Ler mais
            </a>
        </article>
        <?php endforeach; ?>

        <!-- Paginação -->
        <nav aria-label="Navegação de posts" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Anterior</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Próximo</a>
                </li>
            </ul>
        </nav>
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
