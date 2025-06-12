<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';
require_once '../includes/editor.php';

load_editor_scripts('tinymce');

// Obter categorias
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome ASC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2>Novo Post</h2>
    <form method="POST" action="save-post.php" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" class="form-control" id="titulo" name="titulo" required>
        </div>
        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" class="form-control" id="slug" name="slug" readonly>
        </div>
        <div class="mb-3">
            <label for="resumo" class="form-label">Resumo</label>
            <textarea class="form-control" id="resumo" name="resumo" rows="3" maxlength="300"></textarea>
        </div>
        <div class="mb-3">
            <label for="conteudo" class="form-label">Conteúdo</label>
            <textarea class="form-control" id="conteudo" name="conteudo" rows="10"></textarea>
        </div>
        <div class="mb-3">
            <label for="categoria_id" class="form-label">Categoria</label>
            <select class="form-select" id="categoria_id" name="categoria_id" required>
                <option value="">Selecione uma categoria</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="tags" class="form-label">Tags (separadas por vírgula)</label>
            <input type="text" class="form-control" id="tags" name="tags">
        </div>
        <div class="mb-3">
            <label for="meta_description" class="form-label">Meta descrição (SEO)</label>
            <textarea class="form-control" id="meta_description" name="meta_description" rows="2" maxlength="160"></textarea>
        </div>
        <div class="mb-3">
            <label for="featured_image" class="form-label">Imagem destacada</label>
            <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Publicar</button>
        <button type="button" class="btn btn-secondary" onclick="previewPost()">Visualizar</button>
    </form>

    <div id="preview" class="mt-5" style="display: none;">
        <h3>Pré-visualização</h3>
        <div id="preview-content" class="border p-3"></div>
    </div>
</div>

<script>
// Gerar slug automaticamente
function generateSlug(text) {
    return text.toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

document.getElementById('titulo').addEventListener('input', function () {
    document.getElementById('slug').value = generateSlug(this.value);
});

// Salvar categoria com localStorage
const categoriaSelect = document.getElementById('categoria_id');
categoriaSelect.value = localStorage.getItem('categoria_id') || '';
categoriaSelect.addEventListener('change', function() {
    localStorage.setItem('categoria_id', this.value);
});

// Visualização ao vivo
function previewPost() {
    const titulo = document.getElementById('titulo').value;
    const conteudo = tinymce.get('conteudo').getContent();
    const preview = document.getElementById('preview');
    const previewContent = document.getElementById('preview-content');

    preview.style.display = 'block';
    previewContent.innerHTML = `<h4>${titulo}</h4>` + conteudo;
}

// Atalho Ctrl+S para enviar
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        document.querySelector('form').submit();
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>