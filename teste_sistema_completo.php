<?php
require_once 'config/database.php';
require_once 'includes/VisualConfigManager.php';

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Teste do Sistema Completo - Brasil Hilário</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .test-success { background-color: #d4edda; border-color: #c3e6cb; }
        .test-error { background-color: #f8d7da; border-color: #f5c6cb; }
        .test-info { background-color: #d1ecf1; border-color: #bee5eb; }
    </style>
</head>
<body>
    <div class='container mt-4'>
        <h1 class='text-center mb-4'>🧪 Teste do Sistema Completo de Configurações Visuais</h1>";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='test-section test-success'>
        <h3>✅ Conexão com Banco de Dados</h3>
        <p>Conectado com sucesso ao banco: <strong>" . DB_NAME . "</strong></p>
    </div>";
    
    // Testar VisualConfigManager
    $visualManager = new VisualConfigManager($pdo);
    
    echo "<div class='test-section test-info'>
        <h3>🔧 Testando VisualConfigManager</h3>";
    
    // Testar geração de CSS
    $css = $visualManager->generateCSS();
    if (!empty($css)) {
        echo "<p>✅ CSS gerado com sucesso! Tamanho: <strong>" . strlen($css) . " caracteres</strong></p>";
        
        // Salvar CSS
        $cssSaved = $visualManager->saveCSS();
        if ($cssSaved) {
            echo "<p>✅ CSS salvo no arquivo <strong>assets/css/dynamic.css</strong></p>";
        } else {
            echo "<p>❌ Erro ao salvar CSS</p>";
        }
    } else {
        echo "<p>❌ Erro ao gerar CSS</p>";
    }
    
    echo "</div>";
    
    // Verificar configurações no banco
    echo "<div class='test-section test-info'>
        <h3>📊 Verificando Configurações no Banco</h3>";
    
    $stmt = $pdo->prepare("SELECT categoria, COUNT(*) as total FROM configuracoes_visuais WHERE ativo = 1 GROUP BY categoria");
    $stmt->execute();
    $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($configs)) {
        echo "<p>✅ Configurações encontradas:</p><ul>";
        foreach ($configs as $config) {
            echo "<li><strong>{$config['categoria']}</strong>: {$config['total']} configurações</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>⚠️ Nenhuma configuração encontrada no banco</p>";
    }
    
    echo "</div>";
    
    // Testar classes CSS específicas
    echo "<div class='test-section test-info'>
        <h3>🎨 Testando Classes CSS Específicas</h3>
        <p>Verificando se as classes CSS estão sendo aplicadas corretamente...</p>";
    
    $cssClasses = [
        'header' => '.header',
        'site-logo' => '.site-logo',
        'navbar' => '.navbar',
        'nav-link' => '.nav-link',
        'sidebar' => '.sidebar',
        'widget-title' => '.widget-title',
        'related-posts-block' => '.related-posts-block',
        'related-posts-title' => '.related-posts-title',
        'latest-posts-block' => '.latest-posts-block',
        'latest-posts-title' => '.latest-posts-title',
        'category-tag' => '.category-tag',
        'footer' => '.footer',
        'section-title' => '.section-title',
        'card' => '.card',
        'card-title' => '.card-title',
        'btn-primary' => '.btn-primary',
        'badge-category' => '.badge-category'
    ];
    
    $classesEncontradas = 0;
    foreach ($cssClasses as $nome => $classe) {
        if (strpos($css, $classe) !== false) {
            $classesEncontradas++;
        }
    }
    
    if ($classesEncontradas > 0) {
        echo "<p>✅ <strong>{$classesEncontradas}</strong> de <strong>" . count($cssClasses) . "</strong> classes CSS encontradas no CSS gerado</p>";
    } else {
        echo "<p>❌ Nenhuma classe CSS específica encontrada</p>";
    }
    
    echo "</div>";
    
    // Instruções para teste
    echo "<div class='test-section test-success'>
        <h3>🚀 Próximos Passos para Teste</h3>
        <ol>
            <li><strong>Acesse:</strong> <code>admin/executar_configuracoes_completas.php</code></li>
            <li><strong>Clique:</strong> 'APLICAR CONFIGURAÇÕES COMPLETAS'</li>
            <li><strong>Configure:</strong> No Painel Admin → Configurações Visuais</li>
            <li><strong>Teste:</strong> Em diferentes dispositivos</li>
            <li><strong>Verifique:</strong> Se as mudanças estão sendo aplicadas</li>
        </ol>
    </div>";
    
} catch (PDOException $e) {
    echo "<div class='test-section test-error'>
        <h3>❌ Erro de Conexão</h3>
        <p><strong>Erro:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        <p>Verifique as configurações de banco de dados em <code>config/database.php</code></p>
    </div>";
} catch (Exception $e) {
    echo "<div class='test-section test-error'>
        <h3>❌ Erro Geral</h3>
        <p><strong>Erro:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
    </div>";
}

echo "
    </div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?> 