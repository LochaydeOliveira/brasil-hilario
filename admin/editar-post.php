<?php
require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/editor-config.php';

// Verifica se o usuário está autenticado
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$post = null;
$categories = [];
$error = null;

// Verifica se foi fornecido um ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: posts.php');
    exit;
}

$post_id = (int)$_GET['id'];

try {
    // Busca o post
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if (!$post) {
        header('Location: posts.php');
        exit;
    }

    // Busca as categorias
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erro ao carregar dados: " . $e->getMessage();
}

$page_title = "Editar Post";
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><?php echo $page_title; ?></h1>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" action="save-post.php" class="needs-validation" novalidate enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                
                <div class="mb-3">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="<?php echo htmlspecialchars($post['title']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control" id="slug" name="slug" 
                           value="<?php echo htmlspecialchars($post['slug']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Categoria</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                    <?php echo $category['id'] == $post['category_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Conteúdo</label>
                    <textarea id="editor" name="content"><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="excerpt" class="form-label">Resumo</label>
                    <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?php echo htmlspecialchars($post['excerpt']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="featured_image" class="form-label">Imagem Destacada</label>
                    <?php if ($post['featured_image']): ?>
                        <div class="mb-2">
                            <img src="../uploads/images/<?php echo $post['featured_image']; ?>" 
                                 alt="Imagem atual" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                    <small class="form-text text-muted">Deixe em branco para manter a imagem atual</small>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="published" name="published" value="1"
                               <?php echo $post['published'] ? 'checked' : ''; ?>>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init(<?php echo json_encode($editor_config); ?>);
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