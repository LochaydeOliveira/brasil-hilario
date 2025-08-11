<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../includes/db.php';
require_once '../includes/VisualConfigManager.php';
require_once 'includes/auth.php';

// Verificar se o usuário está logado
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$visualConfig = new VisualConfigManager($pdo);
$mensagem = '';
$tipo_mensagem = 'success';

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Configurações Completas de Fontes - Brasil Hilário</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
</head>
<body class='bg-light'>
    <div class='container mt-4'>
        <div class='row justify-content-center'>
            <div class='col-md-10'>
                <div class='card shadow'>
                    <div class='card-header bg-primary text-white'>
                        <h4 class='mb-0'>
                            <i class='fas fa-font me-2'></i>
                            Configurações Completas de Fontes
                        </h4>
                    </div>
                    <div class='card-body'>";

// Executar SQL se solicitado
if (isset($_POST['executar_sql'])) {
    try {
        $sql_file = '../sql/configuracoes_fontes_completas.sql';
        
        if (!file_exists($sql_file)) {
            throw new Exception("Arquivo SQL não encontrado: {$sql_file}");
        }
        
        $sql_content = file_get_contents($sql_file);
        
        if (empty($sql_content)) {
            throw new Exception("Arquivo SQL está vazio");
        }
        
        // Executar cada comando SQL separadamente
        $commands = explode(';', $sql_content);
        $executados = 0;
        $erros = [];
        
        foreach ($commands as $command) {
            $command = trim($command);
            if (!empty($command) && !preg_match('/^--/', $command)) {
                try {
                    $pdo->exec($command);
                    $executados++;
                } catch (Exception $e) {
                    $erros[] = "Erro no comando: " . substr($command, 0, 50) . "... - " . $e->getMessage();
                }
            }
        }
        
        if (empty($erros)) {
            $mensagem = "✅ <strong>Sucesso!</strong> {$executados} comandos SQL executados com sucesso!";
            $tipo_mensagem = 'success';
        } else {
            $mensagem = "⚠️ <strong>Atenção!</strong> {$executados} comandos executados, mas houve alguns erros:<br>" . implode('<br>', $erros);
            $tipo_mensagem = 'warning';
        }
        
    } catch (Exception $e) {
        $mensagem = "❌ <strong>Erro!</strong> " . $e->getMessage();
        $tipo_mensagem = 'danger';
    }
}

// Gerar CSS se solicitado
if (isset($_POST['gerar_css'])) {
    try {
        $css_salvo = $visualConfig->saveCSS();
        
        if ($css_salvo) {
            $mensagem = "✅ <strong>Sucesso!</strong> CSS dinâmico gerado com sucesso!";
            $tipo_mensagem = 'success';
        } else {
            $mensagem = "❌ <strong>Erro!</strong> Falha ao gerar CSS dinâmico";
            $tipo_mensagem = 'danger';
        }
        
    } catch (Exception $e) {
        $mensagem = "❌ <strong>Erro!</strong> " . $e->getMessage();
        $tipo_mensagem = 'danger';
    }
}

// Exibir mensagem se houver
if (!empty($mensagem)) {
    echo "<div class='alert alert-{$tipo_mensagem} alert-dismissible fade show' role='alert'>
            {$mensagem}
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
}

