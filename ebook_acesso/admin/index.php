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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .topbar{background:#d2691e}
        .topbar .navbar-brand{color:#fff;font-weight:700}
        .btn-primary{background:#d2691e;border-color:#d2691e}
        .admin-wrap{width:90%;max-width:1600px;margin-inline:auto}
        .card{box-shadow:0 10px 24px rgba(0,0,0,.06);border:1px solid #f0e7dd}
        .card-header h5{font-weight:700}
        .top-form .form-label{font-weight:600;font-size:.9rem}
        .top-form .form-control,.top-form .form-check-input{height:42px}
        .top-form .btn{height:42px}
        .table-responsive{overflow-x:auto}
        .table-users th,.table-users td{vertical-align:middle; white-space:nowrap}
        .table-users .actions{min-width:180px}
        .table-users input.form-control-sm{min-width:140px}
        .badge{letter-spacing:.2px}
        @media (max-width: 992px){
            .table-users th,.table-users td{white-space:normal}
            .table-users .actions{min-width:unset}
        }
    </style>
</head>
<body>
    <nav class="navbar topbar"><div class="container"><span class="navbar-brand">Painel Admin</span><div class="ms-auto d-flex gap-2"><a class="btn btn-light btn-sm" href="../dashboard.php">Área restrita</a><a class="btn btn-outline-light btn-sm" href="../logout.php">Sair</a></div></div></nav>
    <div class="container admin-wrap my-4">
        <?php if($msg): ?><div class="alert alert-info py-2"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
        <div class="card mb-4 top-form"><div class="card-header bg-white"><h5 class="mb-0">Novo Usuário</h5></div><div class="card-body">
            <form method="post" class="row g-3 align-items-end"><input type="hidden" name="action" value="create">
                <div class="col-12 col-md-3"><label class="form-label">Nome</label><input type="text" name="nome" class="form-control" placeholder="Ex.: Maria Silva" required></div>
                <div class="col-12 col-md-4"><label class="form-label">E-mail</label><input type="email" name="email" class="form-control" placeholder="seu@email.com" required></div>
                <div class="col-12 col-md-3"><label class="form-label">Senha</label><input type="password" name="senha" class="form-control" placeholder="Senha" required></div>
                <div class="col-6 col-md-1"><div class="form-check"><input class="form-check-input" type="checkbox" name="ativo" id="ativo" checked><label class="form-check-label" for="ativo">Ativo</label></div></div>
                <div class="col-6 col-md-1"><button class="btn btn-primary w-100">Salvar</button></div>
            </form></div></div>
        <div class="d-flex justify-content-between align-items-center mb-2"><h5 class="mb-0">Usuários</h5><form class="d-flex" method="get" role="search"><input class="form-control" type="search" placeholder="Buscar" name="q" value="<?php echo htmlspecialchars($busca); ?>"></form></div>
        <div class="table-responsive border rounded">
            <table class="table table-hover table-striped align-middle mb-0 table-users"><thead class="table-light"><tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Status</th><th>Criado em</th><th style="width:180px">Ações</th></tr></thead><tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?php echo (int)$u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['nome']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo ((int)$u['ativo']===1?'<span class="badge bg-success">Ativo</span>':'<span class="badge bg-secondary">Suspenso</span>'); ?></td>
                    <td><small class="text-muted"><?php echo htmlspecialchars($u['criado_em']); ?></small></td>
                    <td class="actions">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal_<?php echo (int)$u['id']; ?>">Editar</button>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                            <button class="btn btn-sm btn-outline-secondary" name="action" value="suspend">Suspender</button>
                        </form>
                    </td>
                </tr>
                <!-- Modal de edição -->
                <div class="modal fade" id="editUserModal_<?php echo (int)$u['id']; ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Editar usuário #<?php echo (int)$u['id']; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <form method="post">
                        <div class="modal-body">
                          <input type="hidden" name="action" value="update">
                          <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                          <div class="row g-3">
                            <div class="col-md-6">
                              <label class="form-label">Nome</label>
                              <input type="text" class="form-control" name="nome" value="<?php echo htmlspecialchars($u['nome']); ?>" required>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">E-mail</label>
                              <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($u['email']); ?>" required>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Nova senha (opcional)</label>
                              <input type="password" class="form-control" name="senha" placeholder="Deixe em branco para manter">
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                              <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="ativo" id="ativo_m_<?php echo (int)$u['id']; ?>" <?php echo ((int)$u['ativo']===1?'checked':''); ?>>
                                <label class="form-check-label" for="ativo_m_<?php echo (int)$u['id']; ?>">Ativo</label>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                          <button class="btn btn-primary">Salvar alterações</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($usuarios)): ?><tr><td colspan="6" class="text-center py-4 text-muted">Nenhum usuário encontrado.</td></tr><?php endif; ?>
            </tbody></table>
        </div>
    </div>
</body>
</html>


