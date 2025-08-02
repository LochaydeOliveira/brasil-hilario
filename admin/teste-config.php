<?php
session_start();

// Incluir configurações
require_once '../config/config.php';

echo "<h1>Teste de Configurações</h1>";

echo "<h2>Constantes definidas:</h2>";
echo "BLOG_URL: " . (defined('BLOG_URL') ? BLOG_URL : 'NÃO DEFINIDA') . "<br>";
echo "BLOG_TITLE: " . (defined('BLOG_TITLE') ? BLOG_TITLE : 'NÃO DEFINIDA') . "<br>";
echo "POSTS_PER_PAGE: " . (defined('POSTS_PER_PAGE') ? POSTS_PER_PAGE : 'NÃO DEFINIDA') . "<br>";

echo "<h2>Teste de sessão:</h2>";
if (isset($_SESSION['usuario_id'])) {
    echo "✅ Logado - ID: " . $_SESSION['usuario_id'] . "<br>";
} else {
    echo "❌ Não logado<br>";
}

echo "<h2>Links para testar:</h2>";
echo "<a href='grupos-anuncios.php'>Grupos de Anúncios</a><br>";
echo "<a href='novo-grupo-anuncios.php'>Novo Grupo</a><br>";
echo "<a href='index.php'>Dashboard</a><br>";
?> 
