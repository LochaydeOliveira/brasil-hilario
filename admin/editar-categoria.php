<?php
/**
 * Arquivo: editar-categoria.php
 * Descrição: Edição de categorias do painel administrativo
 * Funcionalidades:
 * - Carrega dados da categoria
 * - Permite editar nome e descrição
 * - Atualiza slug automaticamente
 * - Valida dados antes de salvar
 */

// Define o título da página
$page_title = 'Editar Categoria';

// Inclui arquivos necessários
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Verifica se o usuário está autenticado
check_login();

// Verifica se o ID foi fornecido
if (!isset($_GET['id'])) {
    setError('ID da categoria não fornecido.');
    redirect('categorias.php');
}

// Conecta ao banco de dados
$conn = connectDB();

// Obtém a categoria
$categoria = getCategory($conn, $_GET['id']);

// Verifica se a categoria existe
if (!$categoria) {
    setError('Categoria não encontrada.');
    redirect('categorias.php');
}

// Processa o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome']);
    $slug = generateSlug($nome);
    $descricao = sanitize($_POST['descricao']);
    
    // Valida os campos
    if (empty($nome)) {
        setError('O nome da categoria é obrigatório.');
    } else {
        try {
            // Verifica se o slug já existe (exceto para a própria categoria)
            if (slugExists($conn, $slug, 'categorias', $categoria['id'])) {
                setError('Já existe uma categoria com este nome.');
            } else {
                // Atualiza a categoria
                $data = [
                    'nome' => $nome,
                    'slug' => $slug,
                    'descricao' => $descricao
                ];
                
                if (updateCategory($conn, $categoria['id'], $data)) {
                    setSuccess('Categoria atualizada com sucesso!');
                    redirect('categorias.php');
                } else {
                    setError('Erro ao atualizar categoria.');
                }
            }
        } catch (PDOException $e) {
            setError('Erro ao processar a categoria.');
        }
    }
}

// Inclui o cabeçalho
include 'includes/header.php';
?>

<!-- Formulário de edição -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Editar Categoria</h5>
    </div>
    
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($categoria['nome']); ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <input type="text" class="form-control" id="descricao" name="descricao" value="<?php echo htmlspecialchars($categoria['descricao']); ?>">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="slug" class="form-label">Slug</label>
                <input type="text" class="form-control" id="slug" value="<?php echo htmlspecialchars($categoria['slug']); ?>" readonly>
                <small class="form-text text-muted">O slug é gerado automaticamente a partir do nome.</small>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar
                </button>
                
                <a href="categorias.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Script para gerar slug -->
<script>
document.getElementById('nome').addEventListener('input', function() {
    const nome = this.value;
    const slug = nome.toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
    
    document.getElementById('slug').value = slug;
});
</script>

<?php
// Inclui o rodapé
include 'includes/footer.php';
?> 