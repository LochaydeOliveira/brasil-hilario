<?php
ob_start();
session_start();

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/GruposAnunciosManager.php';
require_once '../includes/AnunciosManager.php';

$page_title = 'Editar Grupo de Anúncios';

// Verificar login - usar a mesma verificação do header
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$gruposManager = new GruposAnunciosManager($pdo);
$anunciosManager = new AnunciosManager($pdo);

// Verificar se foi passado um ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: grupos-anuncios.php');
    exit;
}

$grupoId = (int)$_GET['id'];

// Buscar dados do grupo
$grupo = $gruposManager->getGrupo($grupoId);
if (!$grupo) {
    header('Location: grupos-anuncios.php');
    exit;
}

// Buscar anúncios do grupo
$anunciosDoGrupo = $gruposManager->getAnunciosDoGrupo($grupoId);
$anunciosIds = array_column($anunciosDoGrupo, 'anuncio_id');

// Buscar posts do grupo
$postsDoGrupo = $gruposManager->getPostsDoGrupo($grupoId);
$postsIds = array_column($postsDoGrupo, 'id');

// Buscar todos os posts disponíveis
$todosPosts = $gruposManager->getAllPosts();

// Buscar todos os anúncios para seleção
$todosAnuncios = $anunciosManager->getAllAnunciosComStats();

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $localizacao = $_POST['localizacao'];
    $layout = $_POST['layout'] ?? 'carrossel';
    $marca = $_POST['marca'] ?? '';
    $anuncios = $_POST['anuncios'] ?? [];
    $postsEspecificos = isset($_POST['posts_especificos']);
    $aparecerInicio = isset($_POST['aparecer_inicio']);
    $posts = $_POST['posts'] ?? [];
    
    // Lógica específica para sidebar: sempre requer posts específicos
    if ($localizacao === 'sidebar') {
        $postsEspecificos = true;
        if (empty($posts)) {
            $erro = 'Para grupos da sidebar, você deve selecionar pelo menos um post específico.';
        }
    }
    
    if (empty($nome)) {
        $erro = 'Nome do grupo é obrigatório.';
    } elseif (empty($anuncios)) {
        $erro = 'Selecione pelo menos um anúncio.';
    } elseif ($postsEspecificos && empty($posts)) {
        $erro = 'Se você selecionou "Posts específicos", deve escolher pelo menos um post.';
    } else {
        $dados = [
            'nome' => $nome,
            'localizacao' => $localizacao,
            'layout' => $layout,
            'marca' => $marca,
            'anuncios' => $anuncios,
            'ativo' => isset($_POST['ativo'])
        ];
        
        if ($gruposManager->atualizarGrupo($grupoId, $dados)) {
            // Atualizar configurações de posts
            if ($gruposManager->atualizarConfiguracoesPosts($grupoId, $postsEspecificos, $aparecerInicio, $posts)) {
                $sucesso = 'Grupo atualizado com sucesso!';
                // Atualizar dados do grupo para exibir no formulário
                $grupo = $gruposManager->getGrupo($grupoId);
                $anunciosDoGrupo = $gruposManager->getAnunciosDoGrupo($grupoId);
                $anunciosIds = array_column($anunciosDoGrupo, 'anuncio_id');
                $postsDoGrupo = $gruposManager->getPostsDoGrupo($grupoId);
                $postsIds = array_column($postsDoGrupo, 'id');
            } else {
                $erro = 'Erro ao atualizar configurações de posts.';
            }
        } else {
            $erro = 'Erro ao atualizar grupo.';
        }
    }
}

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Editar Grupo de Anúncios</h1>
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
                                           value="<?php echo htmlspecialchars($grupo['nome']); ?>" required>
                                    <div class="form-text">Ex: "Anúncios da Página Inicial", "Promoções Especiais"</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="localizacao" class="form-label">Localização *</label>
                                    <select class="form-select" id="localizacao" name="localizacao" required>
                                        <option value="">Selecione...</option>
                                        <option value="sidebar" <?php echo $grupo['localizacao'] === 'sidebar' ? 'selected' : ''; ?>>Sidebar</option>
                                        <option value="conteudo" <?php echo $grupo['localizacao'] === 'conteudo' ? 'selected' : ''; ?>>Conteúdo Principal</option>
                                    </select>
                                    <div class="form-text">Onde o grupo será exibido no site</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="layout" class="form-label">Layout *</label>
                                    <select class="form-select" id="layout" name="layout" required>
                                        <option value="carrossel" <?php echo $grupo['layout'] === 'carrossel' ? 'selected' : ''; ?>>Carrossel</option>
                                        <option value="grade" <?php echo $grupo['layout'] === 'grade' ? 'selected' : ''; ?>>Grade</option>
                                    </select>
                                    <div class="form-text">Como os anúncios serão organizados</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="marca" class="form-label">Marca/Marketplace</label>
                                    <select class="form-select" id="marca" name="marca">
                                        <option value="" <?php echo ($grupo['marca'] ?? '') === '' ? 'selected' : ''; ?>>Vazio (Infoproduto)</option>
                                        <option value="shopee" <?php echo ($grupo['marca'] ?? '') === 'shopee' ? 'selected' : ''; ?>>Shopee</option>
                                        <option value="amazon" <?php echo ($grupo['marca'] ?? '') === 'amazon' ? 'selected' : ''; ?>>Amazon</option>
                                    </select>
                                    <div class="form-text">Selecione a marca ou marketplace dos produtos</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" 
                                               <?php echo $grupo['ativo'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="ativo">
                                            Grupo ativo
                                        </label>
                                    </div>
                                    <div class="form-text">Grupos inativos não são exibidos no site</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Selecionar Anúncios *</label>
                            <div class="form-text mb-2">Selecione os anúncios que farão parte deste grupo</div>
                            
                            <?php if (empty($todosAnuncios)): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Nenhum anúncio encontrado. 
                                    <a href="novo-anuncio.php" class="alert-link">Crie um anúncio primeiro</a>.
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($todosAnuncios as $anuncio): ?>
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="anuncios[]" 
                                                       value="<?php echo $anuncio['id']; ?>" 
                                                       id="anuncio_<?php echo $anuncio['id']; ?>"
                                                       <?php echo in_array($anuncio['id'], $anunciosIds) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="anuncio_<?php echo $anuncio['id']; ?>">
                                                    <strong><?php echo htmlspecialchars($anuncio['titulo']); ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo ucfirst($anuncio['localizacao']); ?> • 
                                                        <?php echo $anuncio['total_cliques'] ?? 0; ?> cliques
                                                    </small>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Configuração de Posts Específicos -->
                        <div class="mb-3">
                            <label class="form-label">Configuração de Exibição</label>
                            <div class="form-text mb-2">Defina onde este grupo de anúncios será exibido</div>
                            
                            <?php if ($grupo['localizacao'] === 'sidebar'): ?>
                                <!-- Configuração específica para sidebar -->
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Grupo da Sidebar:</strong> Os anúncios aparecerão apenas nos posts específicos selecionados.
                                </div>
                                <input type="hidden" name="posts_especificos" value="1">
                            <?php else: ?>
                                <!-- Configuração para conteúdo principal -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="posts_especificos" name="posts_especificos"
                                                   <?php echo ($grupo['posts_especificos'] ?? false) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="posts_especificos">
                                                <strong>Posts específicos</strong>
                                            </label>
                                            <div class="form-text">Se marcado, o grupo aparecerá apenas nos posts selecionados</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="aparecer_inicio" name="aparecer_inicio"
                                                   <?php echo ($grupo['aparecer_inicio'] ?? true) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="aparecer_inicio">
                                                <strong>Aparecer na página inicial</strong>
                                            </label>
                                            <div class="form-text">Se marcado, o grupo aparecerá na página inicial</div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Seleção de Posts (sempre aparece para sidebar, ou se "Posts específicos" estiver marcado) -->
                            <div id="selecao_posts" class="mt-3" style="display: <?php echo ($grupo['localizacao'] === 'sidebar' || ($grupo['posts_especificos'] ?? false)) ? 'block' : 'none'; ?>;">
                                <label class="form-label">Selecionar Posts *</label>
                                <div class="form-text mb-2">Selecione os posts onde este grupo será exibido</div>
                                
                                <?php if (empty($todosPosts)): ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Nenhum post encontrado.
                                    </div>
                                <?php else: ?>
                                    <!-- Barra de busca -->
                                    <div class="mb-3">
                                        <input type="text" class="form-control" id="busca_posts" 
                                               placeholder="Digite para buscar posts..." 
                                               onkeyup="filtrarPosts()">
                                    </div>
                                    
                                    <!-- Controles rápidos -->
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="selecionarTodosPosts()">
                                            <i class="fas fa-check-double"></i> Selecionar Todos
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="limparSelecaoPosts()">
                                            <i class="fas fa-times"></i> Limpar Seleção
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="selecionarRecentes()">
                                            <i class="fas fa-clock"></i> Últimos 10 Posts
                                        </button>
                                    </div>
                                    
                                    <!-- Lista de posts com scroll -->
                                    <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                        <div class="row" id="lista_posts">
                                            <?php foreach ($todosPosts as $post): ?>
                                                <div class="col-md-6 mb-2 post-item" data-titulo="<?php echo strtolower(htmlspecialchars($post['titulo'])); ?>">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               name="posts[]" 
                                                               value="<?php echo $post['id']; ?>" 
                                                               id="post_<?php echo $post['id']; ?>"
                                                               <?php echo in_array($post['id'], $postsIds) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="post_<?php echo $post['id']; ?>">
                                                            <strong><?php echo htmlspecialchars($post['titulo']); ?></strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <?php echo date('d/m/Y', strtotime($post['data_publicacao'])); ?>
                                                            </small>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Contador -->
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <span id="contador_posts">0</span> de <?php echo count($todosPosts); ?> posts selecionados
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="grupos-anuncios.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Atualizar Grupo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações do Grupo</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>ID:</strong> <?php echo $grupo['id']; ?>
                    </div>
                    <div class="mb-3">
                        <strong>Criado em:</strong><br>
                        <?php echo date('d/m/Y H:i', strtotime($grupo['criado_em'])); ?>
                    </div>
                    <?php if ($grupo['atualizado_em']): ?>
                        <div class="mb-3">
                            <strong>Última atualização:</strong><br>
                            <?php echo date('d/m/Y H:i', strtotime($grupo['atualizado_em'])); ?>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <strong>Anúncios no grupo:</strong><br>
                        <span class="badge bg-primary"><?php echo count($anunciosIds); ?> anúncio(s)</span>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Anúncios Selecionados</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($anunciosDoGrupo)): ?>
                        <p class="text-muted">Nenhum anúncio selecionado</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($anunciosDoGrupo as $anuncio): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($anuncio['titulo']); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo ucfirst($anuncio['localizacao']); ?></small>
                                    </div>
                                    <span class="badge bg-secondary"><?php echo $anuncio['total_cliques'] ?? 0; ?> cliques</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<script>
