<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';

// Verificar login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Executar Posts Específicos';

// Executar o SQL
try {
    $sql_file = file_get_contents('../sql/adicionar_posts_especificos_grupos.sql');
    $statements = explode(';', $sql_file);
    
    $success_count = 0;
    $error_count = 0;
    $errors = [];
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        try {
            $stmt = $pdo->prepare($statement);
            $stmt->execute();
            $success_count++;
        } catch (Exception $e) {
            $error_count++;
            $errors[] = "Erro na execução: " . $e->getMessage() . " (SQL: " . substr($statement, 0, 100) . "...)";
        }
    }
    
    if ($error_count === 0) {
        $mensagem = "Funcionalidade de posts específicos adicionada com sucesso! {$success_count} comandos executados.";
        $tipo_mensagem = 'success';
    } else {
        $mensagem = "Executado com {$error_count} erros. {$success_count} comandos executados com sucesso.";
        $tipo_mensagem = 'warning';
    }
    
} catch (Exception $e) {
    $mensagem = "Erro ao executar SQL: " . $e->getMessage();
    $tipo_mensagem = 'danger';
    $errors = [$e->getMessage()];
}

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Executar Posts Específicos</h1>
    <a href="grupos-anuncios.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<?php if (isset($mensagem)): ?>
    <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
        <?php echo $mensagem; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Resultado da Execução</h5>
    </div>
    <div class="card-body">
        <h6>Funcionalidades Adicionadas:</h6>
        <ul>
            <li>✅ Nova tabela <code>grupos_anuncios_posts</code> para associar grupos a posts específicos</li>
            <li>✅ Campo <code>posts_especificos</code> na tabela <code>grupos_anuncios</code></li>
            <li>✅ Campo <code>aparecer_inicio</code> na tabela <code>grupos_anuncios</code></li>
            <li>✅ Índices para performance</li>
            <li>✅ Interface atualizada no painel admin</li>
            <li>✅ Lógica de filtro no frontend</li>
        </ul>
        
        <h6 class="mt-4">Como Usar:</h6>
        <ol>
            <li>Vá para <strong>Grupos de Anúncios</strong></li>
            <li>Edite um grupo existente ou crie um novo</li>
            <li>Marque <strong>"Posts específicos"</strong> se quiser limitar a posts específicos</li>
            <li>Selecione os posts onde o grupo deve aparecer</li>
            <li>Configure se deve aparecer na página inicial</li>
            <li>Salve as configurações</li>
        </ol>
        
        <?php if (!empty($errors)): ?>
            <div class="mt-4">
                <h6 class="text-danger">Erros Encontrados:</h6>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-4">
    <a href="grupos-anuncios.php" class="btn btn-primary">
        <i class="fas fa-list"></i> Ver Grupos de Anúncios
    </a>
    <a href="index.php" class="btn btn-outline-secondary">
        <i class="fas fa-home"></i> Voltar ao Painel
    </a>
</div>

<?php include 'includes/footer.php'; ?> 