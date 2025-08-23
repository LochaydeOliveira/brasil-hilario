<?php
require_once __DIR__ . '/../conexao.php';
require_admin();

$hash = '';
$senhaVisivel = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha = (string)($_POST['senha'] ?? '');
    if ($senha !== '') {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        // nunca exibimos a senha em produção, mantemos apenas para cópia local
        $senhaVisivel = $senha;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerador de Hash de Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#f6f7fb}
        .card{max-width:680px;margin:40px auto;box-shadow:0 10px 30px rgba(0,0,0,.06)}
        .brand{font-weight:800;color:#d2691e}
        code{user-select:all}
    </style>
</head>
<body>
    <div class="card p-4 border-0">
        <div class="mb-3">
            <div class="brand">Gerador de Hash (password_hash)</div>
            <small class="text-muted">Use o hash no INSERT da tabela <strong>usuarios</strong>.</small>
        </div>

        <form method="post" class="row g-2">
            <div class="col-12 col-md-8">
                <label class="form-label">Digite a senha para gerar o hash</label>
                <input type="text" name="senha" class="form-control" placeholder="SUA_SENHA_FORTE" required>
            </div>
            <div class="col-12 col-md-4 d-flex align-items-end">
                <button class="btn btn-primary w-100" style="background:#d2691e;border-color:#d2691e">Gerar hash</button>
            </div>
        </form>

        <?php if ($hash): ?>
        <hr>
        <div class="mb-2"><strong>Hash gerado:</strong></div>
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="hashOut" value="<?php echo htmlspecialchars($hash); ?>" readonly>
            <button class="btn btn-outline-secondary" type="button" onclick="copyHash()">Copiar</button>
        </div>
        <div class="alert alert-secondary small">
            Exemplo de INSERT:<br>
            <code>INSERT INTO usuarios (nome, email, senha, ativo) VALUES ('NOME', 'EMAIL', '<?php echo htmlspecialchars($hash); ?>', 1);</code>
        </div>
        <?php endif; ?>

        <div class="mt-2">
            <a class="btn btn-light" href="../admin/">Voltar ao Admin</a>
        </div>
    </div>

    <script>
        function copyHash(){
            const el = document.getElementById('hashOut');
            el.select();
            el.setSelectionRange(0, 99999);
            document.execCommand('copy');
        }
    </script>
</body>
</html>


