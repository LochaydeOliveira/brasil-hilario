<?php
require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/editor-config.php';
require_once 'includes/functions.php';

// Verifica se o usuário está autenticado
check_login();

$post = null;
$categories = [];
$tags = [];
$tags_string = '';

// Verifica se foi fornecido um ID
if (!isset($_GET['id'])) {
    header('Location: posts.php');
    exit;
}

$post_id = (int)$_GET['id'];

try {
    // Busca o post
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();

    if (!$post) {
        header('Location: posts.php');
        exit;
    }

    // Verifica se o usuário tem permissão para editar o post
    if (!can_edit_post($post['autor_id'])) {
        showError('Você não tem permissão para editar este post.');
        exit;
    }

    // Busca as categorias
    $stmt = $conn->prepare("SELECT * FROM categorias ORDER BY nome");
    $stmt->execute();
    $result = $stmt->get_result();
    $categories = $result->fetch_all(MYSQLI_ASSOC);

    // Busca tags do post
    $stmt = $conn->prepare("
        SELECT t.nome 
        FROM tags t 
        JOIN post_tags pt ON t.id = pt.tag_id 
        WHERE pt.post_id = ?
    ");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tags_data = $result->fetch_all(MYSQLI_ASSOC);
    $tags = array_column($tags_data, 'nome');
    $tags_string = implode(', ', $tags);
} catch (Exception $e) {
    $error = "Erro ao carregar dados: " . $e->getMessage();
}

$page_title = getPageTitle();
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
                <input type="hidden" name="id" value="<?php echo $post['id']; ?>">

                <div class="mb-3">
                    <label for="title" class="form-label titles-form-adm">Título</label>
                    <input type="text" class="form-control" id="title" name="titulo" 
                           value="<?php echo htmlspecialchars($post['titulo'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="featured_image" class="form-label titles-form-adm">Imagem Destacada</label>
                    <?php if (!empty($post['imagem_destacada'])): ?>
                        <div class="mb-2">
                            <img src="../uploads/images/<?php echo htmlspecialchars($post['imagem_destacada']); ?>" 
                                 alt="Imagem atual" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                    <small class="form-text text-muted">Formatos aceitos: JPG, PNG, GIF e WebP. Tamanho máximo: 5MB</small>
                    <?php if (!empty($post['imagem_destacada'])): ?>
                        <small class="form-text text-muted">Deixe em branco para manter a imagem atual</small>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="tags" class="form-label titles-form-adm">Tags (separadas por vírgula)</label>
                    <input type="text" class="form-control" id="tags" name="tags" value="<?php echo htmlspecialchars($tags_string); ?>" placeholder="Ex: humor, política, esportes">
                    <div class="form-text">Digite as tags separadas por vírgula. Ex: humor, política, esportes</div>
                </div>

                <div class="mb-3">
                    <label for="excerpt" class="form-label titles-form-adm">Resumo</label>
                    <textarea class="form-control" id="excerpt" name="resumo" rows="3"><?php echo htmlspecialchars($post['resumo'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label titles-form-adm">Slug</label>
                    <input type="text" class="form-control" id="slug" name="slug" 
                           value="<?php echo htmlspecialchars($post['slug'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label titles-form-adm">Categoria</label>
                    <select class="form-select" id="category_id" name="categoria_id" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo (isset($post['categoria_id']) && $post['categoria_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label titles-form-adm">Conteúdo</label>
                    <textarea id="editor" name="conteudo"><?php echo htmlspecialchars($post['conteudo'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="published" name="publicado" value="1" 
                               <?php echo (isset($post['publicado']) && $post['publicado']) ? 'checked' : ''; ?>>
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