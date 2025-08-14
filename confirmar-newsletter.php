<?php
require_once 'includes/db.php';
require_once 'config/config.php';
require_once 'includes/Logger.php';
require_once 'includes/NewsletterManager.php';

$logger = new Logger();
$newsletter = new NewsletterManager($pdo);

$message = '';
$messageType = '';

// Processar confirmação
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $result = $newsletter->confirmSubscription($token);
    
    if ($result['success']) {
        $message = $result['message'];
        $messageType = 'success';
        
        $logger->info('Newsletter confirmada via token', ['token' => $token]);
    } else {
        $message = $result['error'];
        $messageType = 'danger';
        
        $logger->warning('Tentativa de confirmação inválida', ['token' => $token]);
    }
} else {
    $message = 'Token de confirmação não fornecido.';
    $messageType = 'danger';
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> text-white">
                    <h3><i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i> 
                        Confirmação da Newsletter
                    </h3>
                </div>
                <div class="card-body text-center">
                    <?php if ($messageType === 'success'): ?>
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="text-success">Inscrição Confirmada!</h4>
                        <p class="lead">Parabéns! Sua inscrição na newsletter do Brasil Hilário foi confirmada com sucesso.</p>
                        
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> O que acontece agora?</h5>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-envelope text-primary"></i> Você receberá nossos emails</li>
                                <li><i class="fas fa-bell text-warning"></i> Fique atento às novidades</li>
                                <li><i class="fas fa-heart text-danger"></i> Conteúdo exclusivo para você</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <a href="<?php echo BLOG_URL; ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-home"></i> Voltar ao Site
                            </a>
                        </div>
                        
                    <?php else: ?>
                        <div class="mb-4">
                            <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="text-danger">Erro na Confirmação</h4>
                        <p class="lead"><?php echo htmlspecialchars($message); ?></p>
                        
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-question-circle"></i> O que fazer?</h5>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-envelope text-primary"></i> Verifique seu email</li>
                                <li><i class="fas fa-link text-info"></i> Use o link correto</li>
                                <li><i class="fas fa-redo text-warning"></i> Tente se inscrever novamente</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <a href="<?php echo BLOG_URL; ?>/newsletter" class="btn btn-success btn-lg">
                                <i class="fas fa-envelope"></i> Inscrever-se Novamente
                            </a>
                            <a href="<?php echo BLOG_URL; ?>" class="btn btn-outline-primary btn-lg ms-2">
                                <i class="fas fa-home"></i> Voltar ao Site
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($messageType === 'success'): ?>
                <div class="card mt-4">
                    <div class="card-body">
                        <h5><i class="fas fa-lightbulb"></i> Dica</h5>
                        <p class="text-muted">
                            Adicione <strong>noreply@brasilhilario.com.br</strong> aos seus contatos 
                            para garantir que nossos emails não vão para a pasta de spam.
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 