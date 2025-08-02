<?php
echo "<h1>📊 Visualizador de Cliques - Anúncios Nativos</h1>";

$logFile = 'logs/cliques_anuncios.log';

if (!file_exists($logFile)) {
    echo "<p style='color: orange;'>⚠️ Arquivo de log não encontrado: $logFile</p>";
    echo "<p>Isso significa que ainda não houve cliques registrados ou o arquivo não foi criado.</p>";
    exit;
}

$logContent = file_get_contents($logFile);
$lines = explode("\n", trim($logContent));

if (empty($lines) || $lines[0] === '') {
    echo "<p style='color: orange;'>⚠️ Arquivo de log vazio</p>";
    exit;
}

$cliques = [];
foreach ($lines as $line) {
    if (!empty($line)) {
        $dados = json_decode($line, true);
        if ($dados) {
            $cliques[] = $dados;
        }
    }
}

if (empty($cliques)) {
    echo "<p style='color: orange;'>⚠️ Nenhum clique encontrado no log</p>";
    exit;
}

// Estatísticas
$totalCliques = count($cliques);
$cliquesPorAnuncio = [];
$cliquesPorTipo = [];
$cliquesPorPost = [];

foreach ($cliques as $clique) {
    $anuncioId = $clique['anuncio_id'];
    $tipo = $clique['tipo_clique'];
    $postId = $clique['post_id'];
    
    $cliquesPorAnuncio[$anuncioId] = ($cliquesPorAnuncio[$anuncioId] ?? 0) + 1;
    $cliquesPorTipo[$tipo] = ($cliquesPorTipo[$tipo] ?? 0) + 1;
    $cliquesPorPost[$postId] = ($cliquesPorPost[$postId] ?? 0) + 1;
}

echo "<h2>📈 Estatísticas Gerais</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;'>";
echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 8px; text-align: center;'>";
echo "<h3>Total de Cliques</h3>";
echo "<p style='font-size: 2em; font-weight: bold; color: #1976d2;'>$totalCliques</p>";
echo "</div>";

echo "<div style='background: #f3e5f5; padding: 20px; border-radius: 8px; text-align: center;'>";
echo "<h3>Anúncios Únicos</h3>";
echo "<p style='font-size: 2em; font-weight: bold; color: #7b1fa2;'>" . count($cliquesPorAnuncio) . "</p>";
echo "</div>";

echo "<div style='background: #e8f5e8; padding: 20px; border-radius: 8px; text-align: center;'>";
echo "<h3>Posts Únicos</h3>";
echo "<p style='font-size: 2em; font-weight: bold; color: #388e3c;'>" . count($cliquesPorPost) . "</p>";
echo "</div>";
echo "</div>";

// Cliques por tipo
echo "<h2>🎯 Cliques por Tipo</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 30px;'>";
echo "<tr><th>Tipo</th><th>Quantidade</th><th>Percentual</th></tr>";
foreach ($cliquesPorTipo as $tipo => $quantidade) {
    $percentual = round(($quantidade / $totalCliques) * 100, 1);
    echo "<tr>";
    echo "<td>" . ucfirst($tipo) . "</td>";
    echo "<td>$quantidade</td>";
    echo "<td>$percentual%</td>";
    echo "</tr>";
}
echo "</table>";

// Top anúncios mais clicados
echo "<h2>🏆 Top Anúncios Mais Clicados</h2>";
arsort($cliquesPorAnuncio);
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 30px;'>";
echo "<tr><th>Posição</th><th>Anúncio ID</th><th>Cliques</th><th>Percentual</th></tr>";
$posicao = 1;
foreach (array_slice($cliquesPorAnuncio, 0, 10) as $anuncioId => $quantidade) {
    $percentual = round(($quantidade / $totalCliques) * 100, 1);
    echo "<tr>";
    echo "<td>#$posicao</td>";
    echo "<td>$anuncioId</td>";
    echo "<td>$quantidade</td>";
    echo "<td>$percentual%</td>";
    echo "</tr>";
    $posicao++;
}
echo "</table>";

// Últimos cliques
echo "<h2>🕒 Últimos Cliques Registrados</h2>";
$ultimosCliques = array_slice(array_reverse($cliques), 0, 20);
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Data/Hora</th><th>Anúncio ID</th><th>Post ID</th><th>Tipo</th><th>IP</th></tr>";
foreach ($ultimosCliques as $clique) {
    echo "<tr>";
    echo "<td>" . $clique['data_clique'] . "</td>";
    echo "<td>" . $clique['anuncio_id'] . "</td>";
    echo "<td>" . $clique['post_id'] . "</td>";
    echo "<td>" . ucfirst($clique['tipo_clique']) . "</td>";
    echo "<td>" . $clique['ip_usuario'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Botão para limpar log (apenas para desenvolvimento)
echo "<h2>🔧 Ações</h2>";
echo "<div style='margin-top: 20px;'>";
echo "<a href='?action=clear_log' onclick='return confirm(\"Tem certeza que deseja limpar o log?\")' style='background: #f44336; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Limpar Log</a>";
echo "<a href='diagnostico-api.php' style='background: #2196f3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Diagnóstico da API</a>";
echo "<a href='teste-anuncios.php' style='background: #4caf50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Teste Completo</a>";
echo "</div>";

// Processar ação de limpar log
if (isset($_GET['action']) && $_GET['action'] === 'clear_log') {
    if (file_put_contents($logFile, '') !== false) {
        echo "<script>alert('Log limpo com sucesso!'); window.location.href = 'visualizar-cliques.php';</script>";
    } else {
        echo "<script>alert('Erro ao limpar o log!');</script>";
    }
}
?> 