echo "
                        <div class='row'>
                            <div class='col-md-6'>
                                <div class='card border-primary'>
                                    <div class='card-header bg-primary text-white'>
                                        <h5 class='mb-0'>
                                            <i class='fas fa-database me-2'></i>
                                            Banco de Dados
                                        </h5>
                                    </div>
                                    <div class='card-body'>
                                        <p class='text-muted'>
                                            Adiciona todas as configurações de fontes para controle total via painel admin.
                                        </p>
                                        <ul class='list-unstyled'>
                                            <li><i class='fas fa-check text-success me-2'></i>Fontes principais do site</li>
                                            <li><i class='fas fa-check text-success me-2'></i>Header e navegação</li>
                                            <li><i class='fas fa-check text-success me-2'></i>Sidebar (controle total)</li>
                                            <li><i class='fas fa-check text-success me-2'></i>Conteúdo principal</li>
                                            <li><i class='fas fa-check text-success me-2'></i>Cards e posts</li>
                                            <li><i class='fas fa-check text-success me-2'></i>Botões e badges</li>
                                            <li><i class='fas fa-check text-success me-2'></i>Anúncios</li>
                                            <li><i class='fas fa-check text-success me-2'></i>Footer</li>
                                            <li><i class='fas fa-check text-success me-2'></i>Meta textos</li>
                                            <li><i class='fas fa-check text-success me-2'></i>Seções específicas</li>
                                        </ul>
                                        <form method='post' class='mt-3'>
                                            <button type='submit' name='executar_sql' class='btn btn-primary'>
                                                <i class='fas fa-play me-2'></i>
                                                Executar Configurações
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <div class='col-md-6'>
                                <div class='card border-success'>
                                    <div class='card-header bg-success text-white'>
                                        <h5 class='mb-0'>
                                            <i class='fas fa-css3-alt me-2'></i>
                                            CSS Dinâmico
                                        </h5>
                                    </div>
                                    <div class='card-body'>
                                        <p class='text-muted'>
                                            Gera o CSS dinâmico com todas as configurações aplicadas.
                                        </p>
                                        <ul class='list-unstyled'>
                                            <li><i class='fas fa-check text-success me-2'></i>Variáveis CSS atualizadas</li>
                                            <li><i class='fas fa-check text-success me-2'></i>Configurações específicas</li>
                                            <li><i class='fas fa-check text-success me-2'></i>Responsividade mobile</li>
                                            <li><i class='fas fa-check text-success me-2'></i>Controle total via admin</li>
                                        </ul>
                                        <form method='post' class='mt-3'>
                                            <button type='submit' name='gerar_css' class='btn btn-success'>
                                                <i class='fas fa-magic me-2'></i>
                                                Gerar CSS Dinâmico
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class='mt-4'>
                            <div class='card border-info'>
                                <div class='card-header bg-info text-white'>
                                    <h5 class='mb-0'>
                                        <i class='fas fa-info-circle me-2'></i>
                                        Como Usar
                                    </h5>
                                </div>
                                <div class='card-body'>
                                    <div class='row'>
                                        <div class='col-md-6'>
                                            <h6><i class='fas fa-cog me-2'></i>1. Configurar Fontes</h6>
                                            <p class='text-muted small'>
                                                Acesse <strong>Painel Admin → Configurações Visuais</strong> e configure as fontes para cada área específica do site.
                                            </p>
                                            
                                            <h6><i class='fas fa-palette me-2'></i>2. Personalizar</h6>
                                            <p class='text-muted small'>
                                                Cada área (sidebar, header, conteúdo, etc.) tem suas próprias configurações de fonte, peso e tamanho.
                                            </p>
                                        </div>
                                        <div class='col-md-6'>
                                            <h6><i class='fas fa-mobile-alt me-2'></i>3. Responsividade</h6>
                                            <p class='text-muted small'>
                                                Configure tamanhos diferentes para desktop e mobile em cada área.
                                            </p>
                                            
                                            <h6><i class='fas fa-eye me-2'></i>4. Visualizar</h6>
                                            <p class='text-muted small'>
                                                As alterações são aplicadas automaticamente ao gerar o CSS dinâmico.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class='mt-3 text-center'>
                            <a href='configuracoes-visuais.php' class='btn btn-outline-primary me-2'>
                                <i class='fas fa-cog me-2'></i>
                                Ir para Configurações Visuais
                            </a>
                            <a href='index.php' class='btn btn-outline-secondary'>
                                <i class='fas fa-home me-2'></i>
                                Voltar ao Painel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?> 