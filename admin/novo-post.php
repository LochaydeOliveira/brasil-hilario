<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/editors.php';

// Verificar se o usuário está logado
check_login();

// Processar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = filter_input(INPUT_POST, 'titulo', FILTER_UNSAFE_RAW);
    $resumo = filter_input(INPUT_POST, 'resumo', FILTER_UNSAFE_RAW);
    $conteudo = $_POST['conteudo'];
    $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
    $tags = filter_input(INPUT_POST, 'tags', FILTER_UNSAFE_RAW);
    $status_form = filter_input(INPUT_POST, 'status', FILTER_UNSAFE_RAW);
    $editor_type = filter_input(INPUT_POST, 'editor_type', FILTER_UNSAFE_RAW);
    
    // Converter o status do formulário para o formato do banco de dados (0 ou 1 para 'publicado')
    $publicado = ($status_form === 'publicado') ? 1 : 0;
    
    // Gerar slug do título
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo)));
    
    // Validação básica
    if (empty($titulo) || empty($conteudo) || !$categoria_id) {
        $erro = "Por favor, preencha o título, o conteúdo e selecione uma categoria.";
    } else {
        try {
            // Inserir o post
            $stmt = $pdo->prepare("INSERT INTO posts (
                titulo, 
                slug, 
                resumo, 
                conteudo, 
                categoria_id, 
                publicado,
                editor_type,
                autor_id,
                criado_em,
                atualizado_em,
                visualizacoes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0)");
            
            $stmt->execute([
                $titulo, 
                $slug, 
                $resumo, 
                $conteudo, 
                $categoria_id, 
                $publicado,
                $editor_type,
                $_SESSION['usuario_id']
            ]);
            
            $post_id = $pdo->lastInsertId();
            
            // Processar tags
            if (!empty($tags)) {
                $tags_array = array_map('trim', explode(',', $tags));
                foreach ($tags_array as $tag_nome) {
                    // Verificar se a tag já existe
                    $stmt = $pdo->prepare("SELECT id FROM tags WHERE nome = ?");
                    $stmt->execute([$tag_nome]);
                    $tag = $stmt->fetch();
                    
                    if (!$tag) {
                        // Criar nova tag
                        $tag_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tag_nome)));
                        $stmt = $pdo->prepare("INSERT INTO tags (nome, slug) VALUES (?, ?)");
                        $stmt->execute([$tag_nome, $tag_slug]);
                        $tag_id = $pdo->lastInsertId();
                    } else {
                        $tag_id = $tag['id'];
                    }
                    
                    // Associar tag ao post
                    $stmt = $pdo->prepare("INSERT INTO posts_tags (post_id, tag_id) VALUES (?, ?)");
                    $stmt->execute([$post_id, $tag_id]);
                }
            }
            
            header('Location: posts.php?success=1');
            exit;
            
        } catch (PDOException $e) {
            $erro = "Erro ao salvar o post: " . $e->getMessage();
        }
    }
}

// Buscar categorias
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome");
$categorias = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Novo Post</h1>
            </div>
            
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="resumo" class="form-label">Resumo</label>
                            <textarea class="form-control" id="resumo" name="resumo" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="conteudo" class="form-label">Conteúdo</label>
                            <div class="editor-toolbar mb-2">
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="editor_type" id="editor_tinymce" value="tinymce" checked>
                                    <label class="btn btn-outline-primary" for="editor_tinymce">TinyMCE</label>
                                    
                                    <input type="radio" class="btn-check" name="editor_type" id="editor_markdown" value="markdown">
                                    <label class="btn btn-outline-primary" for="editor_markdown">Markdown</label>
                                </div>
                            </div>
                            <textarea class="form-control" id="conteudo" name="conteudo" rows="15" required></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Publicação</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="rascunho">Rascunho</option>
                                        <option value="publicado">Publicado</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="categoria_id" class="form-label">Categoria</label>
                                    <select class="form-select" id="categoria_id" name="categoria_id">
                                        <option value="">Selecione uma categoria</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?php echo $categoria['id']; ?>">
                                                <?php echo htmlspecialchars($categoria['nome']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="tags" class="form-label">Tags</label>
                                    <input type="text" class="form-control" id="tags" name="tags" 
                                           placeholder="Separe as tags por vírgula">
                                    <div class="form-text">Exemplo: tecnologia, marketing, seo</div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">Publicar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>

<?php
// Carregar scripts do editor
load_editor_scripts('tinymce');
include 'includes/footer.php';
?> 