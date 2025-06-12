<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/editor-config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;
$categories = [];

try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();

    if ($post_id > 0) {
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

// Configuração do TinyMCE
$editor_config = [
    'selector' => 'textarea#editor',
    'height' => 500,
    'menubar' => true,
    'plugins' => [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons',
        'codesample', 'hr', 'pagebreak', 'nonbreaking', 'toc', 'visualchars',
        'quickbars', 'imagetools', 'paste', 'autoresize'
    ],
    'toolbar' => 'undo redo | styles | bold italic underline strikethrough | ' .
                'alignleft aligncenter alignright alignjustify | ' .
                'bullist numlist outdent indent | link image media | ' .
                'forecolor backcolor emoticons | removeformat code | ' .
                'fullscreen preview | help',
    'content_style' => 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #333; } ' .
                      'h1, h2, h3, h4, h5, h6 { margin-top: 24px; margin-bottom: 16px; font-weight: 600; line-height: 1.25; } ' .
                      'p { margin-top: 0; margin-bottom: 16px; } ' .
                      'img { max-width: 100%; height: auto; } ' .
                      'blockquote { padding: 0 1em; color: #6a737d; border-left: 0.25em solid #dfe2e5; margin: 0 0 16px 0; } ' .
                      'code { padding: 0.2em 0.4em; margin: 0; font-size: 85%; background-color: rgba(27,31,35,0.05); border-radius: 3px; } ' .
                      'pre { padding: 16px; overflow: auto; font-size: 85%; line-height: 1.45; background-color: #f6f8fa; border-radius: 3px; } ' .
                      'table { border-spacing: 0; border-collapse: collapse; margin: 16px 0; } ' .
                      'table th, table td { padding: 6px 13px; border: 1px solid #dfe2e5; } ' .
                      'table tr { background-color: #fff; border-top: 1px solid #c6cbd1; } ' .
                      'table tr:nth-child(2n) { background-color: #f6f8fa; }',
    'images_upload_url' => 'upload-image.php',
    'images_upload_handler' => 'function (blobInfo, success, failure) {
        var xhr, formData;
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open("POST", "upload-image.php");
        xhr.onload = function() {
            var json;
            if (xhr.status != 200) {
                failure("HTTP Error: " + xhr.status);
                return;
            }
            json = JSON.parse(xhr.responseText);
            if (!json || typeof json.location != "string") {
                failure("Invalid JSON: " + xhr.responseText);
                return;
            }
            success(json.location);
        };
        formData = new FormData();
        formData.append("file", blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
    }',
    'quickbars_selection_toolbar' => 'bold italic | quicklink h2 h3 blockquote',
    'quickbars_insert_toolbar' => 'quickimage quicktable',
    'contextmenu' => 'link image table',
    'branding' => false,
    'promotion' => false,
    'browser_spellcheck' => true,
    'paste_data_images' => true,
    'image_advtab' => true,
    'image_title' => true,
    'automatic_uploads' => true,
    'file_picker_types' => 'image',
    'images_reuse_filename' => true,
    'relative_urls' => false,
    'remove_script_host' => false,
    'convert_urls' => true,
    'language' => 'pt_BR',
    'language_url' => '/assets/js/tinymce/langs/pt_BR.js'
];
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        tinymce.init(<?php echo json_encode($editor_config); ?>);
    });

    document.getElementById('title').addEventListener('input', function () {
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
