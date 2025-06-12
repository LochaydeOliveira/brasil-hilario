<?php
/**
 * Arquivo: index.php
 * Descrição: Página inicial do painel administrativo
 * Funcionalidades:
 * - Exibe estatísticas gerais
 * - Lista posts recentes
 * - Lista comentários recentes
 * - Acesso rápido às principais funções
 */

// Define o título da página
$page_title = 'Painel de Controle';

// Inclui arquivos necessários
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Verifica se o usuário está autenticado
check_login();

// Conecta ao banco de dados
$conn = connectDB();

// Busca estatísticas
$stats = [
    'total_posts' => getTotalPosts($conn),
    'total_categorias' => getTotalCategories($conn),
    'total_comentarios' => getTotalComments($conn),
    'total_usuarios' => getTotalUsers($conn)
];

// Busca posts recentes
$posts_recentes = getRecentPosts($conn, 5);

// Busca comentários recentes
$comentarios_recentes = getRecentComments($conn, 5);

// Inclui o cabeçalho
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Painel de Controle</h1>
            </div>
            
            <?php showMessages(); ?>
            
            <!-- Cards de Estatísticas -->
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total de Posts</h5>
                            <p class="card-text display-4"><?php echo $stats['total_posts']; ?></p>
                            <a href="posts.php" class="text-white">Ver todos <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Categorias</h5>
                            <p class="card-text display-4"><?php echo $stats['total_categorias']; ?></p>
                            <a href="categorias.php" class="text-white">Ver todas <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Comentários</h5>
                            <p class="card-text display-4"><?php echo $stats['total_comentarios']; ?></p>
                            <a href="comentarios.php" class="text-white">Ver todos <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Usuários</h5>
                            <p class="card-text display-4"><?php echo $stats['total_usuarios']; ?></p>
                            <a href="usuarios.php" class="text-white">Ver todos <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Posts Recentes -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Posts Recentes</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <?php foreach ($posts_recentes as $post): ?>
                                <a href="editar-post.php?id=<?php echo $post['id']; ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($post['titulo']); ?></h6>
                                        <small><?php echo date('d/m/Y', strtotime($post['criado_em'])); ?></small>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars(substr($post['resumo'], 0, 100)) . '...'; ?>
                                    </small>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Comentários Recentes -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Comentários Recentes</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <?php foreach ($comentarios_recentes as $comentario): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($comentario['nome']); ?></h6>
                                        <small><?php echo date('d/m/Y H:i', strtotime($comentario['criado_em'])); ?></small>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars(substr($comentario['conteudo'], 0, 100)) . '...'; ?></p>
                                    <small class="text-muted">
                                        Em: <?php echo htmlspecialchars($comentario['post_titulo']); ?>
                                    </small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 