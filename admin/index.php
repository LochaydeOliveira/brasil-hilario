<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';

// Verificar se o usuário está logado
check_auth();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>
            
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Posts</h5>
                            <?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM posts");
                            $total_posts = $stmt->fetchColumn();
                            ?>
                            <p class="card-text display-4"><?php echo $total_posts; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Categorias</h5>
                            <?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM categorias");
                            $total_categorias = $stmt->fetchColumn();
                            ?>
                            <p class="card-text display-4"><?php echo $total_categorias; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">Tags</h5>
                            <?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM tags");
                            $total_tags = $stmt->fetchColumn();
                            ?>
                            <p class="card-text display-4"><?php echo $total_tags; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Usuários</h5>
                            <?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
                            $total_usuarios = $stmt->fetchColumn();
                            ?>
                            <p class="card-text display-4"><?php echo $total_usuarios; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Posts Recentes</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <?php
                                $stmt = $pdo->query("SELECT p.*, c.nome as categoria_nome 
                                                   FROM posts p 
                                                   LEFT JOIN categorias c ON p.categoria_id = c.id 
                                                   ORDER BY p.id DESC LIMIT 5");
                                while ($post = $stmt->fetch()) {
                                    echo '<a href="editar-post.php?id=' . $post['id'] . '" class="list-group-item list-group-item-action">';
                                    echo '<div class="d-flex w-100 justify-content-between">';
                                    echo '<h6 class="mb-1">' . htmlspecialchars($post['titulo']) . '</h6>';
                                    $data = !empty($post['data_criacao']) ? date('d/m/Y', strtotime($post['data_criacao'])) : 'Sem data';
                                    echo '<small>' . $data . '</small>';
                                    echo '</div>';
                                    echo '<small class="text-muted">Categoria: ' . htmlspecialchars($post['categoria_nome'] ?? 'Sem categoria') . '</small>';
                                    echo '</a>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 