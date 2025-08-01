<?php
require_once 'config/database.php';
require_once 'includes/GruposAnunciosManager.php';
require_once 'includes/AnunciosManager.php';

echo "<h1>Teste do Sistema de Grupos de Anúncios</h1>";

try {
    $gruposManager = new GruposAnunciosManager($pdo);
    $anunciosManager = new AnunciosManager($pdo);
    
    echo "<h2>✅ Conexão com banco de dados: OK</h2>";
    
    // Testar busca de anúncios
    $anuncios = $anunciosManager->getAllAnunciosComStats();
    echo "<h3>Anúncios disponíveis: " . count($anuncios) . "</h3>";
    
    if (!empty($anuncios)) {
        echo "<ul>";
        foreach ($anuncios as $anuncio) {
            echo "<li><strong>" . htmlspecialchars($anuncio['titulo']) . "</strong> - " . ucfirst($anuncio['localizacao']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>⚠️ Nenhum anúncio encontrado. Crie alguns anúncios primeiro em <a href='admin/novo-anuncio.php'>admin/novo-anuncio.php</a></p>";
    }
    
    // Testar busca de grupos
    $grupos = $gruposManager->getAllGruposComStats();
    echo "<h3>Grupos existentes: " . count($grupos) . "</h3>";
    
    if (!empty($grupos)) {
        echo "<ul>";
        foreach ($grupos as $grupo) {
            echo "<li><strong>" . htmlspecialchars($grupo['nome']) . "</strong> - " . ucfirst($grupo['localizacao']) . " (" . ucfirst($grupo['layout']) . ") - " . $grupo['total_anuncios'] . " anúncios</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>ℹ️ Nenhum grupo criado ainda. Crie seu primeiro grupo em <a href='admin/novo-grupo-anuncios.php'>admin/novo-grupo-anuncios.php</a></p>";
    }
    
    echo "<h2>🎯 Próximos Passos:</h2>";
    echo "<ol>";
    echo "<li><a href='admin/anuncios.php'>Ver anúncios existentes</a></li>";
    echo "<li><a href='admin/novo-anuncio.php'>Criar novo anúncio</a></li>";
    echo "<li><a href='admin/grupos-anuncios.php'>Gerenciar grupos de anúncios</a></li>";
    echo "<li><a href='admin/novo-grupo-anuncios.php'>Criar primeiro grupo</a></li>";
    echo "<li><a href='index.php'>Ver site funcionando</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2>❌ Erro:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 