<?php
require_once '../config/database.php';
require_once '../includes/VisualConfigManager.php';

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Usar a conexão $pdo já estabelecida em database.php
        if (!isset($pdo)) {
            throw new Exception("Conexão com banco de dados não disponível");
        }
        
        // Ler e executar o arquivo SQL
        $sqlFile = '../sql/sistema_configuracoes_visuais_completo.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            
            // Dividir o SQL em comandos individuais
            $commands = array_filter(array_map('trim', explode(';', $sql)));
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            
            foreach ($commands as $command) {
                if (!empty($command)) {
                    try {
                        $pdo->exec($command);
                        $successCount++;
                    } catch (PDOException $e) {
                        $errorCount++;
                        $errors[] = $e->getMessage();
                    }
                }
            }
            
            // Gerar CSS dinâmico
            $visualManager = new VisualConfigManager($pdo);
            $cssGenerated = $visualManager->saveCSS();
            
            $message = "✅ <strong>Configurações aplicadas com sucesso!</strong><br>";
            $message .= "📊 <strong>Estatísticas:</strong><br>";
            $message .= "• Comandos SQL executados: <strong>{$successCount}</strong><br>";
            $message .= "• Erros encontrados: <strong>{$errorCount}</strong><br>";
            $message .= "• CSS gerado: <strong>" . ($cssGenerated ? 'Sim' : 'Não') . "</strong><br><br>";
            
            if ($errorCount > 0) {
                $message .= "⚠️ <strong>Erros encontrados:</strong><br>";
                foreach (array_slice($errors, 0, 5) as $error) {
                    $message .= "• " . htmlspecialchars($error) . "<br>";
                }
                if (count($errors) > 5) {
                    $message .= "• ... e mais " . (count($errors) - 5) . " erros<br>";
                }
            }
            
            $message .= "<br>🎨 <strong>Próximos passos:</strong><br>";
            $message .= "1. Acesse o Painel Admin → Configurações Visuais<br>";
            $message .= "2. Configure as fontes, cores e tamanhos desejados<br>";
            $message .= "3. Salve as alterações para aplicar ao site<br>";
            $message .= "4. Teste em diferentes dispositivos";
            
            $messageType = 'success';
        } else {
            $message = "❌ <strong>Erro:</strong> Arquivo SQL não encontrado!";
            $messageType = 'danger';
        }
        
    } catch (Exception $e) {
        $message = "❌ <strong>Erro:</strong> " . htmlspecialchars($e->getMessage());
        $messageType = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Completo de Configurações Visuais - Brasil Hilário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #0b8103 0%, #0a6b02 100%);
            color: white;
            padding: 3rem 0;
        }
        .feature-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .icon-large {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fas fa-palette me-3"></i>
                Sistema Completo de Configurações Visuais
            </h1>
            <p class="lead mb-0">Controle total sobre fontes, cores, tamanhos e responsividade do seu site</p>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Card Principal -->
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-magic me-2"></i>
                            Aplicar Configurações Completas
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if (isset($message)): ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Formulário de Execução -->
                        <form method="POST" class="text-center">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle me-2"></i>O que será configurado?</h5>
                                <p class="mb-0">Este sistema criará configurações para <strong>todas as seções</strong> do seu site, incluindo:</p>
                            </div>
                            
                            <button type="submit" class="btn btn-success btn-lg px-5 py-3">
                                <i class="fas fa-rocket me-2"></i>
                                APLICAR CONFIGURAÇÕES COMPLETAS
                            </button>
                        </form>

                        <!-- Seções Configuráveis -->
                        <div class="row mt-5">
                            <div class="col-md-6 mb-4">
                                <div class="card feature-card h-100">
                                    <div class="card-body text-center">
                                        <div class="icon-large text-primary">
                                            <i class="fas fa-heading"></i>
                                        </div>
                                        <h5 class="card-title">Header & Navegação</h5>
                                        <p class="card-text">Título do site, logo, navbar com categorias, cores e fontes personalizáveis.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="card feature-card h-100">
                                    <div class="card-body text-center">
                                        <div class="icon-large text-success">
                                            <i class="fas fa-newspaper"></i>
                                        </div>
                                        <h5 class="card-title">Conteúdo Principal</h5>
                                        <p class="card-text">Títulos e parágrafos dos posts, meta informações, responsividade completa.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="card feature-card h-100">
                                    <div class="card-body text-center">
                                        <div class="icon-large text-warning">
                                            <i class="fas fa-columns"></i>
                                        </div>
                                        <h5 class="card-title">Sidebar</h5>
                                        <p class="card-text">Títulos das seções, cards, links, fontes e cores personalizáveis.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="card feature-card h-100">
                                    <div class="card-body text-center">
                                        <div class="icon-large text-info">
                                            <i class="fas fa-link"></i>
                                        </div>
                                        <h5 class="card-title">Leia Também & Últimas</h5>
                                        <p class="card-text">Seções específicas com controle total sobre fontes, tamanhos e pesos.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="card feature-card h-100">
                                    <div class="card-body text-center">
                                        <div class="icon-large text-danger">
                                            <i class="fas fa-palette"></i>
                                        </div>
                                        <h5 class="card-title">Elementos Visuais</h5>
                                        <p class="card-text">Cards, botões, badges, formulários, paginação e anúncios.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="card feature-card h-100">
                                    <div class="card-body text-center">
                                        <div class="icon-large text-secondary">
                                            <i class="fas fa-mobile-alt"></i>
                                        </div>
                                        <h5 class="card-title">Responsividade</h5>
                                        <p class="card-text">Breakpoints, espaçamentos, adaptação para mobile e tablet.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instruções -->
                        <div class="alert alert-success mt-4">
                            <h6><i class="fas fa-lightbulb me-2"></i>Como usar:</h6>
                            <ol class="mb-0">
                                <li>Clique no botão acima para aplicar as configurações</li>
                                <li>Acesse <strong>Painel Admin → Configurações Visuais</strong></li>
                                <li>Configure cada seção conforme sua preferência</li>
                                <li>Salve as alterações para aplicar ao site</li>
                                <li>Teste em diferentes dispositivos</li>
                            </ol>
                        </div>

                        <!-- Voltar ao Admin -->
                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Voltar ao Painel Admin
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 