<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php'; // Adicionando a conexão com o banco de dados

// Iniciar buffer de saída
ob_start();

// Verificar se a categoria foi especificada
if (!isset($_GET['slug'])) {
    header('Location: ' . BLOG_URL);
    exit;
}

$categoria_slug = $_GET['slug'];

// Buscar informações da categoria
$stmt = $conn->prepare("SELECT * FROM categorias WHERE slug = ?");
$stmt->bind_param("s", $categoria_slug);
$stmt->execute();
$result = $stmt->get_result();
$categoria = $result->fetch_assoc();

// Se a categoria não existir, redirecionar para a página inicial
if (!$categoria) {
    header('Location: ' . BLOG_URL);
    exit;
}

// Buscar posts da categoria
$stmt = $conn->prepare("
    SELECT p.*, u.nome as autor_nome, c.nome as categoria_nome 
    FROM posts p 
    LEFT JOIN usuarios u ON p.autor_id = u.id 
    LEFT JOIN categorias c ON p.categoria_id = c.id 
    WHERE p.categoria_id = ? AND p.publicado = 1 
    ORDER BY p.data_publicacao DESC
");
$stmt->bind_param("i", $categoria['id']);
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);

// Se não houver posts, redirecionar para a página inicial
if (empty($posts)) {
    header('Location: ' . BLOG_URL);
    exit;
}

// Definir meta tags para SEO
$og_title = "Categoria: " . $categoria['nome'] . " - " . BLOG_TITLE;
$meta_description = "Posts sobre " . $categoria['nome'] . " no " . BLOG_TITLE;
$og_url = BLOG_URL . "/categoria/" . $categoria_slug;
$og_type = "website";

// Incluir o cabeçalho
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <!-- Conteúdo Principal -->
        <div class="col-lg-8">
            <h1 class="mb-4">Categoria: <?php echo htmlspecialchars($categoria['nome']); ?></h1>
            
            <div class="row">
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <?php if ($post['imagem_destacada']): ?>
                                <img src="<?php echo BLOG_URL . '/uploads/images/' . $post['imagem_destacada']; ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($post['titulo']); ?>" loading="lazy">
                            <?php endif; ?>
                            
                            <div class="card-body no-pad">
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
                                        <?php echo date('d/m/Y', strtotime($post['data_publicacao'])); ?>
                                    </small>
                                    <a href="<?php echo BLOG_URL . '/post/' . $post['slug']; ?>" class="lead">
                                        Ler mais
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <?php include 'includes/sidebar.php'; ?>
        </div>
    </div>
</div>

<?php
// Incluir o rodapé
include 'includes/footer.php';

// Enviar o buffer de saída
ob_end_flush();
?> 