// Validação do layout para grade
document.getElementById('layout').addEventListener('change', function() {
    const layout = this.value;
    const checkboxes = document.querySelectorAll('input[name="anuncios[]"]');
    const checkedBoxes = document.querySelectorAll('input[name="anuncios[]"]:checked');
    
    if (layout === 'grade' && checkedBoxes.length > 8) {
        alert('O layout de grade suporta no máximo 8 anúncios. Por favor, desmarque alguns anúncios.');
        this.value = 'carrossel';
    }
});

// Validação ao marcar/desmarcar anúncios
document.querySelectorAll('input[name="anuncios[]"]').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const layout = document.getElementById('layout').value;
        const checkedBoxes = document.querySelectorAll('input[name="anuncios[]"]:checked');
        
        if (layout === 'grade' && checkedBoxes.length > 8) {
            alert('O layout de grade suporta no máximo 8 anúncios. Por favor, desmarque outros anúncios primeiro.');
            this.checked = false;
        }
    });
});

// Controlar exibição da seleção de posts
document.getElementById('posts_especificos').addEventListener('change', function() {
    const selecaoPosts = document.getElementById('selecao_posts');
    const postsCheckboxes = document.querySelectorAll('input[name="posts[]"]');
    
    if (this.checked) {
        selecaoPosts.style.display = 'block';
        // NÃO marcar como required - usar apenas validação customizada
        // Atualizar contador
        setTimeout(atualizarContador, 100);
    } else {
        selecaoPosts.style.display = 'none';
        // Desmarcar todos os posts
        postsCheckboxes.forEach(function(checkbox) {
            checkbox.checked = false;
        });
        atualizarContador();
    }
});

