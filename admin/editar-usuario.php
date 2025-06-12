<?php
/**
 * Arquivo: editar-usuario.php
 * Descrição: Edição de usuários do painel administrativo
 * Funcionalidades:
 * - Carrega dados do usuário
 * - Permite editar nome, email e tipo
 * - Permite alterar senha
 * - Valida dados antes de salvar
 * - Redireciona com mensagem de sucesso/erro
 */

// Define o título da página
$page_title = 'Editar Usuário';

// Inclui arquivos necessários
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Verifica se o usuário está autenticado e é admin
check_login();
if (!is_admin()) {
    setError('Acesso negado.');
    redirect('index.php');
}

// Verifica se o ID foi fornecido
if (!isset($_GET['id'])) {
    setError('ID do usuário não fornecido.');
    redirect('usuarios.php');
}

// Conecta ao banco de dados
$conn = connectDB();

// Busca o usuário
$usuario = getUser($conn, $_GET['id']);

if (!$usuario) {
    setError('Usuário não encontrado.');
    redirect('usuarios.php');
}

// Processa o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitiza os dados do formulário
    $nome = sanitize($_POST['nome']);
    $email = sanitize($_POST['email']);
    $tipo = sanitize($_POST['tipo']);
    $senha = $_POST['senha'] ?? '';
    
    // Valida os campos obrigatórios
    if (empty($nome) || empty($email) || empty($tipo)) {
        setError('Nome, email e tipo são obrigatórios.');
    } else {
        try {
            // Verifica se o email já existe (exceto para o próprio usuário)
            if (emailExists($conn, $email, $usuario['id'])) {
                setError('Este email já está em uso.');
            } else {
                // Prepara os dados para atualização
                $data = [
                    'nome' => $nome,
                    'email' => $email,
                    'tipo' => $tipo
                ];
                
                // Adiciona senha apenas se foi fornecida
                if (!empty($senha)) {
                    $data['senha'] = password_hash($senha, PASSWORD_DEFAULT);
                }
                
                // Atualiza o usuário
                if (updateUser($conn, $usuario['id'], $data)) {
                    setSuccess('Usuário atualizado com sucesso!');
                    redirect('usuarios.php');
                } else {
                    setError('Erro ao atualizar usuário.');
                }
            }
        } catch (PDOException $e) {
            setError('Erro ao processar o formulário.');
        }
    }
}

// Inclui o cabeçalho
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Editar Usuário</h1>
            </div>
            
            <?php showMessages(); ?>
            
            <!-- Formulário de Edição -->
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="senha" class="form-label">Nova Senha (deixe em branco para manter a atual)</label>
                                <input type="password" class="form-control" id="senha" name="senha">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="admin" <?php echo $usuario['tipo'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                                    <option value="editor" <?php echo $usuario['tipo'] === 'editor' ? 'selected' : ''; ?>>Editor</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 