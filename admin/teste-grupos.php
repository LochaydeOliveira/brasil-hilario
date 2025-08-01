<?php
session_start();

// Verificar se está logado
if (!isset($_SESSION['usuario_id'])) {
    echo "❌ Não está logado. Redirecionando para login...";
    header('Location: login.php');
    exit;
}

echo "✅ Logado com sucesso!<br>";
echo "ID do usuário: " . $_SESSION['usuario_id'] . "<br>";
echo "Tipo de usuário: " . ($_SESSION['usuario_tipo'] ?? 'N/A') . "<br>";

// Testar se os arquivos existem
$files_to_check = [
    'includes/header.php',
    'includes/footer.php',
    'includes/sidebar.php',
    '../includes/GruposAnunciosManager.php',
    '../includes/AnunciosManager.php'
];

echo "<h3>Verificando arquivos:</h3>";
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ $file - OK<br>";
    } else {
        echo "❌ $file - NÃO ENCONTRADO<br>";
    }
}

echo "<h3>Links para testar:</h3>";
echo "<a href='grupos-anuncios.php'>Grupos de Anúncios</a><br>";
echo "<a href='novo-grupo-anuncios.php'>Novo Grupo</a><br>";
echo "<a href='index.php'>Dashboard</a><br>";
?> 