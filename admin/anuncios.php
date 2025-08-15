<?php
require_once '../config/config.php';
require_once '../config/database_unified.php';
require_once 'includes/auth.php';

// Verificar se o usuário está logado
check_login();

$dbManager = DatabaseManager::getInstance();

// Processar exclusão
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    try {
        // Verificar se o produto está em algum grupo
        $em_grupo = $dbManager->queryOne("
            SELECT COUNT(*) as total FROM grupos_anuncios_items WHERE anuncio_id = ?
        ", [$id]);
        
        if ($em_grupo['total'] > 0) {
            $erro = "Não é possível excluir este produto pois ele está associado a um grupo. Remova a associação primeiro.";
        } else {
            $resultado = $dbManager->execute("DELETE FROM anuncios WHERE id = ?", [$id]);
            if ($resultado) {
                $sucesso = "Produto excluído com sucesso!";
            } else {
                $erro = "Erro ao excluir produto.";
            }
        }
    } catch (Exception $e) {
        $erro = "Erro: " . $e->getMessage();
    }
}

// Buscar produtos
$anuncios = $dbManager->query("
    SELECT a.*, 
           COUNT(gi.grupo_id) as total_grupos,
           GROUP_CONCAT(g.nome SEPARATOR ', ') as grupos_associados
    FROM anuncios a
    LEFT JOIN grupos_anuncios_items gi ON a.id = gi.anuncio_id
    LEFT JOIN grupos_anuncios g ON gi.grupo_id = g.id
    GROUP BY a.id
    ORDER BY a.criado_em DESC
");

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Catálogo de Produtos</h1>
                <a href="novo-anuncio.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Produto
                </a>
            </div>
            
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>
            
            <?php if (isset($sucesso)): ?>
                <div class="alert alert-success"><?php echo $sucesso; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Produtos Disponíveis</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($anuncios)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5>Nenhum produto encontrado</h5>
                            <p class="text-muted">Cadastre seu primeiro produto para começar.</p>
                            <a href="novo-anuncio.php" class="btn btn-primary">Cadastrar Primeiro Produto</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Produto</th>
                                        <th>Marca</th>
                                        <th>Status</th>
                                        <th>Grupos</th>
                                        <th>Criado em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($anuncios as $anuncio): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">#<?php echo $anuncio['id']; ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($anuncio['imagem'])): ?>
                                                        <img src="<?php echo htmlspecialchars($anuncio['imagem']); ?>" 
                                                             alt="<?php echo htmlspecialchars($anuncio['titulo']); ?>"
                                                             class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                    <?php endif; ?>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($anuncio['titulo']); ?></strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            <a href="<?php echo htmlspecialchars($anuncio['link_compra']); ?>" 
                                                               target="_blank" class="text-decoration-none">
                                                                <i class="fas fa-external-link-alt"></i> Ver produto
                                                            </a>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if (!empty($anuncio['marca'])): ?>
                                                    <?php if ($anuncio['marca'] === 'amazon'): ?>
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="fab fa-amazon"></i> Amazon
                                                        </span>
                                                    <?php elseif ($anuncio['marca'] === 'shopee'): ?>
                                                        <span class="badge bg-orange text-white">
                                                            <i class="fas fa-shopping-cart"></i> Shopee
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary"><?php echo ucfirst($anuncio['marca']); ?></span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($anuncio['ativo']): ?>
                                                    <span class="badge bg-success">Ativo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inativo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($anuncio['total_grupos'] > 0): ?>
                                                    <span class="badge bg-info"><?php echo $anuncio['total_grupos']; ?> grupo(s)</span>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($anuncio['grupos_associados']); ?></small>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Sem grupo</span>
                                                    <br>
                                                    <small class="text-muted">Não aparecerá no site</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('d/m/Y H:i', strtotime($anuncio['criado_em'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="editar-anuncio.php?id=<?php echo $anuncio['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="anuncios.php?delete=<?php echo $anuncio['id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Tem certeza que deseja excluir este produto?')"
                                                       title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
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
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Como Funciona o Sistema</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="fas fa-box fa-2x text-primary mb-2"></i>
                                <h6>1. Catálogo de Produtos</h6>
                                <p class="text-muted small">Cadastre produtos com informações básicas (nome, imagem, link, marca)</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="fas fa-layer-group fa-2x text-success mb-2"></i>
                                <h6>2. Grupos de Anúncios</h6>
                                <p class="text-muted small">Crie grupos e selecione produtos do catálogo para exibir</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="fas fa-eye fa-2x text-info mb-2"></i>
                                <h6>3. Exibição no Site</h6>
                                <p class="text-muted small">Configure onde e como os produtos aparecerão no site</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 
