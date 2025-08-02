<?php
require_once 'config/config.php';
require_once 'includes/db.php';

echo "<h1>Verificação da Meta Tag post-id</h1>";

// Buscar alguns posts para testar
$stmt = $pdo->prepare("SELECT id, titulo, slug FROM posts WHERE publicado = 1 ORDER BY id LIMIT 5");
$stmt->execute();
$posts = $stmt->fetchAll();

if (empty($posts)) {
    echo "<p style='color: red;'>Nenhum post encontrado para teste.</p>";
    exit;
}

echo "<h2>Posts Disponíveis para Teste:</h2>";
echo "<ul>";
foreach ($posts as $post) {
    $url = BLOG_URL . '/post/' . $post['slug'];
    echo "<li><a href='$url' target='_blank'>" . htmlspecialchars($post['titulo']) . "</a> (ID: {$post['id']})</li>";
}
echo "</ul>";

echo "<h2>Como Verificar:</h2>";
echo "<ol>";
echo "<li>Clique em um dos links acima para abrir uma página de post</li>";
echo "<li>Pressione F12 para abrir as ferramentas do desenvolvedor</li>";
echo "<li>Vá para a aba 'Elements' (Elementos)</li>";
echo "<li>Procure por <code>&lt;meta name=\"post-id\" content=\"...\"&gt;</code> no &lt;head&gt;</li>";
echo "<li>Verifique se o content contém o ID correto do post</li>";
echo "</ol>";

echo "<h2>Teste JavaScript:</h2>";
echo "<p>Abra o console do navegador (F12 > Console) e execute:</p>";
echo "<code>console.log('Post ID:', document.querySelector('meta[name=\"post-id\"]')?.content);</code>";

echo "<h2>Teste de Clique:</h2>";
echo "<p>Para testar se os cliques estão sendo registrados:</p>";
echo "<ol>";
echo "<li>Abra uma página de post</li>";
echo "<li>Abra o console do navegador</li>";
echo "<li>Clique em um anúncio</li>";
echo "<li>Verifique se aparecem as mensagens de log no console</li>";
echo "<li>Verifique se a requisição para /api/registrar-clique-anuncio.php é feita</li>";
echo "</ol>";

echo "<h2>Logs do Servidor:</h2>";
echo "<p>Para verificar os logs do servidor, verifique o arquivo de log do PHP (geralmente em /var/log/apache2/error.log ou similar).</p>";
echo "<p>Procure por mensagens que começam com 'API registrar-clique-anuncio.php chamada'.</p>";
?> 