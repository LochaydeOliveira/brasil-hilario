<?php
ob_start();
session_start();

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/GruposAnunciosManager.php';
require_once '../includes/AnunciosManager.php';

$page_title = 'Novo Grupo de Anúncios';

// Verificar login - usar a mesma verificação do header
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$gruposManager = new GruposAnunciosManager($pdo);
$anunciosManager = new AnunciosManager($pdo);

// Buscar todos os anúncios para seleção
$todosAnuncios = $anunciosManager->getAllAnunciosComStats();

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $localizacao = $_POST['localizacao'];
    $layout = $_POST['layout'] ?? 'carrossel';
    $anuncios = $_POST['anuncios'] ?? [];
    
    if (empty($nome)) {
        $erro = 'Nome do grupo é obrigatório.';
    } elseif (empty($anuncios)) {
        $erro = 'Selecione pelo menos um anúncio.';
    } else {
        $dados = [
            'nome' => $nome,
            'localizacao' => $localizacao,
            'layout' => $layout,
            'anuncios' => $anuncios
        ];
        
        $grupoId = $gruposManager->criarGrupo($dados);
        
        if ($grupoId) {
            $sucesso = 'Grupo criado com sucesso!';
        } else {
            $erro = 'Erro ao criar grupo.';
        }
    }
}

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Novo Grupo de Anúncios</h1>
    <a href="grupos-anuncios.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

    <?php if (isset($erro)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $erro; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($sucesso)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $sucesso; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações do Grupo</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome do Grupo *</label>
                                    <input type="text" class="form-control" id="nome" name="nome" 
                                           value="<?php echo $_POST['nome'] ?? ''; ?>" required>
                                    <div class="form-text">Ex: "Anúncios da Página Inicial", "Promoções Especiais"</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="localizacao" class="form-label">Localização *</label>
                                    <select class="form-select" id="localizacao" name="localizacao" required>
                                        <option value="">Selecione...</option>
                                        <option value="sidebar" <?php echo ($_POST['localizacao'] ?? '') === 'sidebar' ? 'selected' : ''; ?>>Sidebar</option>
                                        <option value="conteudo" <?php echo ($_POST['localizacao'] ?? '') === 'conteudo' ? 'selected' : ''; ?>>Conteúdo Principal</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="layout" class="form-label">Layout (Conteúdo Principal)</label>
                                    <select class="form-select" id="layout" name="layout">
                                        <option value="carrossel" <?php echo ($_POST['layout'] ?? 'carrossel') === 'carrossel' ? 'selected' : ''; ?>>Carrossel (ilimitado)</option>
                                        <option value="grade" <?php echo ($_POST['layout'] ?? '') === 'grade' ? 'selected' : ''; ?>>Grade (máx. 8 anúncios)</option>
                                    </select>
                                    <div class="form-text">Aplicado apenas para anúncios no conteúdo principal</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Selecionar Anúncios *</label>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Dica:</strong> Para grade, selecione no máximo 8 anúncios. Para carrossel, não há limite.
                            </div>
                            
                            <?php if (empty($todosAnuncios)): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Nenhum anúncio disponível. <a href="novo-anuncio.php">Crie um anúncio primeiro</a>.
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($todosAnuncios as $anuncio): ?>
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body p-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               name="anuncios[]" value="<?php echo $anuncio['id']; ?>" 
                                                               id="anuncio_<?php echo $anuncio['id']; ?>"
                                                               <?php echo in_array($anuncio['id'], $_POST['anuncios'] ?? []) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="anuncio_<?php echo $anuncio['id']; ?>">
                                                            <strong><?php echo htmlspecialchars($anuncio['titulo']); ?></strong>
                                                        </label>
                                                    </div>
                                                    <small class="text-muted d-block mt-1">
                                                        <i class="fas fa-map-marker-alt"></i> <?php echo ucfirst($anuncio['localizacao']); ?>
                                                        <br>
                                                        <i class="fas fa-mouse-pointer"></i> <?php echo $anuncio['cliques'] ?? 0; ?> cliques
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="grupos-anuncios.php" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Criar Grupo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações</h5>
                </div>
                <div class="card-body">
                    <h6><i class="fas fa-info-circle text-primary"></i> Sobre Grupos de Anúncios</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Sidebar:</strong> Anúncios individuais que se intercalam com posts
                        </li>
                        <li class="mb-2">
                            <strong>Conteúdo Principal:</strong> Grupos de anúncios exibidos entre posts
                        </li>
                        <li class="mb-2">
                            <strong>Grade:</strong> Máximo 8 anúncios em grid responsivo
                        </li>
                        <li class="mb-2">
                            <strong>Carrossel:</strong> Anúncios ilimitados com navegação
                        </li>
                    </ul>
                    
                    <hr>
                    
                    <h6><i class="fas fa-lightbulb text-warning"></i> Dicas</h6>
                    <ul class="list-unstyled">
                        <li class="mb-1">• Use nomes descritivos para facilitar a organização</li>
                        <li class="mb-1">• Para grade, selecione anúncios com imagens similares</li>
                        <li class="mb-1">• Carrossel funciona melhor com muitos anúncios</li>
                        <li class="mb-1">• Sidebar é ideal para anúncios individuais</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<script>
// Contador de anúncios selecionados
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="anuncios[]"]');
    const layoutSelect = document.getElementById('layout');
    
    function updateCounter() {
        const selected = document.querySelectorAll('input[name="anuncios[]"]:checked').length;
        const layout = layoutSelect.value;
        
        if (layout === 'grade' && selected > 8) {
            alert('Para layout de grade, selecione no máximo 8 anúncios.');
            return false;
        }
        
        return true;
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCounter);
    });
    
    layoutSelect.addEventListener('change', updateCounter);
});
</script>

<?php include 'includes/footer.php'; ?> 
