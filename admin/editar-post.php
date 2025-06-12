<?php
/**
 * Arquivo: editar-post.php
 * Descrição: Interface para edição de posts existentes
 * Funcionalidades:
 * - Carrega dados do post do banco
 * - Exibe formulário de edição
 * - Integra editor TinyMCE
 * - Permite atualização de todos os campos
 */

// Inclui arquivos necessários
require_once '../config/config.php';      // Configurações gerais
require_once '../includes/db.php';        // Conexão com banco de dados
require_once 'includes/auth.php';         // Funções de autenticação
require_once 'includes/editor-config.php'; // Configuração do TinyMCE

// Verifica se o usuário está autenticado
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Inicializa variáveis
$post = null;
$categories = [];

// Verifica se foi fornecido um ID
if (!isset($_GET['id'])) {
    header('Location: posts.php');
    exit;
}

$post_id = (int)$_GET['id'];

try {
    // Busca o post específico no banco
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    // Se o post não existir, redireciona
    if (!$post) {
        header('Location: posts.php');
        exit;
    }

    // Busca todas as categorias para o select
    $stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erro ao carregar dados: " . $e->getMessage();
}

// Define o título da página
$page_title = "Editar Post";
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Cabeçalho da página -->


            <!-- Exibe mensagem de erro se houver -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Formulário de edição -->
            <form method="post" action="save-post.php" class="needs-validation" novalidate>
                <!-- Campo oculto com ID do post -->
                <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                
                <!-- Campo de título -->
                <div class="mb-3">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" class="form-control" id="title" name="titulo" 
                           value="<?php echo htmlspecialchars($post['titulo'] ?? ''); ?>" required>
                </div>

                <!-- Campo de slug (URL amigável) -->
                <div class="mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control" id="slug" name="slug" 
                           value="<?php echo htmlspecialchars($post['slug'] ?? ''); ?>" required>
                </div>

                <!-- Seleção de categoria -->
                <div class="mb-3">
                    <label for="category_id" class="form-label">Categoria</label>
                    <select class="form-select" id="category_id" name="categoria_id" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo (isset($post['categoria_id']) && $post['categoria_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Editor TinyMCE para o conteúdo -->
                <div class="mb-3">
                    <label for="content" class="form-label">Conteúdo</label>
                    <textarea id="editor" name="conteudo"><?php echo htmlspecialchars($post['conteudo'] ?? ''); ?></textarea>
                </div>

                <!-- Campo de resumo -->
                <div class="mb-3">
                    <label for="excerpt" class="form-label">Resumo</label>
                    <textarea class="form-control" id="excerpt" name="resumo" rows="3"><?php echo htmlspecialchars($post['resumo'] ?? ''); ?></textarea>
                </div>

                <!-- Checkbox de publicação -->
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="published" name="publicado" value="1" 
                               <?php echo (isset($post['publicado']) && $post['publicado']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="published">Publicar</label>
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <a href="posts.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </main>
    </div>
</div>

<!-- Scripts do TinyMCE -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
    // Inicializa o editor TinyMCE
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init(<?php echo json_encode($editor_config); ?>);
    });

    // Gera o slug automaticamente a partir do título
    document.getElementById('title').addEventListener('input', function() {
        const title = this.value;
        const slug = title
            .toLowerCase()
            .normalize('NFD')  // Remove acentos
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')  // Substitui caracteres especiais por hífen
            .replace(/(^-|-$)/g, '');     // Remove hífens do início e fim
        document.getElementById('slug').value = slug;
    });
</script>

<?php include 'includes/footer.php'; ?>