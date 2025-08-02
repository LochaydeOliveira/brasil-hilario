<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/GruposAnunciosManager.php';
require_once 'includes/AnunciosManager.php';

echo "<h2>Teste dos Anúncios da Sidebar</h2>";

try {
    $gruposManager = new GruposAnunciosManager($pdo);
    $anunciosManager = new AnunciosManager($pdo);
    
    echo "<h3>1. Verificando grupos de anúncios da sidebar:</h3>";
    $gruposSidebar = $gruposManager->getGruposPorLocalizacao('sidebar');
    echo "Grupos encontrados: " . count($gruposSidebar) . "<br>";
    
    if (!empty($gruposSidebar)) {
        foreach ($gruposSidebar as $grupo) {
            echo "- Grupo: " . $grupo['nome'] . " (ID: " . $grupo['id'] . ")<br>";
            $anuncios = $gruposManager->getAnunciosDoGrupo($grupo['id']);
            echo "  Anúncios no grupo: " . count($anuncios) . "<br>";
        }
    }
    
    echo "<h3>2. Verificando anúncios individuais da sidebar:</h3>";
    $anunciosIndividuais = $anunciosManager->getAnunciosPorLocalizacao('sidebar');
    echo "Anúncios individuais encontrados: " . count($anunciosIndividuais) . "<br>";
    
    if (!empty($anunciosIndividuais)) {
        foreach ($anunciosIndividuais as $anuncio) {
            echo "- " . $anuncio['titulo'] . " (ID: " . $anuncio['id'] . ")<br>";
        }
    }
    
    echo "<h3>3. Teste do arquivo includes/anuncios-sidebar.php:</h3>";
    echo "<div style='border: 1px solid #ccc; padding: 10px;'>";
    include 'includes/anuncios-sidebar.php';
    echo "</div>";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}
?> 