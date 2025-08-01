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

// Verificar se o ID foi fornecido
$anuncio_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$anuncio_id) {
    header('Location: anuncios.php');
    exit;
}

// Buscar anúncio
$anuncio = $anunciosManager->getAnuncio($anuncio_id);
if (!$anuncio) {
    header('Location: anuncios.php');
    exit;
}

$posts = $anunciosManager->getPostsParaSelecao();
$mensagem = '';
$tipo_mensagem = '';

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $link_compra = trim($_POST['link_compra'] ?? '');
    $localizacao = $_POST['localizacao'] ?? '';
    $cta_ativo = isset($_POST['cta_ativo']);
    $cta_texto = trim($_POST['cta_texto'] ?? 'Saiba Mais');
    $ativo = isset($_POST['ativo']);
    $posts_selecionados = $_POST['posts'] ?? [];
    
    // Validar campos obrigatórios
    if (empty($titulo) || empty($link_compra) || empty($localizacao)) {
        $mensagem = 'Todos os campos obrigatórios devem ser preenchidos.';
        $tipo_mensagem = 'danger';
    } else {
        // Processar upload da imagem (se houver)
        $imagem_path = $anuncio['imagem']; // Manter imagem atual
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/images/';
            
            // Criar diretório se não existir
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $filename = 'anuncio_' . time() . '_' . uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $upload_path)) {
                    $imagem_path = '/uploads/images/' . $filename;
                } else {
                    $mensagem = 'Erro ao fazer upload da imagem.';
                    $tipo_mensagem = 'danger';
                }
            } else {
                $mensagem = 'Formato de imagem não suportado. Use JPG, PNG, GIF ou WebP.';
                $tipo_mensagem = 'danger';
            }
        }
        
        // Se não houve erro no upload, atualizar o anúncio
        if (empty($mensagem)) {
            $dados = [
                'titulo' => $titulo,
                'imagem' => $imagem_path,
                'link_compra' => $link_compra,
                'localizacao' => $localizacao,
                'cta_ativo' => $cta_ativo,
                'cta_texto' => $cta_texto,
                'ativo' => $ativo,
                'posts' => $posts_selecionados
            ];
            
            $sucesso = $anunciosManager->atualizarAnuncio($anuncio_id, $dados);
            
            if ($sucesso) {
                header('Location: anuncios.php?success=1');
                exit;
            } else {
                $mensagem = 'Erro ao atualizar anúncio. Tente novamente.';
                $tipo_mensagem = 'danger';
            }
        }
    }
}

