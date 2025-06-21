<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';
require_once '../includes/ConfigManager.php';

$configManager = new ConfigManager($conn);

echo "<h2>Verificação do Sistema de Configurações</h2>";

// 1. Verificar se a tabela existe
echo "<h3>1. Verificando estrutura da tabela:</h3>";
$result = $conn->query("SHOW TABLES LIKE 'configuracoes'");
if ($result && $result->num_rows > 0) {
    echo "✅ Tabela 'configuracoes' existe<br>";
} else {
    echo "❌ Tabela 'configuracoes' não existe<br>";
    exit;
}

// 2. Verificar estrutura da tabela
echo "<h3>2. Estrutura da tabela:</h3>";
$result = $conn->query("DESCRIBE configuracoes");
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 3. Verificar se há dados
echo "<h3>3. Verificando dados:</h3>";
$result = $conn->query("SELECT COUNT(*) as total FROM configuracoes");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Total de configurações: {$row['total']}<br>";
    
    if ($row['total'] == 0) {
        echo "⚠️ Nenhuma configuração encontrada. Execute o script de inserção.<br>";
    } else {
        echo "✅ Configurações encontradas<br>";
    }
}

// 4. Testar ConfigManager
echo "<h3>4. Testando ConfigManager:</h3>";
try {
    $test_value = $configManager->get('site_title', 'Teste');
    echo "✅ ConfigManager funcionando. Valor de teste: $test_value<br>";
} catch (Exception $e) {
    echo "❌ Erro no ConfigManager: " . $e->getMessage() . "<br>";
}

// 5. Testar getGroup
echo "<h3>5. Testando getGroup:</h3>";
try {
    $geral_configs = $configManager->getGroup('geral');
    echo "✅ getGroup funcionando. Configurações gerais: " . count($geral_configs) . " itens<br>";
} catch (Exception $e) {
    echo "❌ Erro no getGroup: " . $e->getMessage() . "<br>";
}

// 6. Listar algumas configurações
echo "<h3>6. Algumas configurações:</h3>";
$result = $conn->query("SELECT chave, valor, tipo, grupo FROM configuracoes LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Chave</th><th>Valor</th><th>Tipo</th><th>Grupo</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['chave']}</td>";
        echo "<td>{$row['valor']}</td>";
        echo "<td>{$row['tipo']}</td>";
        echo "<td>{$row['grupo']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<br><a href='configuracoes.php'>← Voltar para Configurações</a>";
?> 