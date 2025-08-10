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

// Buscar todos os posts disponíveis
$todosPosts = $gruposManager->getAllPosts();

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
            'posts_especificos' => $postsEspecificos,
            'aparecer_inicio' => $aparecerInicio
        ];
        
        $grupoId = $gruposManager->criarGrupo($dados);
        
        if ($grupoId) {
            // Configurar posts específicos
            if ($gruposManager->atualizarConfiguracoesPosts($grupoId, $postsEspecificos, $aparecerInicio, $posts)) {
                $sucesso = 'Grupo criado com sucesso!';
            } else {
                $erro = 'Grupo criado, mas houve erro ao configurar posts específicos.';
            }
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
                            <label for="marca" class="form-label">Marca/Marketplace</label>
                            <select class="form-select" id="marca" name="marca">
                                <option value="" <?php echo ($_POST['marca'] ?? '') === '' ? 'selected' : ''; ?>>Vazio (Infoproduto)</option>
                                <option value="shopee" <?php echo ($_POST['marca'] ?? '') === 'shopee' ? 'selected' : ''; ?>>Shopee</option>
                                <option value="amazon" <?php echo ($_POST['marca'] ?? '') === 'amazon' ? 'selected' : ''; ?>>Amazon</option>
                            </select>
                            <div class="form-text">Selecione a marca ou marketplace dos produtos</div>
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
                                                        <i class="fas fa-mouse-pointer"></i> <?php echo $anuncio['total_cliques'] ?? 0; ?> cliques
                                                    </small>
                                                </div>
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
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" 
                                               id="posts_especificos" name="posts_especificos"
                                               <?php echo isset($_POST['posts_especificos']) ? 'checked' : ''; ?>>
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
                                               <?php echo isset($_POST['aparecer_inicio']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="aparecer_inicio">
                                            <strong>Aparecer na página inicial</strong>
                                        </label>
                                        <div class="form-text">Se marcado, o grupo aparecerá na página inicial</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Seleção de Posts (aparece apenas se "Posts específicos" estiver marcado) -->
                            <div id="selecao_posts" class="mt-3" style="display: <?php echo isset($_POST['posts_especificos']) ? 'block' : 'none'; ?>;">
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
                                                <div class="col-md-6 mb-2 post-item" data-titulo="<?php echo strtolower(htmlspecialchars($post['titulo'])); ?>" data-data_publicacao="<?php echo $post['data_publicacao']; ?>">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               name="posts[]" 
                                                               value="<?php echo $post['id']; ?>" 
                                                               id="post_<?php echo $post['id']; ?>"
                                                               <?php echo in_array($post['id'], $_POST['posts'] ?? []) ? 'checked' : ''; ?>>
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
        checkbox.addEventListener('change', function() {
            if (!updateCounter()) {
                this.checked = false;
            }
        });
    });
    
    layoutSelect.addEventListener('change', updateCounter);
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
</script>

<?php include 'includes/footer.php'; ?> 
