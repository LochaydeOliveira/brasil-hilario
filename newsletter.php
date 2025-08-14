<?php
require_once 'includes/db.php';
require_once 'config/config.php';
require_once 'includes/Logger.php';
require_once 'includes/NewsletterManager.php';

$logger = new Logger();
$newsletter = new NewsletterManager($pdo);

$message = '';
$messageType = '';

// Processar inscrição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $nome = $_POST['nome'] ?? '';
    
    $result = $newsletter->subscribe($email, $nome);
    
    if ($result['success']) {
        $message = $result['message'];
        $messageType = 'success';
    } else {
        $message = $result['error'];
        $messageType = 'danger';
    }
}

include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3><i class="fas fa-envelope"></i> Newsletter Brasil Hilário</h3>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Fique por dentro das novidades!</h4>
                            <p>Inscreva-se na nossa newsletter e receba:</p>
                            <ul>
                                <li><i class="fas fa-check text-success"></i> Novos posts em primeira mão</li>
                                <li><i class="fas fa-check text-success"></i> Conteúdo exclusivo</li>
                                <li><i class="fas fa-check text-success"></i> Dicas e curiosidades</li>
                                <li><i class="fas fa-check text-success"></i> Promoções especiais</li>
                            </ul>
                            
                            <div class="mt-4">
                                <h5>Por que se inscrever?</h5>
                                <p class="text-muted">
                                    O Brasil Hilário é o seu portal de humor e entretenimento. 
                                    Com nossa newsletter, você nunca perde uma piada, meme ou 
                                    notícia engraçada!
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <form method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" 
                                           placeholder="Seu nome" required>
                                    <div class="invalid-feedback">
                                        Por favor, informe seu nome.
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="seu@email.com" required>
                                    <div class="invalid-feedback">
                                        Por favor, informe um email válido.
                                    </div>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="termos" required>
                                    <label class="form-check-label" for="termos">
                                        Concordo em receber emails da newsletter
                                    </label>
                                    <div class="invalid-feedback">
                                        Você deve concordar antes de se inscrever.
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-paper-plane"></i> Inscrever-se
                                </button>
                            </form>
                            
                            <div class="mt-3 text-center">
                                <small class="text-muted">
                                    Não se preocupe, você pode cancelar a qualquer momento.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h5><i class="fas fa-shield-alt"></i> Sua privacidade é importante</h5>
                    <p class="text-muted">
                        • Seus dados são protegidos e nunca serão compartilhados<br>
                        • Você pode cancelar a inscrição a qualquer momento<br>
                        • Enviamos apenas conteúdo relevante e de qualidade<br>
                        • Respeitamos a LGPD e suas diretrizes
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validação do formulário
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>

<?php include 'includes/footer.php'; ?> 