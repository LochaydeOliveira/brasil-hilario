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
    <title>Executar Configurações de Fontes - Brasil Hilário</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='css-custom/style-custom-adm.css' rel='stylesheet'>
</head>
<body>
    <div class='container-fluid'>
        <div class='row'>
            <div class='col-12'>
                <div class='card mt-4'>
                    <div class='card-header'>
                        <h5><i class='fas fa-font'></i> Executar Configurações de Fontes para Anúncios</h5>
                    </div>
                    <div class='card-body'>";

try {
    // Ler o arquivo SQL
    $sqlFile = '../sql/adicionar_configuracoes_fontes_anuncios.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("Arquivo SQL não encontrado: {$sqlFile}");
    }
    
    $sqlContent = file_get_contents($sqlFile);
    
    if (empty($sqlContent)) {
        throw new Exception("Arquivo SQL está vazio");
    }
    
    echo "<h6>Executando configurações de fontes...</h6>";
    
    // Dividir o SQL em comandos individuais
    $commands = array_filter(array_map('trim', explode(';', $sqlContent)));
    
    $executados = 0;
    $erros = [];
    
    foreach ($commands as $command) {
        if (empty($command)) continue;
        
        try {
            $stmt = $pdo->prepare($command);
            $resultado = $stmt->execute();
            
            if ($resultado) {
                $executados++;
                echo "<div class='alert alert-success'>✅ Comando executado com sucesso</div>";
            } else {
                $erros[] = "Falha ao executar comando";
                echo "<div class='alert alert-warning'>⚠️ Falha ao executar comando</div>";
            }
        } catch (Exception $e) {
            $erros[] = $e->getMessage();
            echo "<div class='alert alert-danger'>❌ Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
    // Gerar CSS dinâmico
    echo "<h6>Gerando CSS dinâmico...</h6>";
    $css_salvo = $visualConfig->saveCSS();
    
    if ($css_salvo) {
        echo "<div class='alert alert-success'>✅ CSS dinâmico gerado com sucesso</div>";
    } else {
        echo "<div class='alert alert-warning'>⚠️ Erro ao gerar CSS dinâmico</div>";
    }
    
    echo "<br><h6>Resumo:</h6>";
    echo "<div class='alert alert-info'>";
    echo "✅ Comandos executados: {$executados}<br>";
    echo "❌ Erros: " . count($erros) . "<br>";
    echo "🎨 CSS dinâmico: " . ($css_salvo ? 'Gerado' : 'Erro') . "<br>";
    echo "</div>";
    
    if (!empty($erros)) {
        echo "<br><h6>Detalhes dos erros:</h6>";
        echo "<div class='alert alert-danger'>";
        foreach ($erros as $erro) {
            echo htmlspecialchars($erro) . "<br>";
        }
        echo "</div>";
    }
    
    echo "<br><div class='alert alert-success'>";
    echo "<strong>✅ Processo concluído!</strong><br>";
    echo "As configurações de fontes foram aplicadas com sucesso.<br>";
    echo "Agora todas as fontes do site usam as configurações visuais do Admin.";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<strong>❌ Erro:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "
                    </div>
                    <div class='card-footer'>
                        <a href='configuracoes-visuais.php' class='btn btn-primary'>
                            <i class='fas fa-cog'></i> Voltar para Configurações Visuais
                        </a>
                        <a href='index.php' class='btn btn-secondary'>
                            <i class='fas fa-home'></i> Voltar ao Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";

ob_end_flush();
?> 