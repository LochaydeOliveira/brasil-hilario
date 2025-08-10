<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/GruposAnunciosManager.php';

// Verificar login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Debug Grupos de Anúncios';
$gruposManager = new GruposAnunciosManager($pdo);

// Buscar todos os grupos
$grupos = $gruposManager->getAllGruposComStats();

// Buscar alguns posts para teste
$posts = $gruposManager->getAllPosts();
$postsTeste = array_slice($posts, 0, 5); // Primeiros 5 posts

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Debug Grupos de Anúncios</h1>
    <a href="grupos-anuncios.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Configurações dos Grupos</h5>
            </div>
            <div class="card-body">
                <?php if (empty($grupos)): ?>
                    <p class="text-muted">Nenhum grupo encontrado.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Posts Específicos</th>
                                    <th>Aparecer Início</th>
                                    <th>Posts Associados</th>
                                    <th>Debug</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($grupos as $grupo): ?>
                                    <?php $debug = $gruposManager->debugGrupo($grupo['id']); ?>
                                    <tr>
                                        <td><?php echo $grupo['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($grupo['nome']); ?></strong></td>
                                        <td>
                                            <span class="badge bg-<?php echo $grupo['posts_especificos'] ? 'warning' : 'success'; ?>">
                                                <?php echo $grupo['posts_especificos'] ? 'SIM' : 'NÃO'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $grupo['aparecer_inicio'] ? 'success' : 'danger'; ?>">
                                                <?php echo $grupo['aparecer_inicio'] ? 'SIM' : 'NÃO'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($grupo['posts_especificos']): ?>
                                                <small><?php echo $debug['posts_titulos'] ?: 'Nenhum post'; ?></small>
                                            <?php else: ?>
                                                <small class="text-muted">Todos os posts</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info" 
                                                    onclick="testarGrupo(<?php echo $grupo['id']; ?>)">
                                                Testar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Teste de Filtro</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Grupo:</label>
                        <select class="form-select" id="grupo_teste">
                            <option value="">Selecione um grupo...</option>
                            <?php foreach ($grupos as $grupo): ?>
                                <option value="<?php echo $grupo['id']; ?>">
                                    <?php echo htmlspecialchars($grupo['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Post:</label>
                        <select class="form-select" id="post_teste">
                            <option value="">Selecione um post...</option>
                            <option value="home">Página Inicial</option>
                            <?php foreach ($postsTeste as $post): ?>
                                <option value="<?php echo $post['id']; ?>">
                                    <?php echo htmlspecialchars($post['titulo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-primary d-block" onclick="testarFiltro()">
                            <i class="fas fa-search"></i> Testar Filtro
                        </button>
                    </div>
                </div>
                
                <div id="resultado_teste" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<script>
function testarFiltro() {
    const grupoId = document.getElementById('grupo_teste').value;
    const postId = document.getElementById('post_teste').value;
    
    if (!grupoId || !postId) {
        alert('Selecione um grupo e um post para testar.');
        return;
    }
    
    const isHomePage = postId === 'home';
    const postIdReal = isHomePage ? null : postId;
    
    // Fazer requisição AJAX para testar
    fetch('debug-grupos-ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `grupo_id=${grupoId}&post_id=${postIdReal}&is_home_page=${isHomePage ? 1 : 0}`
    })
    .then(response => response.json())
    .then(data => {
        const resultado = document.getElementById('resultado_teste');
        resultado.innerHTML = `
            <div class="alert alert-${data.deve_aparecer ? 'success' : 'warning'}">
                <h6>Resultado do Teste:</h6>
                <p><strong>Grupo:</strong> ${data.grupo_id}</p>
                <p><strong>Post:</strong> ${data.post_id || 'Página Inicial'}</p>
                <p><strong>Posts Específicos:</strong> ${data.posts_especificos ? 'SIM' : 'NÃO'}</p>
                <p><strong>Aparecer Início:</strong> ${data.aparecer_inicio ? 'SIM' : 'NÃO'}</p>
                <p><strong>Deve Aparecer:</strong> <span class="badge bg-${data.deve_aparecer ? 'success' : 'danger'}">${data.deve_aparecer ? 'SIM' : 'NÃO'}</span></p>
                <p><strong>Motivo:</strong> ${data.motivo}</p>
            </div>
        `;
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao testar filtro.');
    });
}

function testarGrupo(grupoId) {
    document.getElementById('grupo_teste').value = grupoId;
}
</script>

<?php include 'includes/footer.php'; ?> 