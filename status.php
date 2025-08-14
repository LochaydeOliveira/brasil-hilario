<?php
/**
 * STATUS DO SISTEMA - Brasil Hilário
 * 
 * Verificação simples do status das funcionalidades
 */

// Verificar se arquivos essenciais existem
$files = [
    'includes/db.php' => 'Conexão com banco',
    'config/config.php' => 'Configurações',
    'includes/Logger.php' => 'Sistema de logs',
    'includes/CacheManager.php' => 'Sistema de cache',
    'includes/Validator.php' => 'Sistema de validação',
    'includes/session_init.php' => 'Sistema de sessão',
    '.env' => 'Arquivo de configuração'
];

$directories = [
    'cache' => 'Diretório de cache',
    'logs' => 'Diretório de logs', 
    'backups' => 'Diretório de backups'
];

$status = [];
$errors = [];

// Verificar arquivos
foreach ($files as $file => $description) {
    if (file_exists($file)) {
        $status[$description] = '✅ Existe';
    } else {
        $status[$description] = '❌ Não encontrado';
        $errors[] = "Arquivo {$file} não encontrado";
    }
}

// Verificar diretórios
foreach ($directories as $dir => $description) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            $status[$description] = '✅ Existe e tem permissão de escrita';
        } else {
            $status[$description] = '⚠️ Existe mas sem permissão de escrita';
            $errors[] = "Diretório {$dir} não tem permissão de escrita";
        }
    } else {
        $status[$description] = '❌ Não existe';
        $errors[] = "Diretório {$dir} não existe";
    }
}

// Teste simples de PHP
try {
    $phpVersion = phpversion();
    $status['Versão do PHP'] = "✅ {$phpVersion}";
} catch (Exception $e) {
    $status['Versão do PHP'] = '❌ Erro ao verificar';
    $errors[] = $e->getMessage();
}

// Teste de extensões PHP
$extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        $status["Extensão {$ext}"] = '✅ Carregada';
    } else {
        $status["Extensão {$ext}"] = '❌ Não carregada';
        $errors[] = "Extensão PHP {$ext} não está carregada";
    }
}

// Informações do servidor
$serverInfo = [
    'Servidor' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido',
    'Data/Hora' => date('Y-m-d H:i:s'),
    'Timezone' => date_default_timezone_get(),
    'Document Root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Desconhecido'
];

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status do Sistema - Brasil Hilário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0b8103, #0a6b02); min-height: 100vh; }
        .status-card { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .status-item { padding: 8px 12px; margin: 4px 0; border-radius: 5px; font-size: 14px; }
        .status-success { background: #d4edda; color: #155724; }
        .status-warning { background: #fff3cd; color: #856404; }
        .status-error { background: #f8d7da; color: #721c24; }
        .status-info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="status-card p-4">
                    <div class="text-center mb-4">
                        <h1><i class="fas fa-server"></i> Status do Sistema</h1>
                        <p class="text-muted">Brasil Hilário - Verificação de Configuração</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h4><i class="fas fa-list-check"></i> Verificações</h4>
                            <?php foreach ($status as $name => $result): ?>
                                <div class="status-item <?php 
                                    echo strpos($result, '✅') !== false ? 'status-success' : 
                                        (strpos($result, '⚠️') !== false ? 'status-warning' : 'status-error'); 
                                ?>">
                                    <strong><?php echo htmlspecialchars($name); ?>:</strong> 
                                    <?php echo htmlspecialchars($result); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="col-md-6">
                            <h4><i class="fas fa-info-circle"></i> Informações do Servidor</h4>
                            <?php foreach ($serverInfo as $name => $value): ?>
                                <div class="status-item status-info">
                                    <strong><?php echo htmlspecialchars($name); ?>:</strong> 
                                    <?php echo htmlspecialchars($value); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger mt-4">
                            <h5><i class="fas fa-exclamation-triangle"></i> Problemas Encontrados</h5>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-info mt-4">
                        <h5><i class="fas fa-lightbulb"></i> Próximos Passos</h5>
                        <ol class="mb-0">
                            <li>Se todos os itens estão ✅, o sistema está configurado corretamente</li>
                            <li>Se há ❌, execute o <a href="configurar_projeto.php">configurador</a></li>
                            <li>Se há ⚠️, verifique as permissões dos diretórios</li>
                            <li>Teste o <a href="admin/backup.php">sistema de backup</a></li>
                            <li>Teste a <a href="newsletter">newsletter</a></li>
                        </ol>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="<?php echo BLOG_URL ?? '/'; ?>" class="btn btn-primary">
                            <i class="fas fa-home"></i> Voltar ao Site
                        </a>
                        <a href="configurar_projeto.php" class="btn btn-success ms-2">
                            <i class="fas fa-cog"></i> Configurar Projeto
                        </a>
                        <a href="admin/backup.php" class="btn btn-info ms-2">
                            <i class="fas fa-database"></i> Sistema de Backup
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 