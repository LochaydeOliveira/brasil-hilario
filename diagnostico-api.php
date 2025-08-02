<?php
echo "<h1>🔍 Diagnóstico da API de Anúncios</h1>";

// 1. Verificar configuração do servidor
echo "<h2>1. Configuração do Servidor</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Item</th><th>Valor</th><th>Status</th></tr>";

$items = [
    'PHP Version' => phpversion(),
    'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
    'Document Root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
    'Script Name' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
    'Request URI' => $_SERVER['REQUEST_URI'] ?? 'N/A',
    'HTTP Host' => $_SERVER['HTTP_HOST'] ?? 'N/A'
];

foreach ($items as $item => $value) {
    $status = $value ? "✅ OK" : "❌ Problema";
    echo "<tr><td>$item</td><td>" . htmlspecialchars($value) . "</td><td>$status</td></tr>";
}
echo "</table>";

// 2. Verificar arquivos e permissões
echo "<h2>2. Verificação de Arquivos</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Arquivo</th><th>Existe</th><th>Permissões</th><th>Legível</th><th>Status</th></tr>";

$files = [
    'api/registrar-clique-anuncio.php',
    'config/config.php',
    'includes/db.php',
    'includes/AnunciosManager.php',
    '.htaccess'
];

foreach ($files as $file) {
    $exists = file_exists($file);
    $permissions = $exists ? substr(sprintf('%o', fileperms($file)), -4) : 'N/A';
    $readable = $exists ? (is_readable($file) ? 'Sim' : 'Não') : 'N/A';
    $status = $exists && is_readable($file) ? "✅ OK" : "❌ Problema";
    
    echo "<tr>";
    echo "<td>$file</td>";
    echo "<td>" . ($exists ? "Sim" : "Não") . "</td>";
    echo "<td>$permissions</td>";
    echo "<td>$readable</td>";
    echo "<td>$status</td>";
    echo "</tr>";
}
echo "</table>";

// 3. Verificar configuração do .htaccess
echo "<h2>3. Configuração do .htaccess</h2>";
if (file_exists('.htaccess')) {
    $htaccess_content = file_get_contents('.htaccess');
    $rules = [
        'RewriteEngine On' => strpos($htaccess_content, 'RewriteEngine On') !== false,
        'API registrar-clique-anuncio.php' => strpos($htaccess_content, 'registrar-clique-anuncio.php') !== false,
        'mod_rewrite' => strpos($htaccess_content, 'mod_rewrite') !== false
    ];
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Regra</th><th>Encontrada</th><th>Status</th></tr>";
    foreach ($rules as $rule => $found) {
        $status = $found ? "✅ OK" : "❌ Ausente";
        echo "<tr><td>$rule</td><td>" . ($found ? "Sim" : "Não") . "</td><td>$status</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Arquivo .htaccess não encontrado!</p>";
}

// 4. Teste de conectividade com banco de dados
echo "<h2>4. Teste de Banco de Dados</h2>";
try {
    require_once 'config/config.php';
    require_once 'includes/db.php';
    
    $stmt = $pdo->prepare("SELECT 1");
    $stmt->execute();
    echo "<p>✅ Conexão com banco de dados: OK</p>";
    
    // Verificar tabelas
    $tables = ['anuncios', 'cliques_anuncios', 'posts'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $exists = $stmt->fetch();
        $status = $exists ? "✅ Existe" : "❌ Não existe";
        echo "<p><strong>$table:</strong> $status</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro de banco de dados: " . $e->getMessage() . "</p>";
}

// 5. Teste de URL da API
echo "<h2>5. Teste de URL da API</h2>";
$base_url = 'http://' . $_SERVER['HTTP_HOST'];
$api_url = $base_url . '/api/registrar-clique-anuncio.php';
echo "<p><strong>URL da API:</strong> <a href='$api_url' target='_blank'>$api_url</a></p>";

// 6. Teste manual da API
echo "<h2>6. Teste Manual da API</h2>";
echo "<p>Execute este código no console do navegador para testar a API:</p>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
echo "fetch('$api_url', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        anuncio_id: 1,
        post_id: 1,
        tipo_clique: 'imagem'
    })
})
.then(response => {
    console.log('Status:', response.status);
    console.log('Headers:', response.headers);
    return response.json();
})
.then(data => {
    console.log('Resposta:', data);
})
.catch(error => {
    console.error('Erro:', error);
});";
echo "</pre>";

// 7. Verificar logs do servidor
echo "<h2>7. Logs do Servidor</h2>";
echo "<p>Para verificar os logs do servidor, procure por:</p>";
echo "<ul>";
echo "<li>Arquivo de erro do Apache: <code>/var/log/apache2/error.log</code></li>";
echo "<li>Arquivo de erro do PHP: <code>/var/log/php_errors.log</code></li>";
echo "<li>Procure por mensagens que contenham: <code>registrar-clique-anuncio.php</code></li>";
echo "</ul>";

// 8. Soluções comuns
echo "<h2>8. Soluções Comuns</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border: 1px solid #ffeaa7;'>";
echo "<h3>Se o erro 403 persistir:</h3>";
echo "<ol>";
echo "<li>Verifique se o mod_rewrite está ativo no Apache</li>";
echo "<li>Confirme se o arquivo .htaccess está sendo lido</li>";
echo "<li>Verifique as permissões dos arquivos (644 para arquivos, 755 para diretórios)</li>";
echo "<li>Teste se o arquivo da API é acessível diretamente</li>";
echo "<li>Verifique se não há regras conflitantes no .htaccess</li>";
echo "</ol>";
echo "</div>";

// 9. Links úteis
echo "<h2>9. Links Úteis</h2>";
echo "<ul>";
echo "<li><a href='teste-api.php'>Teste da API</a></li>";
echo "<li><a href='teste-anuncios.php'>Teste Completo do Sistema</a></li>";
echo "<li><a href='verificar-meta-tag.php'>Verificar Meta Tag</a></li>";
echo "<li><a href='admin/estatisticas-anuncios.php'>Dashboard de Estatísticas</a></li>";
echo "</ul>";
?> 