// Atualizar contador quando checkboxes mudarem
document.addEventListener('change', function(e) {
    if (e.target.name === 'posts[]') {
        atualizarContador();
    }
});

// Inicializar contador
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(atualizarContador, 100);
});

// Validação do formulário
document.querySelector('form').addEventListener('submit', function(e) {
    const postsEspecificos = document.getElementById('posts_especificos').checked;
    const postsSelecionados = document.querySelectorAll('input[name="posts[]"]:checked');
    
    if (postsEspecificos && postsSelecionados.length === 0) {
        e.preventDefault();
        alert('Se você selecionou "Posts específicos", deve escolher pelo menos um post.');
        return false;
    }
    
    // Se posts específicos está marcado e há posts selecionados, permitir envio
    if (postsEspecificos && postsSelecionados.length > 0) {
        return true;
    }
    
    // Se posts específicos não está marcado, permitir envio
    if (!postsEspecificos) {
        return true;
    }
});

// Funções para seleção de posts
function filtrarPosts() {
    const busca = document.getElementById('busca_posts').value.toLowerCase();
    const posts = document.querySelectorAll('#lista_posts .post-item');
    posts.forEach(function(post) {
        const titulo = post.getAttribute('data-titulo');
        if (titulo.includes(busca)) {
            post.style.display = 'block';
        } else {
            post.style.display = 'none';
        }
    });
}

