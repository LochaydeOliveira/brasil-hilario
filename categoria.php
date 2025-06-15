<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Verifica se foi passado um slug de categoria
$categoria_slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($categoria_slug)) {
    header('Location: ' . BLOG_URL);
    exit;
}

// Busca a categoria pelo slug
$stmt = $pdo->prepare("SELECT * FROM categorias WHERE slug = ?");
$stmt->execute([$categoria_slug]);
$categoria = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    header('Location: ' . BLOG_URL);
    exit;
}

// Busca os posts da categoria
$stmt = $pdo->prepare("
    SELECT p.*, u.nome as autor_nome 
    FROM posts p 
    INNER JOIN usuarios u ON p.autor_id = u.id 
    INNER JOIN posts_categorias pc ON p.id = pc.post_id 
    WHERE pc.categoria_id = ? AND p.status = 'publicado' 
    ORDER BY p.data_publicacao DESC
");
$stmt->execute([$categoria['id']]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Configuração das meta tags para SEO
$og_title = "Categoria: " . $categoria['nome'] . " - " . BLOG_TITLE;
$meta_description = "Posts sobre " . $categoria['nome'] . " no " . BLOG_TITLE;
$og_url = BLOG_URL . "/categoria/" . $categoria_slug;
$og_type = "website";

// Inicia o buffer de saída
ob_start();
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-4">Categoria: <?php echo htmlspecialchars($categoria['nome']); ?></h1>
            
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
                                    <img src="<?php echo BLOG_URL; ?>/uploads/<?php echo $post['imagem_destaque']; ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($post['titulo']); ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h2 class="card-title h5">
                                        <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>" 
                                           class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($post['titulo']); ?>
                                        </a>
                                    </h2>
                                    <p class="card-text text-muted small">
                                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($post['autor_nome']); ?> |
                                        <i class="fas fa-calendar"></i> <?php echo formatarData($post['data_publicacao']); ?>
                                    </p>
                                    <p class="card-text">
                                        <?php echo resumirTexto(strip_tags($post['conteudo']), 150); ?>
                                    </p>
                                    <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        Ler mais
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <?php include 'includes/sidebar.php'; ?>
        </div>
    </div>
</div>

<?php 
include 'includes/footer.php';
// Enviar o buffer de saída
ob_end_flush();
?> 