<?php
ob_start();
session_start();

require_once '../config/config.php';
require_once '../config/database_unified.php';
require_once '../includes/GruposAnunciosManager.php';
require_once '../includes/AnunciosManager.php';
require_once 'includes/auth.php';

// Verificar se o usuário está logado
check_login();

$dbManager = DatabaseManager::getInstance();

$page_title = 'Novo Grupo de Anúncios';

// Buscar anúncios disponíveis
$anuncios_disponiveis = $dbManager->query("
    SELECT id, titulo, marca, ativo
    FROM anuncios 
    WHERE ativo = 1
    ORDER BY titulo ASC
");

// Buscar posts para seleção
$posts = $dbManager->query("
    SELECT id, titulo, slug
    FROM posts 
    WHERE status = 'publicado'
    ORDER BY data_publicacao DESC
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $localizacao = $_POST['localizacao'];
    $layout = $_POST['layout'];
    $marca = $_POST['marca'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $posts_especificos = isset($_POST['posts_especificos']) ? 1 : 0;
    $aparecer_inicio = isset($_POST['aparecer_inicio']) ? 1 : 0;
    $anuncios_selecionados = $_POST['anuncios'] ?? [];
    $posts_selecionados = $_POST['posts'] ?? [];
    
    // Validações
    if (empty($nome)) {
        $erro = "Nome do grupo é obrigatório.";
    } elseif (empty($anuncios_selecionados)) {
        $erro = "Selecione pelo menos um anúncio para o grupo.";
    } elseif ($posts_especificos && empty($posts_selecionados)) {
        $erro = "Se marcou 'Posts específicos', selecione pelo menos um post.";
    } else {
        try {
            $dbManager->beginTransaction();
            
            // Criar grupo
            $sql_grupo = "INSERT INTO grupos_anuncios (nome, localizacao, layout, marca, ativo, posts_especificos, aparecer_inicio, criado_em) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $grupo_id = $dbManager->execute($sql_grupo, [
                $nome, $localizacao, $layout, $marca, $ativo, 
                $posts_especificos, $aparecer_inicio
            ]);
            
            if ($grupo_id) {
                $grupo_id = $dbManager->lastInsertId();
                
                // Associar anúncios ao grupo
                foreach ($anuncios_selecionados as $ordem => $anuncio_id) {
                    $dbManager->execute("
                        INSERT INTO grupos_anuncios_items (grupo_id, anuncio_id, ordem) 
                        VALUES (?, ?, ?)
                    ", [$grupo_id, $anuncio_id, $ordem]);
                }
                
                // Associar posts específicos (se houver)
                if ($posts_especificos && !empty($posts_selecionados)) {
                    foreach ($posts_selecionados as $post_id) {
                        $dbManager->execute("
                            INSERT INTO grupos_anuncios_posts (grupo_id, post_id) 
                            VALUES (?, ?)
                        ", [$grupo_id, $post_id]);
                    }
                }
                
                $dbManager->commit();
                $sucesso = "Grupo criado com sucesso!";
                $_POST = array(); // Limpar formulário
                
            } else {
                $dbManager->rollback();
                $erro = "Erro ao criar grupo.";
            }
        } catch (Exception $e) {
            $dbManager->rollback();
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
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="marca" class="form-label">Marca Principal</label>
                                    <select class="form-select" id="marca" name="marca">
                                        <option value="">Nenhuma</option>
                                        <option value="amazon" <?php echo (isset($_POST['marca']) && $_POST['marca'] === 'amazon') ? 'selected' : ''; ?>>Amazon</option>
                                        <option value="shopee" <?php echo (isset($_POST['marca']) && $_POST['marca'] === 'shopee') ? 'selected' : ''; ?>>Shopee</option>
                                    </select>
                                    <div class="form-text">Marca principal do grupo (opcional)</div>
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
                    
                    <!-- Configurações de Exibição -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Configurações de Exibição</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="posts_especificos" name="posts_especificos" 
                                               <?php echo (isset($_POST['posts_especificos']) && $_POST['posts_especificos']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="posts_especificos">
                                            Posts Específicos
                                        </label>
                                        <div class="form-text">Se marcado, o grupo só aparecerá nos posts selecionados</div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="aparecer_inicio" name="aparecer_inicio" 
                                               <?php echo (isset($_POST['aparecer_inicio']) && $_POST['aparecer_inicio']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="aparecer_inicio">
                                            Aparecer na Página Inicial
                                        </label>
                                        <div class="form-text">Se marcado, o grupo aparecerá na página inicial</div>
                                    </div>
                                </div>
                                
                                <div id="posts_container" style="display: none;">
                                    <label class="form-label">Selecionar Posts</label>
                                    <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                        <?php foreach ($posts as $post): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="posts[]" 
                                                       value="<?php echo $post['id']; ?>" 
                                                       id="post_<?php echo $post['id']; ?>"
                                                       <?php echo (isset($_POST['posts']) && in_array($post['id'], $_POST['posts'])) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="post_<?php echo $post['id']; ?>">
                                                    <?php echo htmlspecialchars($post['titulo']); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
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
                            <div class="row">
                                <?php foreach ($anuncios_disponiveis as $anuncio): ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input anuncio-checkbox" type="checkbox" 
                                                           name="anuncios[]" value="<?php echo $anuncio['id']; ?>" 
                                                           id="anuncio_<?php echo $anuncio['id']; ?>"
                                                           <?php echo (isset($_POST['anuncios']) && in_array($anuncio['id'], $_POST['anuncios'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="anuncio_<?php echo $anuncio['id']; ?>">
                                                        <strong><?php echo htmlspecialchars($anuncio['titulo']); ?></strong>
                                                    </label>
                                                </div>
                                                <?php if (!empty($anuncio['marca'])): ?>
                                                    <small class="text-muted">
                                                        <?php if ($anuncio['marca'] === 'amazon'): ?>
                                                            <i class="fab fa-amazon"></i> Amazon
                                                        <?php elseif ($anuncio['marca'] === 'shopee'): ?>
                                                            <i class="fas fa-shopping-cart"></i> Shopee
                                                        <?php else: ?>
                                                            <?php echo ucfirst($anuncio['marca']); ?>
                                                        <?php endif; ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="selecionar_todos">
                                    <i class="fas fa-check-square"></i> Selecionar Todos
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="limpar_selecao">
                                    <i class="fas fa-square"></i> Limpar Seleção
                                </button>
                            </div>
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
    const postsEspecificosCheckbox = document.getElementById('posts_especificos');
    const postsContainer = document.getElementById('posts_container');
    const anuncioCheckboxes = document.querySelectorAll('.anuncio-checkbox');
    const selecionarTodosBtn = document.getElementById('selecionar_todos');
    const limparSelecaoBtn = document.getElementById('limpar_selecao');
    
    // Toggle posts específicos
    postsEspecificosCheckbox.addEventListener('change', function() {
        postsContainer.style.display = this.checked ? 'block' : 'none';
    });
    
    // Selecionar todos os anúncios
    selecionarTodosBtn.addEventListener('click', function() {
        anuncioCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
    });
    
    // Limpar seleção de anúncios
    limparSelecaoBtn.addEventListener('click', function() {
        anuncioCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    });
    
    // Mostrar posts container se já estava marcado
    if (postsEspecificosCheckbox.checked) {
        postsContainer.style.display = 'block';
    }
});
</script>

<?php include 'includes/footer.php'; ?> 
