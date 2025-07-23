<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';

// Verificar se o usuário está logado
check_login();

// Buscar posts
try {
    $stmt = $pdo->prepare("SELECT p.*, c.nome as categoria_nome 
                         FROM posts p 
                         LEFT JOIN categorias c ON p.categoria_id = c.id 
                         ORDER BY p.criado_em DESC");
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Erro ao buscar posts: " . $e->getMessage());
}

include 'includes/header.php';
?>


<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Posts</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="novo-post.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Post
                    </a>
                </div>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Operação realizada com sucesso!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Categoria</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Visualizações</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo BLOG_URL . '/post/' . $post['slug']; ?>" target="_blank">
                                        <?php echo htmlspecialchars($post['titulo']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($post['categoria_nome'] ?? 'Sem categoria'); ?></td>
                                <td>
                                    <?php if ($post['publicado']): ?>
                                        <span class="badge bg-success">Publicado</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Rascunho</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($post['criado_em'])); ?></td>
                                <td><?php echo number_format($post['visualizacoes'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="editar-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="excluir-post.php?id=<?php echo $post['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Tem certeza que deseja excluir este post?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alertElement = document.querySelector('.alert.alert-success');
        if (alertElement) {
            setTimeout(() => {
                // Adiciona a classe 'show' e 'fade' para transição e depois remove a classe 'show'
                // para iniciar o fade out, se a mensagem não for dismissível manualmente
                if (alertElement.classList.contains('show')) {
                    alertElement.classList.remove('show');
                } else {
                    // Caso já esteja sem a classe 'show' por alguma razão, apenas remova
                    alertElement.remove();
                }
                // Remove o alerta do DOM após a transição
                alertElement.addEventListener('transitionend', () => alertElement.remove());
            }, 3000); // 3 segundos
        }
    });
</script> 