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

// Obter ID do post
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Buscar post existente
if ($post_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if (!$post) {
        $_SESSION['error'] = "Post não encontrado.";
        header('Location: posts.php');
        exit;
    }
} else {
    $_SESSION['error'] = "ID inválido.";
    header('Location: posts.php');
    exit;
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
                <h1 class="h2">Editar Post</h1>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form method="POST" action="save-post.php" enctype="multipart/form-data" class="needs-validation" novalidate>
                <input type="hidden" name="id" value="<?php echo $post_id; ?>">

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug" value="<?php echo htmlspecialchars($post['slug']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="excerpt" class="form-label">Resumo</label>
                            <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?php echo htmlspecialchars($post['excerpt']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Conteúdo</label>
                            <textarea class="form-control" id="content" name="content" rows="15" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Prévia</label>
                            <div id="preview" class="border rounded p-3 bg-light"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Publicação</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Categoria</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">Selecione uma categoria</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?php echo $categoria['id']; ?>" <?php echo ($post['category_id'] == $categoria['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($categoria['nome']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="published" name="published" <?php echo ($post['published'] ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="published">Publicado</label>
                                </div>

                                <div class="mb-3">
                                    <label for="featured_image" class="form-label">Imagem Destacada</label>
                                    <input class="form-control" type="file" id="featured_image" name="featured_image" accept="image/*">
                                    <?php if (!empty($post['featured_image'])): ?>
                                        <div class="mt-2">
                                            <img src="<?php echo $post['featured_image']; ?>" alt="Imagem atual" class="img-fluid rounded">
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <button type="submit" class="btn btn-success w-100">Salvar Alterações</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>

<?php
load_editor_scripts('tinymce');
include 'includes/footer.php';
?>

<script>
// Atualização automática do slug baseado no título
const titleInput = document.getElementById('title');
const slugInput = document.getElementById('slug');

function generateSlug(text) {
    return text.toLowerCase()
        .normalize('NFD').replace(/\p{Diacritic}/gu, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim().replace(/\s+/g, '-')
        .replace(/--+/g, '-');
}

titleInput.addEventListener('input', () => {
    slugInput.value = generateSlug(titleInput.value);
});

// Atualização da prévia do conteúdo
const contentInput = document.getElementById('content');
const preview = document.getElementById('preview');
contentInput.addEventListener('input', () => {
    preview.innerHTML = tinymce.activeEditor.getContent();
});

// Salvar categoria com localStorage
const categorySelect = document.getElementById('category_id');
categorySelect.addEventListener('change', () => {
    localStorage.setItem('selected_category', categorySelect.value);
});

document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('selected_category');
    if (saved && categorySelect) {
        categorySelect.value = saved;
    }
});

// Atalho Ctrl+S para salvar
window.addEventListener('keydown', function (e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        document.querySelector('form').submit();
    }
});
</script>