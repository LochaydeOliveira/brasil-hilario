<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Verifica se a categoria foi fornecida
if (!isset($_GET['slug'])) {
    header('Location: ' . BLOG_URL);
    exit;
}

$categoria_slug = $_GET['slug'];

// Busca informações da categoria
$categoria = get_categoria_by_slug($categoria_slug);

if (!$categoria) {
    header('Location: ' . BLOG_URL);
    exit;
}

// Configuração das meta tags para SEO
$og_title = "Categoria: " . $categoria['nome'] . " - " . BLOG_TITLE;
$meta_description = "Posts sobre " . $categoria['nome'] . " no " . BLOG_TITLE;
$og_url = BLOG_URL . "/categoria/" . $categoria_slug;
$og_type = "website";

// Paginação
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$posts_por_pagina = 9;
$offset = ($pagina_atual - 1) * $posts_por_pagina;

// Busca os posts da categoria
$posts = get_posts_by_categoria($categoria['id'], $posts_por_pagina, $offset);
$total_posts = get_total_posts_by_categoria($categoria['id']);
$total_paginas = ceil($total_posts / $posts_por_pagina);

// Inicia o buffer de saída
ob_start();
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-4"><?php echo htmlspecialchars($categoria['nome']); ?></h1>
            
            <?php if (!empty($categoria['descricao'])): ?>
                <p class="lead mb-5"><?php echo htmlspecialchars($categoria['descricao']); ?></p>
            <?php endif; ?>

            <?php if (empty($posts)): ?>
                <div class="alert alert-info">
                    Nenhum post encontrado nesta categoria.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($posts as $post): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <?php if (!empty($post['imagem_destaque'])): ?>
                                    <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>">
                                        <img src="<?php echo BLOG_URL; ?>/uploads/<?php echo $post['imagem_destaque']; ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo htmlspecialchars($post['titulo']); ?>">
                                    </a>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h2 class="card-title h5">
                                        <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>" class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($post['titulo']); ?>
                                        </a>
                                    </h2>
                                    <p class="card-text text-muted small">
                                        <i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($post['data_publicacao'])); ?>
                                    </p>
                                    <p class="card-text">
                                        <?php echo htmlspecialchars(substr(strip_tags($post['conteudo']), 0, 150)) . '...'; ?>
                                    </p>
                                </div>
                                <div class="card-footer bg-white border-0">
                                    <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>" class="btn btn-link p-0">
                                        Ler mais <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($total_paginas > 1): ?>
                    <nav aria-label="Navegação de páginas" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagina_atual > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?slug=<?php echo $categoria_slug; ?>&pagina=<?php echo $pagina_atual - 1; ?>">
                                        Anterior
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <li class="page-item <?php echo $i === $pagina_atual ? 'active' : ''; ?>">
                                    <a class="page-link" href="?slug=<?php echo $categoria_slug; ?>&pagina=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($pagina_atual < $total_paginas): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?slug=<?php echo $categoria_slug; ?>&pagina=<?php echo $pagina_atual + 1; ?>">
                                        Próxima
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <?php include 'includes/sidebar.php'; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/header.php';
echo $content;
include 'includes/footer.php';
?> 