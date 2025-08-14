<?php
/**
 * TESTE DE CONFIGURAÇÃO - Brasil Hilário
 * 
 * Este arquivo testa se todas as configurações estão funcionando corretamente.
 * Acesse este arquivo no navegador para verificar o status.
 */

// Incluir configurações
require_once 'includes/db.php';
require_once 'config/config.php';
require_once 'includes/Logger.php';
require_once 'includes/CacheManager.php';
require_once 'includes/Validator.php';

// Inicializar sessão de forma segura
require_once 'includes/session_init.php';

$logger = new Logger();
$cache = new CacheManager();
$validator = new Validator();

$tests = [];
$errors = [];

// Teste 1: Conexão com banco de dados
try {
    $pdo->query('SELECT 1');
    $tests['database'] = '✅ Conexão com banco OK';
} catch (Exception $e) {
    $tests['database'] = '❌ Erro na conexão com banco';
    $errors[] = $e->getMessage();
}

// Teste 2: Sistema de logs
try {
    $logger->info('Teste de log', ['test' => 'configuracao']);
    $tests['logs'] = '✅ Sistema de logs OK';
} catch (Exception $e) {
    $tests['logs'] = '❌ Erro no sistema de logs';
    $errors[] = $e->getMessage();
}

// Teste 3: Sistema de cache
try {
    $cache->set('test_key', 'test_value', 60);
    $value = $cache->get('test_key');
    if ($value === 'test_value') {
        $tests['cache'] = '✅ Sistema de cache OK';
    } else {
        $tests['cache'] = '❌ Erro no sistema de cache';
        $errors[] = 'Cache não está funcionando corretamente';
    }
} catch (Exception $e) {
    $tests['cache'] = '❌ Erro no sistema de cache';
    $errors[] = $e->getMessage();
}

// Teste 4: Sistema de validação
try {
    $validator->setData(['email' => 'test@example.com']);
    $validator->email('email');
    if (!$validator->hasErrors()) {
        $tests['validation'] = '✅ Sistema de validação OK';
    } else {
        $tests['validation'] = '❌ Erro no sistema de validação';
        $errors[] = 'Validação não está funcionando corretamente';
    }
} catch (Exception $e) {
    $tests['validation'] = '❌ Erro no sistema de validação';
    $errors[] = $e->getMessage();
}

// Teste 5: Sessão
try {
    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['csrf_token'])) {
        $tests['session'] = '✅ Sistema de sessão OK';
    } else {
        $tests['session'] = '❌ Erro no sistema de sessão';
        $errors[] = 'Sessão não está funcionando corretamente';
    }
} catch (Exception $e) {
    $tests['session'] = '❌ Erro no sistema de sessão';
    $errors[] = $e->getMessage();
}

// Teste 6: Diretórios
$directories = ['cache', 'logs', 'backups'];
foreach ($directories as $dir) {
    if (is_dir(__DIR__ . '/' . $dir) && is_writable(__DIR__ . '/' . $dir)) {
        $tests['directories'] = '✅ Diretórios criados e com permissão de escrita';
    } else {
        $tests['directories'] = '❌ Problema com diretórios';
        $errors[] = "Diretório {$dir} não existe ou não tem permissão de escrita";
    }
}

// Teste 7: Arquivo .env
if (file_exists(__DIR__ . '/.env')) {
    $tests['env'] = '✅ Arquivo .env existe';
} else {
    $tests['env'] = '❌ Arquivo .env não encontrado';
    $errors[] = 'Arquivo .env não foi criado';
}

// Log do teste
$logger->info('Teste de configuração executado', [
    'tests' => $tests,
    'errors' => $errors,
    'session_id' => session_id()
]);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Configuração - Brasil Hilário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0b8103, #0a6b02); min-height: 100vh; }
        .test-card { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .test-item { padding: 10px; margin: 5px 0; border-radius: 5px; }
        .test-success { background: #d4edda; color: #155724; }
        .test-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="test-card p-4">
                    <div class="text-center mb-4">
                        <h1><i class="fas fa-cogs"></i> Teste de Configuração</h1>
                        <p class="text-muted">Verificando se todas as funcionalidades estão funcionando</p>
                    </div>
                    
                    <div class="mb-4">
                        <h4><i class="fas fa-list-check"></i> Resultados dos Testes</h4>
                        <?php foreach ($tests as $name => $result): ?>
                            <div class="test-item <?php echo strpos($result, '✅') !== false ? 'test-success' : 'test-error'; ?>">
                                <strong><?php echo ucfirst($name); ?>:</strong> <?php echo $result; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Erros Encontrados</h5>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Informações do Sistema</h5>
                        <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
                        <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                        <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
                        <p><strong>Timestamp:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                    </div>
                    
                    <div class="text-center">
                        <a href="<?php echo BLOG_URL; ?>" class="btn btn-primary">
                            <i class="fas fa-home"></i> Voltar ao Site
                        </a>
                        <a href="configurar_projeto.php" class="btn btn-success ms-2">
                            <i class="fas fa-cog"></i> Configurar Projeto
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 