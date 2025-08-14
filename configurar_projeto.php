<?php
/**
 * CONFIGURADOR SIMPLES DO BRASIL HILÁRIO
 * 
 * Este arquivo vai te ajudar a configurar o projeto de forma simples.
 * Basta acessar este arquivo no navegador e seguir as instruções.
 */

// Verificar se já existe arquivo .env
$envExists = file_exists(__DIR__ . '/.env');
$configExists = file_exists(__DIR__ . '/config/config.php');

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = true;
    $message = '';
    
    try {
        // Criar arquivo .env
        $envContent = "# Configurações do Banco de Dados\n";
        $envContent .= "DB_HOST_LOCAL=" . $_POST['db_host'] . "\n";
        $envContent .= "DB_HOST_IP=" . $_POST['db_host_ip'] . "\n";
        $envContent .= "DB_NAME=" . $_POST['db_name'] . "\n";
        $envContent .= "DB_USER=" . $_POST['db_user'] . "\n";
        $envContent .= "DB_PASS=" . $_POST['db_pass'] . "\n\n";
        $envContent .= "# Configurações do Site\n";
        $envContent .= "BLOG_URL=" . $_POST['blog_url'] . "\n";
        $envContent .= "ADMIN_EMAIL=" . $_POST['admin_email'] . "\n\n";
        $envContent .= "# Configurações de Segurança\n";
        $envContent .= "SECURE_AUTH_KEY=" . bin2hex(random_bytes(32)) . "\n\n";
        $envContent .= "# Configurações de Cache\n";
        $envContent .= "CACHE_ENABLED=true\n";
        $envContent .= "CACHE_TIME=3600\n\n";
        $envContent .= "# Configurações de Upload\n";
        $envContent .= "UPLOAD_MAX_SIZE=5242880\n";
        
        file_put_contents(__DIR__ . '/.env', $envContent);
        
        // Criar diretórios necessários
        $directories = ['cache', 'logs', 'backups'];
        foreach ($directories as $dir) {
            if (!is_dir(__DIR__ . '/' . $dir)) {
                mkdir(__DIR__ . '/' . $dir, 0755, true);
            }
        }
        
        // Testar conexão com banco
        $dsn = "mysql:host=" . $_POST['db_host'] . ";dbname=" . $_POST['db_name'] . ";charset=utf8mb4";
        $pdo = new PDO($dsn, $_POST['db_user'], $_POST['db_pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $message = "✅ Configuração realizada com sucesso!<br>";
        $message .= "✅ Arquivo .env criado<br>";
        $message .= "✅ Diretórios criados<br>";
        $message .= "✅ Conexão com banco testada<br>";
        $message .= "<br><strong>IMPORTANTE:</strong> Delete este arquivo (configurar_projeto.php) por segurança!";
        
    } catch (Exception $e) {
        $success = false;
        $message = "❌ Erro na configuração: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Brasil Hilário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0b8103, #0a6b02); min-height: 100vh; }
        .config-card { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .step { background: #f8f9fa; border-radius: 10px; padding: 20px; margin: 10px 0; }
        .step-number { background: #0b8103; color: white; width: 30px; height: 30px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="config-card p-4">
                    <div class="text-center mb-4">
                        <h1><i class="fas fa-cog"></i> Configurar Brasil Hilário</h1>
                        <p class="text-muted">Configure seu projeto de forma simples e segura</p>
                    </div>
                    
                    <?php if (isset($message)): ?>
                        <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!$envExists): ?>
                        <form method="POST">
                            <div class="step">
                                <h5><span class="step-number">1</span>Configurações do Banco de Dados</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Host Local</label>
                                        <input type="text" name="db_host" class="form-control" value="localhost" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Host IP</label>
                                        <input type="text" name="db_host_ip" class="form-control" value="192.185.222.27" required>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label class="form-label">Nome do Banco</label>
                                        <input type="text" name="db_name" class="form-control" value="paymen58_brasil_hilario" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Usuário</label>
                                        <input type="text" name="db_user" class="form-control" value="paymen58" required>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <label class="form-label">Senha</label>
                                    <input type="password" name="db_pass" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="step">
                                <h5><span class="step-number">2</span>Configurações do Site</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">URL do Site</label>
                                        <input type="url" name="blog_url" class="form-control" value="https://www.brasilhilario.com.br" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email do Admin</label>
                                        <input type="email" name="admin_email" class="form-control" value="admin@brasilhilario.com.br" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save"></i> Salvar Configuração
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <h5><i class="fas fa-check-circle"></i> Projeto já configurado!</h5>
                            <p>O arquivo .env já existe. Se precisar reconfigurar, delete o arquivo .env e recarregue esta página.</p>
                        </div>
                        
                        <div class="step">
                            <h5><span class="step-number">3</span>Próximos Passos</h5>
                            <ol>
                                <li><strong>Delete este arquivo</strong> (configurar_projeto.php) por segurança</li>
                                <li>Acesse o painel admin: <code>/admin/</code></li>
                                <li>Teste o sistema de backup: <code>/admin/backup.php</code></li>
                                <li>Verifique os logs em: <code>/logs/app.log</code></li>
                            </ol>
                        </div>
                    <?php endif; ?>
                    
                    <div class="step">
                        <h5><span class="step-number">4</span>Funcionalidades Disponíveis</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-shield-alt text-success"></i> Segurança aprimorada</li>
                                    <li><i class="fas fa-database text-info"></i> Sistema de backup</li>
                                    <li><i class="fas fa-envelope text-warning"></i> Newsletter</li>
                                    <li><i class="fas fa-tachometer-alt text-primary"></i> Cache inteligente</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-clipboard-check text-success"></i> Validação de dados</li>
                                    <li><i class="fas fa-file-alt text-info"></i> Logs estruturados</li>
                                    <li><i class="fas fa-chart-line text-warning"></i> Monitoramento</li>
                                    <li><i class="fas fa-cogs text-primary"></i> Configurações centralizadas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 