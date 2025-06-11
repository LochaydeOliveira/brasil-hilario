<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de Conexão</h1>";

// Teste 1: Verificar se o PHP está funcionando
echo "<h2>Teste 1: PHP</h2>";
echo "PHP está funcionando! Versão: " . phpversion();

// Teste 2: Verificar se os arquivos de configuração existem
echo "<h2>Teste 2: Arquivos de Configuração</h2>";
$files = [
    '../config/config.php',
    '../includes/db.php',
    'includes/auth.php'
];

foreach ($files as $file) {
    echo "$file: " . (file_exists($file) ? "✅ Existe" : "❌ Não existe") . "<br>";
}

// Teste 3: Tentar incluir os arquivos
echo "<h2>Teste 3: Inclusão de Arquivos</h2>";
try {
    require_once '../config/config.php';
    echo "config.php: ✅ Incluído com sucesso<br>";
} catch (Exception $e) {
    echo "config.php: ❌ Erro: " . $e->getMessage() . "<br>";
}

try {
    require_once '../includes/db.php';
    echo "db.php: ✅ Incluído com sucesso<br>";
} catch (Exception $e) {
    echo "db.php: ❌ Erro: " . $e->getMessage() . "<br>";
}

// Teste 4: Verificar conexão com o banco
echo "<h2>Teste 4: Conexão com Banco de Dados</h2>";
try {
    if (isset($pdo)) {
        echo "Conexão com banco: ✅ Funcionando<br>";
    } else {
        echo "Conexão com banco: ❌ Não inicializada<br>";
    }
} catch (Exception $e) {
    echo "Conexão com banco: ❌ Erro: " . $e->getMessage() . "<br>";
}

// Teste 5: Verificar permissões
echo "<h2>Teste 5: Permissões</h2>";
echo "Diretório atual: " . getcwd() . "<br>";
echo "Permissões do diretório: " . substr(sprintf('%o', fileperms('.')), -4) . "<br>";
echo "Permissões deste arquivo: " . substr(sprintf('%o', fileperms(__FILE__)), -4) . "<br>";
?> 