<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';
require_once '../includes/ConfigManager.php';

$configManager = new ConfigManager($pdo);

echo "<h2>Verificação do Sistema de Configurações</h2>";

// 1. Verificar se a tabela existe
echo "<h3>1. Verificando estrutura da tabela:</h3>";
$stmt = $pdo->query("SHOW TABLES LIKE 'configuracoes'");
$tables = $stmt->fetchAll(PDO::FETCH_NUM);

if ($tables && count($tables) > 0) {
    echo "✅ Tabela 'configuracoes' existe<br>";
} else {
    echo "❌ Tabela 'configuracoes' não existe<br>";
    exit;
}

// 2. Verificar estrutura da tabela
echo "<h3>2. Estrutura da tabela:</h3>";
$stmt = $pdo->query("DESCRIBE configuracoes");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($columns) {
    echo "<table border='1'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th></tr>";
    foreach ($columns as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 3. Verificar se há dados
echo "<h3>3. Verificando dados:</h3>";
$stmt = $pdo->query("SELECT COUNT(*) as total FROM configuracoes");
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo "Total de configurações: " . htmlspecialchars($row['total']) . "<br>";
    
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
    echo "✅ ConfigManager funcionando. Valor de teste: " . htmlspecialchars($test_value) . "<br>";
} catch (Exception $e) {
    echo "❌ Erro no ConfigManager: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// 5. Testar getGroup
echo "<h3>5. Testando getGroup:</h3>";
try {
    $geral_configs = $configManager->getGroup('geral');
    echo "✅ getGroup funcionando. Configurações gerais: " . count($geral_configs) . " itens<br>";
} catch (Exception $e) {
    echo "❌ Erro no getGroup: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// 6. Listar algumas configurações
echo "<h3>6. Algumas configurações:</h3>";
$stmt = $pdo->query("SELECT chave, valor, tipo, grupo FROM configuracoes LIMIT 5");
$configs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($configs && count($configs) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Chave</th><th>Valor</th><th>Tipo</th><th>Grupo</th></tr>";
    foreach ($configs as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['chave']) . "</td>";
        echo "<td>" . htmlspecialchars($row['valor']) . "</td>";
        echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['grupo']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<br><a href='configuracoes.php'>← Voltar para Configurações</a>";
?>
