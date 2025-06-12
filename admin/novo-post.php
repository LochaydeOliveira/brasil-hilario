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

$post = null; // Para novo post, $post é nulo
$categories = [];

try {
    // Busca as categorias
    $stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erro ao carregar categorias: " . $e->getMessage();
}

$page_title = "Novo Post";
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
                <div class="mb-3">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="" required>
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control" id="slug" name="slug" 
                           value="" required>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Categoria</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo (isset($post['categoria_id']) && $post['categoria_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Conteúdo</label>
                    <textarea id="editor" name="content"></textarea>
                </div>

                <div class="mb-3">
                    <label for="excerpt" class="form-label">Resumo</label>
                    <textarea class="form-control" id="excerpt" name="excerpt" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="featured_image" class="form-label">Imagem Destacada</label>
                    <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="published" name="published" value="1">
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