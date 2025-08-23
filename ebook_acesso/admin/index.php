<?php
require_once __DIR__ . '/../conexao.php';
require_admin();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        if ($nome && filter_var($email, FILTER_VALIDATE_EMAIL) && $senha) {
            try { $hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha, ativo) VALUES (?, ?, ?, ?)');
                $stmt->execute([$nome, $email, $hash, $ativo]);
                $msg = 'Usuário criado.'; } catch (Throwable $e) { $msg = 'Erro ao criar (e-mail pode já existir).'; }
        } else { $msg = 'Preencha todos os campos corretamente.'; }
    }
    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        if ($id && $nome && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                if ($senha !== '') { $hash = password_hash($senha, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare('UPDATE usuarios SET nome=?, email=?, senha=?, ativo=? WHERE id=?');
                    $stmt->execute([$nome, $email, $hash, $ativo, $id]);
                } else { $stmt = $pdo->prepare('UPDATE usuarios SET nome=?, email=?, ativo=? WHERE id=?');
                    $stmt->execute([$nome, $email, $ativo, $id]); }
                $msg = 'Usuário atualizado.'; } catch (Throwable $e) { $msg = 'Erro ao atualizar (e-mail pode já existir).'; }
        } else { $msg = 'Dados inválidos.'; }
    }
    if ($action === 'suspend') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) { $stmt = $pdo->prepare('UPDATE usuarios SET ativo = 0 WHERE id=?'); $stmt->execute([$id]); $msg = 'Usuário suspenso.'; }
    }
}

$busca = trim($_GET['q'] ?? '');
if ($busca) { $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nome LIKE ? OR email LIKE ? ORDER BY criado_em DESC"); $like='%'.$busca.'%'; $stmt->execute([$like,$like]); }
else { $stmt = $pdo->query('SELECT * FROM usuarios ORDER BY criado_em DESC'); }
$usuarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>.topbar{background:#d2691e}.topbar .navbar-brand{color:#fff;font-weight:700}.btn-primary{background:#d2691e;border-color:#d2691e}@media (max-width:768px){.table-responsive{font-size:.95rem}}</style>
</head>
<body>
    <nav class="navbar topbar"><div class="container"><span class="navbar-brand">Painel Admin</span><div class="ms-auto d-flex gap-2"><a class="btn btn-light btn-sm" href="../dashboard.php">Área restrita</a><a class="btn btn-outline-light btn-sm" href="../logout.php">Sair</a></div></div></nav>
    <div class="container my-4">
        <?php if($msg): ?><div class="alert alert-info py-2"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
        <div class="card mb-4"><div class="card-header bg-white"><h5 class="mb-0">Novo Usuário</h5></div><div class="card-body">
            <form method="post" class="row g-2"><input type="hidden" name="action" value="create">
                <div class="col-md-3"><input type="text" name="nome" class="form-control" placeholder="Nome" required></div>
                <div class="col-md-3"><input type="email" name="email" class="form-control" placeholder="E-mail" required></div>
                <div class="col-md-3"><input type="password" name="senha" class="form-control" placeholder="Senha" required></div>
                <div class="col-md-2 d-flex align-items-center"><div class="form-check"><input class="form-check-input" type="checkbox" name="ativo" id="ativo" checked><label class="form-check-label" for="ativo">Ativo</label></div></div>
                <div class="col-md-1"><button class="btn btn-primary w-100">Salvar</button></div>
            </form></div></div>
        <div class="d-flex justify-content-between align-items-center mb-2"><h5 class="mb-0">Usuários</h5><form class="d-flex" method="get" role="search"><input class="form-control" type="search" placeholder="Buscar" name="q" value="<?php echo htmlspecialchars($busca); ?>"></form></div>
        <div class="table-responsive border rounded">
            <table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Status</th><th>Criado em</th><th style="width:220px">Ações</th></tr></thead><tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?php echo (int)$u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['nome']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo ((int)$u['ativo']===1?'<span class="badge bg-success">Ativo</span>':'<span class="badge bg-secondary">Suspenso</span>'); ?></td>
                    <td><small class="text-muted"><?php echo htmlspecialchars($u['criado_em']); ?></small></td>
                    <td>
                        <form method="post" class="row g-1">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                            <div class="col-md-2 col-3"><input type="text" class="form-control form-control-sm" name="nome" value="<?php echo htmlspecialchars($u['nome']); ?>" placeholder="Nome"></div>
                            <div class="col-md-3 col-9"><input type="email" class="form-control form-control-sm" name="email" value="<?php echo htmlspecialchars($u['email']); ?>" placeholder="E-mail"></div>
                            <div class="col-md-3 col-6"><input type="password" class="form-control form-control-sm" name="senha" placeholder="Nova senha (opcional)"></div>
                            <div class="col-md-2 col-6 d-flex align-items-center"><div class="form-check"><input class="form-check-input" type="checkbox" name="ativo" id="ativo_<?php echo (int)$u['id']; ?>" <?php echo ((int)$u['ativo']===1?'checked':''); ?>><label class="form-check-label" for="ativo_<?php echo (int)$u['id']; ?>">Ativo</label></div></div>
                            <div class="col-md-2 col-12 d-flex gap-1"><button class="btn btn-sm btn-primary w-100">Atualizar</button><button class="btn btn-sm btn-outline-secondary w-100" name="action" value="suspend">Suspender</button></div>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($usuarios)): ?><tr><td colspan="6" class="text-center py-4 text-muted">Nenhum usuário encontrado.</td></tr><?php endif; ?>
            </tbody></table>
        </div>
    </div>
</body>
</html>


