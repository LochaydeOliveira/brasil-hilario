<?php
// Teste da API de anúncios
echo "<h1>Teste da API de Anúncios</h1>";

// Teste 1: Verificar se o arquivo existe
$api_file = 'api/registrar-clique-anuncio.php';
echo "<h2>1. Verificação do Arquivo</h2>";
echo "<p><strong>Arquivo:</strong> $api_file</p>";
echo "<p><strong>Existe:</strong> " . (file_exists($api_file) ? "✅ Sim" : "❌ Não") . "</p>";

// Teste 2: Verificar permissões
if (file_exists($api_file)) {
    echo "<p><strong>Permissões:</strong> " . substr(sprintf('%o', fileperms($api_file)), -4) . "</p>";
    echo "<p><strong>Legível:</strong> " . (is_readable($api_file) ? "✅ Sim" : "❌ Não") . "</p>";
}

// Teste 3: Simular requisição POST
echo "<h2>2. Teste de Requisição POST</h2>";
$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/api/registrar-clique-anuncio.php';
echo "<p><strong>URL da API:</strong> $url</p>";

// Dados de teste
$test_data = [
    'anuncio_id' => 1,
    'post_id' => 1,
    'tipo_clique' => 'imagem'
];

echo "<p><strong>Dados de teste:</strong></p>";
echo "<pre>" . json_encode($test_data, JSON_PRETTY_PRINT) . "</pre>";

// Teste 4: Verificar se o .htaccess está permitindo acesso
echo "<h2>3. Verificação do .htaccess</h2>";
$htaccess_file = '.htaccess';
if (file_exists($htaccess_file)) {
    $htaccess_content = file_get_contents($htaccess_file);
    if (strpos($htaccess_content, 'registrar-clique-anuncio.php') !== false) {
        echo "<p>✅ Regra para API encontrada no .htaccess</p>";
    } else {
        echo "<p>❌ Regra para API NÃO encontrada no .htaccess</p>";
    }
} else {
    echo "<p>❌ Arquivo .htaccess não encontrado</p>";
}

// Teste 5: Verificar configuração do servidor
echo "<h2>4. Configuração do Servidor</h2>";
echo "<p><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

// Teste 6: Verificar se o mod_rewrite está ativo
echo "<h2>5. Verificação do mod_rewrite</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "<p><strong>mod_rewrite:</strong> " . (in_array('mod_rewrite', $modules) ? "✅ Ativo" : "❌ Inativo") . "</p>";
} else {
    echo "<p>Não foi possível verificar os módulos do Apache</p>";
}

// Teste 7: Teste manual da API
echo "<h2>6. Teste Manual da API</h2>";
echo "<p>Para testar manualmente, execute no console do navegador:</p>";
echo "<pre>";
echo "fetch('/api/registrar-clique-anuncio.php', {
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
    return response.json();
})
.then(data => {
    console.log('Resposta:', data);
})
.catch(error => {
    console.error('Erro:', error);
});";
echo "</pre>";

echo "<h2>7. Links Úteis</h2>";
echo "<ul>";
echo "<li><a href='teste-anuncios.php'>Teste Completo do Sistema</a></li>";
echo "<li><a href='verificar-meta-tag.php'>Verificar Meta Tag</a></li>";
echo "<li><a href='admin/estatisticas-anuncios.php'>Dashboard de Estatísticas</a></li>";
echo "</ul>";
?> 