$page_title = 'Editar Anúncio';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Editar Anúncio</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="anuncios.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            
            <?php if (!empty($mensagem)): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($mensagem); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Editar Anúncio</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Informações Básicas -->
                            <div class="col-md-8">
                                <h6 class="mb-3">Informações do Anúncio</h6>
                                
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Título do Anúncio *</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" 
                                           value="<?php echo htmlspecialchars($anuncio['titulo']); ?>" 
                                           required placeholder="Digite o título do anúncio">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="link_compra" class="form-label">Link de Compra *</label>
                                    <input type="url" class="form-control" id="link_compra" name="link_compra" 
                                           value="<?php echo htmlspecialchars($anuncio['link_compra']); ?>" 
                                           required placeholder="https://exemplo.com/produto">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="localizacao" class="form-label">Localização *</label>
                                            <select class="form-select" id="localizacao" name="localizacao" required>
                                                <option value="">Selecione...</option>
                                                <option value="sidebar" <?php echo $anuncio['localizacao'] === 'sidebar' ? 'selected' : ''; ?>>Sidebar</option>
                                                <option value="conteudo" <?php echo $anuncio['localizacao'] === 'conteudo' ? 'selected' : ''; ?>>Conteúdo Principal</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="imagem" class="form-label">Imagem do Anúncio</label>
                                            <input type="file" class="form-control" id="imagem" name="imagem" 
                                                   accept="image/*">
                                            <div class="form-text">Deixe em branco para manter a imagem atual</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- CTA -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="cta_ativo" name="cta_ativo" 
                                               <?php echo $anuncio['cta_ativo'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="cta_ativo">
                                            Ativar botão de ação (CTA)
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3" id="cta_texto_container" style="display: <?php echo $anuncio['cta_ativo'] ? 'block' : 'none'; ?>;">
                                    <label for="cta_texto" class="form-label">Texto do Botão CTA</label>
                                    <input type="text" class="form-control" id="cta_texto" name="cta_texto" 
                                           value="<?php echo htmlspecialchars($anuncio['cta_texto']); ?>" 
                                           placeholder="Ex: Comprar Agora, Saiba Mais, Ver Ofertas">
                                </div>
                                
                                <!-- Status -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" 
                                               <?php echo $anuncio['ativo'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="ativo">
                                            Anúncio ativo
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Preview -->
                            <div class="col-md-4">
                                <h6 class="mb-3">Preview do Anúncio</h6>
                                <div class="card" id="preview-card">
                                    <div class="card-body">
                                        <div class="anuncio-patrocinado-preview">PATROCINADO</div>
                                        <div class="preview-imagem-container mb-2">
                                            <img id="preview-imagem" src="<?php echo htmlspecialchars($anuncio['imagem']); ?>" 
                                                 alt="Preview" class="img-fluid rounded" style="max-height: 150px; width: 100%; object-fit: cover;">
                                        </div>
                                        <h6 id="preview-titulo" class="card-title"><?php echo htmlspecialchars($anuncio['titulo']); ?></h6>
                                        <button id="preview-cta" class="btn btn-primary btn-sm" style="display: <?php echo $anuncio['cta_ativo'] ? 'inline-block' : 'none'; ?>;"><?php echo htmlspecialchars($anuncio['cta_texto']); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Seleção de Posts -->
                        <div class="mt-4">
                            <h6 class="mb-3">Posts onde o anúncio será exibido</h6>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="todos_posts">
                                    <label class="form-check-label" for="todos_posts">
                                        <strong>Selecionar todos os posts</strong>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="row" style="max-height: 300px; overflow-y: auto;">
                                <?php foreach ($posts as $post): ?>
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input post-checkbox" type="checkbox" 
                                                   name="posts[]" value="<?php echo $post['id']; ?>" 
                                                   id="post_<?php echo $post['id']; ?>"
                                                   <?php echo in_array($post['id'], $anuncio['posts_ids']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="post_<?php echo $post['id']; ?>">
                                                <?php echo htmlspecialchars($post['titulo']); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Atualizar Anúncio
                            </button>
                            <a href="anuncios.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctaCheckbox = document.getElementById('cta_ativo');
    const ctaContainer = document.getElementById('cta_texto_container');
    const ctaTexto = document.getElementById('cta_texto');
    const previewCta = document.getElementById('preview-cta');
    const previewTitulo = document.getElementById('preview-titulo');
    const previewImagem = document.getElementById('preview-imagem');
    const imagemInput = document.getElementById('imagem');
    const tituloInput = document.getElementById('titulo');
    const todosPostsCheckbox = document.getElementById('todos_posts');
    const postCheckboxes = document.querySelectorAll('.post-checkbox');
    
    // Toggle CTA
    ctaCheckbox.addEventListener('change', function() {
        if (this.checked) {
            ctaContainer.style.display = 'block';
            previewCta.style.display = 'inline-block';
        } else {
            ctaContainer.style.display = 'none';
            previewCta.style.display = 'none';
        }
    });
    
    // Preview do título
    tituloInput.addEventListener('input', function() {
        previewTitulo.textContent = this.value || 'Título do anúncio aparecerá aqui';
    });
    
    // Preview da imagem
    imagemInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImagem.src = e.target.result;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Preview do CTA
    ctaTexto.addEventListener('input', function() {
        previewCta.textContent = this.value || 'Saiba Mais';
    });
    
    // Selecionar todos os posts
    todosPostsCheckbox.addEventListener('change', function() {
        postCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Verificar se todos os posts estão selecionados
    postCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const todosSelecionados = Array.from(postCheckboxes).every(cb => cb.checked);
            todosPostsCheckbox.checked = todosSelecionados;
        });
    });
    
    // Verificar estado inicial do "selecionar todos"
    const todosSelecionados = Array.from(postCheckboxes).every(cb => cb.checked);
    todosPostsCheckbox.checked = todosSelecionados;
});
</script>

<?php include 'includes/footer.php'; ?> 