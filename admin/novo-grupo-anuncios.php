<?php
ob_start();
session_start();

require_once '../config/config.php';
require_once '../includes/db.php';
require_once '../includes/GruposAnunciosManager.php';
require_once '../includes/AnunciosManager.php';
require_once 'includes/auth.php';

// Verificar se o usuário está logado
check_login();

// Conexão via $pdo (definido em ../includes/db.php)

$page_title = 'Novo Grupo de Anúncios';

// Buscar anúncios disponíveis
try {
    $stmt = $pdo->prepare("SELECT id, titulo, marca, ativo FROM anuncios WHERE ativo = 1 ORDER BY titulo ASC");
    $stmt->execute();
    $anuncios_disponiveis = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $anuncios_disponiveis = [];
}

// Buscar posts para seleção
try {
    $stmt = $pdo->prepare("SELECT id, titulo, slug FROM posts WHERE status = 'publicado' ORDER BY data_publicacao DESC");
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $posts = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $localizacao = $_POST['localizacao'];
    $layout = $_POST['layout'] ?? 'carrossel';
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $anuncios_selecionados = $_POST['anuncios'] ?? [];
    $posts_selecionados = $_POST['posts'] ?? [];

    // Validações obrigatórias no novo modelo
    if (empty($nome)) {
        $erro = "Nome do grupo é obrigatório.";
    } elseif (empty($anuncios_selecionados)) {
        $erro = "Selecione pelo menos um anúncio para o grupo.";
    } elseif (empty($posts_selecionados)) {
        $erro = "Selecione pelo menos um post para exibir este grupo.";
    } else {
        try {
            $pdo->beginTransaction();

            // Ajustes de regra: sidebar força layout 'grade'; posts_especificos=1; aparecer_inicio=0; marca vazia
            if ($localizacao === 'sidebar') {
                $layout = 'grade';
            }

            $sql_grupo = "INSERT INTO grupos_anuncios (nome, localizacao, layout, marca, ativo, posts_especificos, aparecer_inicio, criado_em) 
                          VALUES (?, ?, ?, '', ?, 1, 0, NOW())";
            $stmt = $pdo->prepare($sql_grupo);
            $ok = $stmt->execute([$nome, $localizacao, $layout, $ativo]);

            if ($ok) {
                $grupo_id = (int)$pdo->lastInsertId();

                // Associar anúncios ao grupo (ordem pela posição no seletor)
                $stmtItem = $pdo->prepare("INSERT INTO grupos_anuncios_items (grupo_id, anuncio_id, ordem) VALUES (?, ?, ?)");
                foreach ($anuncios_selecionados as $ordem => $anuncio_id) {
                    $stmtItem->execute([$grupo_id, $anuncio_id, $ordem]);
                }

                // Associar posts obrigatórios
                $stmtPost = $pdo->prepare("INSERT INTO grupos_anuncios_posts (grupo_id, post_id) VALUES (?, ?)");
                foreach ($posts_selecionados as $post_id) {
                    $stmtPost->execute([$grupo_id, $post_id]);
                }

                $pdo->commit();
                $sucesso = "Grupo criado com sucesso!";
                $_POST = array();
            } else {
                $pdo->rollBack();
                $erro = "Erro ao criar grupo.";
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $erro = "Erro: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="h3 mb-4">Criar Novo Grupo de Anúncios</h1>
            
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>
            
            <?php if (isset($sucesso)): ?>
                <div class="alert alert-success"><?php echo $sucesso; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="row">
                    <!-- Informações Básicas -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Informações do Grupo</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome do Grupo *</label>
                                    <input type="text" class="form-control" id="nome" name="nome" 
                                           value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" 
                                           required>
                                    <div class="form-text">Nome para identificar este grupo</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="localizacao" class="form-label">Localização *</label>
                                            <select class="form-select" id="localizacao" name="localizacao" required>
                                                <option value="">Selecione...</option>
                                                <option value="sidebar" <?php echo (isset($_POST['localizacao']) && $_POST['localizacao'] === 'sidebar') ? 'selected' : ''; ?>>Sidebar</option>
                                                <option value="conteudo" <?php echo (isset($_POST['localizacao']) && $_POST['localizacao'] === 'conteudo') ? 'selected' : ''; ?>>Conteúdo Principal</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="layout" class="form-label">Layout</label>
                                            <select class="form-select" id="layout" name="layout">
                                                <option value="carrossel" <?php echo (isset($_POST['layout']) && $_POST['layout'] === 'carrossel') ? 'selected' : ''; ?>>Carrossel</option>
                                                <option value="grade" <?php echo (isset($_POST['layout']) && $_POST['layout'] === 'grade') ? 'selected' : ''; ?>>Grade</option>
                                            </select>
                                            <div class="form-text">Para Sidebar o layout será automaticamente "Grade" empilhado.</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" 
                                               <?php echo (isset($_POST['ativo']) && $_POST['ativo']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="ativo">
                                            Grupo Ativo
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Seleção de Posts (sempre obrigatória) -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Posts Específicos *</h5>
                            </div>
                            <div class="card-body">
                                <label for="posts" class="form-label">Selecione os posts onde este grupo aparecerá</label>
                                <select class="form-select" id="posts" name="posts[]" multiple size="10" required>
                                    <?php foreach ($posts as $post): ?>
                                        <option value="<?php echo $post['id']; ?>" <?php echo (isset($_POST['posts']) && in_array($post['id'], $_POST['posts'])) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($post['titulo']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Segure Ctrl (Windows) ou Cmd (Mac) para selecionar múltiplos.</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Seleção de Anúncios -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Selecionar Anúncios *</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($anuncios_disponiveis)): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Nenhum anúncio disponível!</strong>
                                <br>
                                Crie anúncios primeiro em <a href="anuncios.php" class="alert-link">Anúncios</a> antes de criar um grupo.
                            </div>
                        <?php else: ?>
                            <label for="anuncios" class="form-label">Selecione os anúncios (produtos) a exibir</label>
                            <select class="form-select" id="anuncios" name="anuncios[]" multiple size="10" required>
                                <?php foreach ($anuncios_disponiveis as $anuncio): ?>
                                    <option value="<?php echo $anuncio['id']; ?>" <?php echo (isset($_POST['anuncios']) && in_array($anuncio['id'], $_POST['anuncios'])) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($anuncio['titulo']); ?>
                                        <?php echo !empty($anuncio['marca']) ? ' - ' . htmlspecialchars($anuncio['marca']) : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Segure Ctrl (Windows) ou Cmd (Mac) para selecionar múltiplos.</div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="grupos-anuncios.php" class="btn btn-secondary">← Voltar</a>
                    <button type="submit" class="btn btn-primary" <?php echo empty($anuncios_disponiveis) ? 'disabled' : ''; ?>>
                        Criar Grupo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const localizacaoSelect = document.getElementById('localizacao');
    const layoutSelect = document.getElementById('layout');
    
    // Função para atualizar configuração baseada na localização
    function aplicarRegraSidebar() {
        const isSidebar = localizacaoSelect.value === 'sidebar';
        layoutSelect.value = isSidebar ? 'grade' : layoutSelect.value;
        layoutSelect.disabled = isSidebar;
    }

    localizacaoSelect.addEventListener('change', aplicarRegraSidebar);
    aplicarRegraSidebar();
});
</script>

<?php include 'includes/footer.php'; ?> 
