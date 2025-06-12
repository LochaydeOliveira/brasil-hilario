<?php
/**
 * Arquivo: categorias.php
 * Descrição: Gerenciamento de categorias do painel administrativo
 * Funcionalidades:
 * - Lista todas as categorias
 * - Permite adicionar novas categorias
 * - Permite editar categorias existentes
 * - Permite excluir categorias
 */

// Define o título da página
$page_title = 'Categorias';

// Inclui arquivos necessários
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Verifica se o usuário está autenticado
check_login();

// Conecta ao banco de dados
$conn = connectDB();

// Processa o formulário de categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome']);
    $slug = generateSlug($nome);
    $descricao = sanitize($_POST['descricao']);
    
    // Valida os campos
    if (empty($nome)) {
        setError('O nome da categoria é obrigatório.');
    } else {
        try {
            // Verifica se o slug já existe
            if (slugExists($conn, $slug, 'categorias')) {
                setError('Já existe uma categoria com este nome.');
            } else {
                // Cria a categoria
                $data = [
                    'nome' => $nome,
                    'slug' => $slug,
                    'descricao' => $descricao
                ];
                
                if (createCategory($conn, $data)) {
                    setSuccess('Categoria criada com sucesso!');
                } else {
                    setError('Erro ao criar categoria.');
                }
            }
        } catch (PDOException $e) {
            setError('Erro ao processar a categoria.');
        }
    }
}

// Obtém todas as categorias
$categorias = getAllCategories($conn);

// Inclui o cabeçalho
include 'includes/header.php';
?>

<!-- Formulário de categoria -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Nova Categoria</h5>
    </div>
    
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <input type="text" class="form-control" id="descricao" name="descricao">
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar
            </button>
        </form>
    </div>
</div>

<!-- Lista de categorias -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Lista de Categorias</h5>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categorias)): ?>
                        <tr>
                            <td colspan="4" class="text-center">Nenhuma categoria encontrada.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                                <td><?php echo htmlspecialchars($categoria['slug']); ?></td>
                                <td><?php echo htmlspecialchars($categoria['descricao']); ?></td>
                                <td>
                                    <a href="editar-categoria.php?id=<?php echo $categoria['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="excluir-categoria.php?id=<?php echo $categoria['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta categoria?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Inclui o rodapé
include 'includes/footer.php';
?> 