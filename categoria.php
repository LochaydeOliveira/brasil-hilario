<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

ob_start();

// 1. Pega o slug da URL
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header('Location: ' . BLOG_URL);
    exit;
}

$categoria_slug = $_GET['slug'];

// 2. Busca a categoria no banco
$stmt = $conn->prepare("SELECT * FROM categorias WHERE slug = ?");
$stmt->bind_param("s", $categoria_slug);
$stmt->execute();
$result = $stmt->get_result();
$categoria = $result->fetch_assoc();

if (!$categoria) {
    header('Location: ' . BLOG_URL);
    exit;
}

// 3. Busca os posts publicados da categoria
$stmt = $conn->prepare("
    SELECT p.*, u.nome as autor_nome, c.nome as categoria_nome, c.slug as categoria_slug
    FROM posts p
    LEFT JOIN usuarios u ON p.autor_id = u.id
    LEFT JOIN categorias c ON p.categoria_id = c.id
    WHERE p.categoria_id = ? AND p.publicado = 1
    ORDER BY COALESCE(p.data_publicacao, p.criado_em) DESC
");
$stmt->bind_param("i", $categoria['id']);
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);

// 4. Depuração: exibe os posts encontrados
// REMOVA isso após o teste
// echo '<pre>'; var_dump($posts); echo '</pre>'; exit;

$og_title = "Categoria: " . $categoria['nome'] . " - " . BLOG_TITLE;
$meta_description = "Posts sobre " . $categoria['nome'] . " no " . BLOG_TITLE;
$og_url = BLOG_URL . "/categoria/" . $categoria_slug;
$og_type = "website";

include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-4">Categoria: <?php echo htmlspecialchars($categoria['nome']); ?></h1>

            <?php if (empty($posts)): ?>
                <div class="alert alert-warning">
                    Nenhum post foi publicado ainda nesta categoria.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($posts as $post): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <?php if ($post['imagem_destacada']): ?>
                                    <img src="<?php echo BLOG_URL . '/uploads/images/' . $post['imagem_destacada']; ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($post['titulo']); ?>">
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="<?php echo BLOG_URL . '/post/' . $post['slug']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($post['titulo']); ?>
                                        </a>
                                    </h5>
                                    
                                    <p class="card-text">
                                        <?php echo htmlspecialchars(substr($post['resumo'], 0, 150)) . '...'; ?>
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?php
                                            $data = $post['data_publicacao'] ?? $post['criado_em'];
                                            echo date('d/m/Y', strtotime($data));
                                            ?>
                                        </small>
                                        <a href="<?php echo BLOG_URL . '/post/' . $post['slug']; ?>" class="lead">Ler mais</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <?php include 'includes/sidebar.php'; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ob_end_flush(); ?>
