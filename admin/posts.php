<?php
/**
 * Arquivo: posts.php
 * Descrição: Gerenciamento de posts do painel administrativo
 * Funcionalidades:
 * - Lista todos os posts
 * - Permite filtrar por status e categoria
 * - Permite buscar posts
 * - Permite excluir posts
 * - Controle de acesso baseado em permissões
 */

// Define o título da página
$page_title = 'Posts';

// Inclui arquivos necessários
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Verifica se o usuário está autenticado
check_login();

// Conecta ao banco de dados
$conn = connectDB();

// Processa a exclusão de post
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (deletePost($conn, $_GET['delete'])) {
        setSuccess('Post excluído com sucesso!');
    } else {
        setError('Erro ao excluir post.');
    }
    redirect('posts.php');
}

// Define filtros
$filtros = [
    'status' => $_GET['status'] ?? 'todos',
    'categoria' => $_GET['categoria'] ?? 'todas',
    'busca' => $_GET['busca'] ?? ''
];

// Busca todas as categorias para o filtro
$categorias = getAllCategories($conn);

// Busca os posts com os filtros aplicados
$posts = getFilteredPosts($conn, $filtros);

// Inclui o cabeçalho
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gerenciar Posts</h1>
                <a href="novo-post.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Post
                </a>
            </div>
            
            <?php showMessages(); ?>
            
            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="todos" <?php echo $filtros['status'] === 'todos' ? 'selected' : ''; ?>>Todos</option>
                                <option value="publicado" <?php echo $filtros['status'] === 'publicado' ? 'selected' : ''; ?>>Publicado</option>
                                <option value="rascunho" <?php echo $filtros['status'] === 'rascunho' ? 'selected' : ''; ?>>Rascunho</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="categoria" class="form-label">Categoria</label>
                            <select class="form-select" id="categoria" name="categoria">
                                <option value="todas" <?php echo $filtros['categoria'] === 'todas' ? 'selected' : ''; ?>>Todas</option>
                                <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria['id']; ?>" <?php echo $filtros['categoria'] == $categoria['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($categoria['nome']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="busca" class="form-label">Buscar</label>
                            <input type="text" class="form-control" id="busca" name="busca" value="<?php echo htmlspecialchars($filtros['busca']); ?>" placeholder="Buscar por título...">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Lista de Posts -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Categoria</th>
                                    <th>Autor</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($posts)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum post encontrado.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($post['titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($post['categoria_nome']); ?></td>
                                    <td><?php echo htmlspecialchars($post['autor_nome']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($post['criado_em'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $post['status'] === 'publicado' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($post['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="editar-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="posts.php?delete=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este post?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 