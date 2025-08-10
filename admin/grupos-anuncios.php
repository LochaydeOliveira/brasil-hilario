<?php
ob_start();
session_start();

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/GruposAnunciosManager.php';
require_once '../includes/AnunciosManager.php';

$page_title = 'Grupos de Anúncios';

// Verificar login - usar a mesma verificação do header
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$gruposManager = new GruposAnunciosManager($pdo);
$anunciosManager = new AnunciosManager($pdo);

// Processar exclusão
if (isset($_POST['excluir_grupo'])) {
    $grupoId = (int)$_POST['grupo_id'];
    if ($gruposManager->excluirGrupo($grupoId)) {
        $mensagem = 'Grupo excluído com sucesso!';
        $tipo_mensagem = 'success';
    } else {
        $mensagem = 'Erro ao excluir grupo.';
        $tipo_mensagem = 'danger';
    }
}

// Buscar grupos
$grupos = $gruposManager->getAllGruposComStats();

// Buscar informações de posts específicos para cada grupo
foreach ($grupos as &$grupo) {
    if ($grupo['posts_especificos']) {
        $postsDoGrupo = $gruposManager->getPostsDoGrupo($grupo['id']);
        $grupo['posts_info'] = count($postsDoGrupo) . ' post(s) específico(s)';
        $grupo['posts_list'] = array_slice(array_column($postsDoGrupo, 'titulo'), 0, 3); // Primeiros 3 títulos
    } else {
        $grupo['posts_info'] = 'Todos os posts';
        $grupo['posts_list'] = [];
    }
}

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Grupos de Anúncios</h1>
    <a href="novo-grupo-anuncios.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Grupo
    </a>
</div>

    <?php if (isset($mensagem)): ?>
        <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
            <?php echo $mensagem; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (empty($grupos)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-ad fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum grupo de anúncios encontrado</h5>
                    <p class="text-muted">Crie seu primeiro grupo para começar a exibir anúncios organizados.</p>
                    <a href="novo-grupo-anuncios.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Criar Primeiro Grupo
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Localização</th>
                                <th>Layout</th>
                                <th>Marca</th>
                                <th>Anúncios</th>
                                <th>Posts</th>
                                <th>Status</th>
                                <th>Criado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grupos as $grupo): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($grupo['nome']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $grupo['localizacao'] === 'sidebar' ? 'info' : 'primary'; ?>">
                                            <?php echo ucfirst($grupo['localizacao']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $grupo['layout'] === 'carrossel' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($grupo['layout']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($grupo['marca'])): ?>
                                            <span class="badge bg-info">
                                                <?php echo ucfirst($grupo['marca']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Infoproduto</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo $grupo['total_anuncios']; ?> anúncio(s)
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($grupo['posts_especificos']): ?>
                                            <span class="badge bg-warning" title="<?php echo htmlspecialchars(implode(', ', $grupo['posts_list'])); ?>">
                                                <?php echo $grupo['posts_info']; ?>
                                            </span>
                                            <?php if (!$grupo['aparecer_inicio']): ?>
                                                <br><small class="text-muted">Não aparece na home</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-success">
                                                <?php echo $grupo['posts_info']; ?>
                                            </span>
                                            <?php if (!$grupo['aparecer_inicio']): ?>
                                                <br><small class="text-muted">Não aparece na home</small>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($grupo['ativo']): ?>
                                            <span class="badge bg-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y H:i', strtotime($grupo['criado_em'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="editar-grupo-anuncios.php?id=<?php echo $grupo['id']; ?>" 
                                               class="btn btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmarExclusao(<?php echo $grupo['id']; ?>, '<?php echo htmlspecialchars($grupo['nome']); ?>')" 
                                                    title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="modalConfirmacao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o grupo "<span id="nomeGrupo"></span>"?</p>
                <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="grupo_id" id="grupoId">
                    <button type="submit" name="excluir_grupo" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(grupoId, nomeGrupo) {
    document.getElementById('grupoId').value = grupoId;
    document.getElementById('nomeGrupo').textContent = nomeGrupo;
    new bootstrap.Modal(document.getElementById('modalConfirmacao')).show();
}
</script>

<?php include 'includes/footer.php'; ?> 
