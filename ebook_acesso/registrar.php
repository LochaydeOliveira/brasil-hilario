<?php
require_once __DIR__ . '/conexao.php';

// Permitir acesso sem segredo somente no PRIMEIRO USUÁRIO
$temUsuarios = 0;
try {
    $temUsuarios = (int)$pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
} catch (Throwable $e) {
    $temUsuarios = 0;
}

if ($temUsuarios > 0 && (($_GET['secret'] ?? '') !== ADMIN_SECRET)) {
    header('Location: index.php');
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $ativo = isset($_POST['ativo']) ? 1 : 1;
    if ($nome && filter_var($email, FILTER_VALIDATE_EMAIL) && $senha) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha, ativo) VALUES (?, ?, ?, ?)');
            $stmt->execute([$nome, $email, $hash, $ativo]);
            $msg = 'Usuário criado com sucesso.';
        } catch (Throwable $e) { $msg = 'Erro ao criar usuário (e-mail pode existir).'; }
    } else { $msg = 'Preencha todos os campos corretamente.'; }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container" style="max-width:640px;">
        <h3>Registrar Usuário</h3>
        <?php if($msg): ?><div class="alert alert-info py-2"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
        <form method="post">
            <div class="mb-3"><label class="form-label">Nome</label><input type="text" class="form-control" name="nome" required></div>
            <div class="mb-3"><label class="form-label">E-mail</label><input type="email" class="form-control" name="email" required></div>
            <div class="mb-3"><label class="form-label">Senha</label><input type="password" class="form-control" name="senha" required></div>
            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="ativo" id="ativo" checked><label class="form-check-label" for="ativo">Ativo</label></div>
            <button class="btn btn-primary">Salvar</button>
        </form>
    </div>
</body>
</html>


