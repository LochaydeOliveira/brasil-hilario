<?php
require_once __DIR__ . '/conexao.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    if ($email === '' || $senha === '') {
        $erro = 'Informe e-mail e senha.';
    } else {
        $stmt = $pdo->prepare('SELECT id, nome, email, senha, ativo FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && (int)$user['ativo'] === 1 && password_verify($senha, $user['senha'])) {
            // Evitar warnings de header: limpe buffers e regenere sessão
            if (ob_get_level()) { @ob_end_clean(); }
            @session_write_close();
            @session_start();
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];
            header('Location: dashboard.php');
            exit;
        } else {
            $erro = 'Credenciais inválidas ou usuário suspenso.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | E-book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f6f7fb}
        .card{max-width:420px;width:100%;box-shadow:0 10px 30px rgba(0,0,0,.06)}
        .brand{font-weight:800;color:#d2691e}
    </style>
    </head>
<body>
    <div class="card p-4 border-0">
        <div class="text-center mb-3">
            <div class="brand">Redescobrindo o Desejo</div>
            <small class="text-muted">Acesso ao e-book</small>
        </div>
        <?php if ($erro): ?>
            <div class="alert alert-danger py-2"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>
        <form method="post" novalidate>
            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control form-control-lg" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control form-control-lg" required>
            </div>
            <button class="btn btn-primary btn-lg w-100" style="background:#d2691e;border-color:#d2691e">Entrar</button>
        </form>
        <div class="text-center mt-3 small">
            <?php if (is_admin()): ?>
                <a href="admin/" class="link-secondary">Painel Administrativo</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>


