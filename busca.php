<?php
require_once 'config/config.php';
require_once 'config/search.php';
require_once 'includes/header.php';

// Verifica se existe um termo de busca
$search_term = isset($_GET['q']) ? clean_search_term($_GET['q']) : '';

if (empty($search_term)) {
    header('Location: ' . BLOG_URL);
    exit;
}

// Função para buscar posts
function search_posts($term) {
    global $conn;
    
    try {
        $term = '%' . $term . '%';
        $sql = "SELECT p.*, c.nome as categoria_nome, u.nome as autor_nome 
                FROM posts p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                LEFT JOIN usuarios u ON p.autor_id = u.id 
                WHERE p.publicado = 1 
                AND (p.titulo LIKE ? OR p.conteudo LIKE ? OR p.resumo LIKE ?)
                ORDER BY p.data_publicacao DESC";
                
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro na preparação da consulta: " . $conn->error);
        }
        
        $stmt->bind_param('sss', $term, $term, $term);
        if (!$stmt->execute()) {
            throw new Exception("Erro na execução da consulta: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Erro na busca: " . $e->getMessage());
        return [];
    }
}

$posts = search_posts($search_term);
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-3">Resultados da busca para: "<?php echo htmlspecialchars($search_term); ?>"</h1>
            <p class="text-muted"><?php echo count($posts); ?> resultado(s) encontrado(s)</p>
        </div>
    </div>
    
    <?php if (empty($posts)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Nenhum resultado encontrado para sua busca. Tente outros termos.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($posts as $post): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($post['imagem_destacada'])): ?>
                            <img src="<?php echo BLOG_URL . '/uploads/' . $post['imagem_destacada']; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($post['titulo']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="<?php echo BLOG_URL . '/post/' . $post['slug']; ?>" class="text-decoration-none text-dark">
                                    <?php echo highlight_search_term(htmlspecialchars($post['titulo']), $search_term); ?>
                                </a>
                            </h5>
                            <p class="card-text">
                                <?php 
                                $excerpt = !empty($post['resumo']) ? $post['resumo'] : generate_excerpt($post['conteudo']);
                                echo highlight_search_term(htmlspecialchars($excerpt), $search_term); 
                                ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    <?php echo date('d/m/Y', strtotime($post['data_publicacao'])); ?>
                                </small>
                                <span class="badge bg-primary">
                                    <i class="fas fa-folder me-1"></i>
                                    <?php echo htmlspecialchars($post['categoria_nome']); ?>
                                </span>
                            </div>
                            <?php if (!empty($post['autor_nome'])): ?>
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-user me-1"></i>
                                    Por: <?php echo htmlspecialchars($post['autor_nome']); ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?> 