<?php
/**
 * Arquivo: usuarios.php
 * Descrição: Gerenciamento de usuários do painel administrativo
 * Funcionalidades:
 * - Lista todos os usuários
 * - Permite adicionar novos usuários
 * - Permite editar usuários existentes
 * - Permite excluir usuários
 * - Controle de acesso baseado em permissões
 */

// Define o título da página
$page_title = 'Usuários';

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

// Conecta ao banco de dados
$conn = connectDB();

// Processa o formulário de usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitiza os dados do formulário
    $nome = sanitize($_POST['nome']);
    $email = sanitize($_POST['email']);
    $senha = $_POST['senha'];
    $tipo = sanitize($_POST['tipo']);
    
    // Valida os campos obrigatórios
    if (empty($nome) || empty($email) || empty($senha) || empty($tipo)) {
        setError('Todos os campos são obrigatórios.');
    } else {
        try {
            // Verifica se o email já existe
            if (emailExists($conn, $email)) {
                setError('Este email já está em uso.');
            } else {
                // Cria o novo usuário
                $data = [
                    'nome' => $nome,
                    'email' => $email,
                    'senha' => password_hash($senha, PASSWORD_DEFAULT),
                    'tipo' => $tipo
                ];
                
                if (createUser($conn, $data)) {
                    setSuccess('Usuário criado com sucesso!');
                } else {
                    setError('Erro ao criar usuário.');
                }
            }
        } catch (PDOException $e) {
            setError('Erro ao processar o formulário.');
        }
    }
}

// Busca todos os usuários
$usuarios = getAllUsers($conn);

// Inclui o cabeçalho
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gerenciar Usuários</h1>
            </div>
            
            <?php showMessages(); ?>
            
            <!-- Formulário de Novo Usuário -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Novo Usuário</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="admin">Administrador</option>
                                    <option value="editor">Editor</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Criar Usuário</button>
                    </form>
                </div>
            </div>
            
            <!-- Lista de Usuários -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Lista de Usuários</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Tipo</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo $usuario['id']; ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td><?php echo ucfirst($usuario['tipo']); ?></td>
                                    <td>
                                        <a href="editar-usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-primary">Editar</a>
                                        <a href="excluir-usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 