function selecionarTodosPosts() {
    const postsCheckboxes = document.querySelectorAll('input[name="posts[]"]');
    postsCheckboxes.forEach(function(checkbox) {
        checkbox.checked = true;
    });
    atualizarContador();
}

function limparSelecaoPosts() {
    const postsCheckboxes = document.querySelectorAll('input[name="posts[]"]');
    postsCheckboxes.forEach(function(checkbox) {
        checkbox.checked = false;
    });
    atualizarContador();
}

function selecionarRecentes() {
    const posts = document.querySelectorAll('#lista_posts .post-item');
    const recentes = Array.from(posts).sort((a, b) => {
        const dataA = new Date(a.getAttribute('data-data_publicacao'));
        const dataB = new Date(b.getAttribute('data-data_publicacao'));
        return dataB - dataA; // Ordena de mais recente para mais antigo
    });

    // Limpar seleção anterior
    limparSelecaoPosts();

    // Selecionar os 10 mais recentes
    for (let i = 0; i < Math.min(10, recentes.length); i++) {
        recentes[i].querySelector('input[type="checkbox"]').checked = true;
    }
    atualizarContador();
}

function atualizarContador() {
    const postsSelecionados = document.querySelectorAll('input[name="posts[]"]:checked');
    document.getElementById('contador_posts').textContent = postsSelecionados.length;
}
</script>

<?php include 'includes/footer.php'; ?> 