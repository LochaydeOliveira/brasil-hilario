<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../includes/db.php';
require_once '../includes/AnunciosManager.php';
require_once 'includes/auth.php';

$anunciosManager = new AnunciosManager($pdo);

// Buscar top anúncios
$topAnuncios = $anunciosManager->getTopAnuncios(5);

// Buscar todos os anúncios com stats
$todosAnuncios = $anunciosManager->getAllAnunciosComStats();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Anúncios Nativos</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="novo-anuncio.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Anúncio
                    </a>
                </div>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Operação realizada com sucesso!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <!-- Dashboard - Top 5 Anúncios -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Top 5 Anúncios Mais Clicados</h5>
                    <a href="todos-anuncios.php" class="btn btn-outline-primary btn-sm">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($topAnuncios)): ?>
                        <p class="text-muted text-center">Nenhum anúncio encontrado.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Localização</th>
                                        <th>Cliques</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topAnuncios as $anuncio): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo htmlspecialchars($anuncio['imagem']); ?>" 
                                                         alt="<?php echo htmlspecialchars($anuncio['titulo']); ?>"
                                                         class="anuncio-thumb me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($anuncio['titulo']); ?></strong>
                                                        <?php if ($anuncio['cta_ativo']): ?>
                                                            <br><small class="text-muted">CTA: <?php echo htmlspecialchars($anuncio['cta_texto']); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $anuncio['localizacao'] === 'sidebar' ? 'info' : 'warning'; ?>">
                                                    <?php echo ucfirst($anuncio['localizacao']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <?php echo number_format($anuncio['total_cliques'], 0, ',', '.'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($anuncio['ativo']): ?>
                                                    <span class="badge bg-success">Ativo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inativo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="editar-anuncio.php?id=<?php echo $anuncio['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="excluir-anuncio.php?id=<?php echo $anuncio['id']; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Tem certeza que deseja excluir este anúncio?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Estatísticas Gerais -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0"><?php echo count($todosAnuncios); ?></h4>
                                    <p class="mb-0">Total de Anúncios</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-ad fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">
                                        <?php 
                                        $ativos = array_filter($todosAnuncios, function($a) { return $a['ativo']; });
                                        echo count($ativos);
                                        ?>
                                    </h4>
                                    <p class="mb-0">Anúncios Ativos</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">
                                        <?php 
                                        $sidebar = array_filter($todosAnuncios, function($a) { return $a['localizacao'] === 'sidebar'; });
                                        echo count($sidebar);
                                        ?>
                                    </h4>
                                    <p class="mb-0">Sidebar</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-columns fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">
                                        <?php 
                                        $conteudo = array_filter($todosAnuncios, function($a) { return $a['localizacao'] === 'conteudo'; });
                                        echo count($conteudo);
                                        ?>
                                    </h4>
                                    <p class="mb-0">Conteúdo</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-newspaper fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Listagem Rápida -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Todos os Anúncios</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($todosAnuncios)): ?>
                        <p class="text-muted text-center">Nenhum anúncio cadastrado.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Localização</th>
                                        <th>Cliques</th>
                                        <th>Posts</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($todosAnuncios as $anuncio): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo htmlspecialchars($anuncio['imagem']); ?>" 
                                                         alt="<?php echo htmlspecialchars($anuncio['titulo']); ?>"
                                                         class="anuncio-thumb me-3" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($anuncio['titulo']); ?></strong>
                                                        <?php if ($anuncio['cta_ativo']): ?>
                                                            <br><small class="text-muted">CTA: <?php echo htmlspecialchars($anuncio['cta_texto']); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $anuncio['localizacao'] === 'sidebar' ? 'info' : 'warning'; ?>">
                                                    <?php echo ucfirst($anuncio['localizacao']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <?php echo number_format($anuncio['total_cliques'], 0, ',', '.'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?php echo $anuncio['total_posts']; ?> posts
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($anuncio['ativo']): ?>
                                                    <span class="badge bg-success">Ativo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inativo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="editar-anuncio.php?id=<?php echo $anuncio['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="excluir-anuncio.php?id=<?php echo $anuncio['id']; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Tem certeza que deseja excluir este anúncio?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 