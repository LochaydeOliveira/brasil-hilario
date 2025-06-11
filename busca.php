<?php
require_once 'includes/config/config.php';
require_once 'includes/header.php';

// Verifica se existe um termo de busca
$search_term = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($search_term)) {
    header('Location: ' . BLOG_URL);
    exit;
}

// Função para buscar posts
function search_posts($term) {
    global $conn;
    
    $term = '%' . $term . '%';
    $sql = "SELECT p.*, c.name as category_name 
            FROM posts p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.status = 'published' 
            AND (p.title LIKE ? OR p.content LIKE ? OR p.excerpt LIKE ?)
            ORDER BY p.published_at DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $term, $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

$posts = search_posts($search_term);
?>

<div class="container py-4">
    <h1 class="mb-4">Resultados da busca para: "<?php echo htmlspecialchars($search_term); ?>"</h1>
    
    <?php if (empty($posts)): ?>
        <div class="alert alert-info">
            Nenhum resultado encontrado para sua busca. Tente outros termos.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($posts as $post): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($post['featured_image'])): ?>
                            <img src="<?php echo BLOG_URL . '/uploads/' . $post['featured_image']; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($post['title']); ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="<?php echo BLOG_URL . '/post/' . $post['slug']; ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h5>
                            <p class="card-text"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <?php echo date('d/m/Y', strtotime($post['published_at'])); ?>
                                </small>
                                <span class="badge bg-primary"><?php echo htmlspecialchars($post['category_name']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?> 