<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/editor-config.php';

// Verifica se o usuário está autenticado
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;
$categories = [];

try {
    // Busca as categorias
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();

    if ($post_id > 0) {
        // Busca o post
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch();

        if (!$post) {
            header('Location: posts.php');
            exit;
        }
    }
} catch (PDOException $e) {
    $error = "Erro ao carregar dados: " . $e->getMessage();
}

$page_title = $post ? "Editar Post" : "Novo Post";
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><?php echo $page_title; ?></h1>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" action="save-post.php" class="needs-validation" novalidate enctype="multipart/form-data">
                <?php if ($post_id): ?>
                    <input type="hidden" name="id" value="<?php echo $post_id; ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="<?php echo $post ? htmlspecialchars($post['title']) : ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control" id="slug" name="slug" 
                           value="<?php echo $post ? htmlspecialchars($post['slug']) : ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Categoria</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                    <?php echo ($post && $post['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Conteúdo</label>
                    <div class="btn-group mb-2">
                        <button type="button" class="btn btn-outline-primary active" id="tinymceBtn">
                            <i class="fas fa-edit"></i> Editor Visual
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="markdownBtn">
                            <i class="fas fa-code"></i> Markdown
                        </button>
                    </div>
                    <textarea id="editor" name="content"><?php echo $post ? htmlspecialchars($post['content']) : ''; ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="excerpt" class="form-label">Resumo</label>
                    <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?php echo $post ? htmlspecialchars($post['excerpt']) : ''; ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="featured_image" class="form-label">Imagem Destacada</label>
                    <?php if ($post && $post['featured_image']): ?>
                        <div class="mb-2">
                            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                                 alt="Imagem destacada" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="published" name="published" value="1"
                               <?php echo ($post && $post['published']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="published">Publicar</label>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <a href="posts.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </main>
    </div>
</div>

<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<!-- Markdown Editor -->
<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
<script>
    let editor;
    let markdownEditor;
    let currentMode = 'tinymce';

    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa o TinyMCE
        editor = tinymce.init(<?php echo json_encode($editor_config); ?>).then(function(editors) {
            console.log('Editor inicializado com sucesso');
        }).catch(function(error) {
            console.error('Erro ao inicializar o editor:', error);
        });

        // Inicializa o Markdown Editor
        markdownEditor = new EasyMDE({
            element: document.getElementById('editor'),
            spellChecker: false,
            status: false,
            toolbar: [
                'bold', 'italic', 'heading', '|',
                'quote', 'unordered-list', 'ordered-list', '|',
                'link', 'image', '|',
                'preview', 'side-by-side', 'fullscreen', '|',
                'guide'
            ],
            initialValue: document.getElementById('editor').value
        });
        markdownEditor.togglePreview();

        // Botões de alternância
        document.getElementById('tinymceBtn').addEventListener('click', function() {
            if (currentMode !== 'tinymce') {
                const content = markdownEditor.value();
                markdownEditor.toTextArea();
                markdownEditor = null;
                
                editor = tinymce.init(<?php echo json_encode($editor_config); ?>).then(function(editors) {
                    editors[0].setContent(content);
                });
                
                currentMode = 'tinymce';
                this.classList.add('active');
                document.getElementById('markdownBtn').classList.remove('active');
            }
        });

        document.getElementById('markdownBtn').addEventListener('click', function() {
            if (currentMode !== 'markdown') {
                const content = tinymce.get('editor').getContent();
                tinymce.get('editor').remove();
                
                markdownEditor = new EasyMDE({
                    element: document.getElementById('editor'),
                    spellChecker: false,
                    status: false,
                    toolbar: [
                        'bold', 'italic', 'heading', '|',
                        'quote', 'unordered-list', 'ordered-list', '|',
                        'link', 'image', '|',
                        'preview', 'side-by-side', 'fullscreen', '|',
                        'guide'
                    ],
                    initialValue: content
                });
                markdownEditor.togglePreview();
                
                currentMode = 'markdown';
                this.classList.add('active');
                document.getElementById('tinymceBtn').classList.remove('active');
            }
        });
    });

    // Gera o slug automaticamente a partir do título
    document.getElementById('title').addEventListener('input', function() {
        const title = this.value;
        const slug = title
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
        document.getElementById('slug').value = slug;
    });
</script>

<?php include 'includes/footer.php'; ?> 