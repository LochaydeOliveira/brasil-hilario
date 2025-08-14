<?php
/**
 * INFORMAÇÕES DO SISTEMA - Brasil Hilário
 * 
 * Arquivo simples para verificar se o PHP está funcionando
 */

echo "<h1>Brasil Hilário - Status do Sistema</h1>";
echo "<hr>";

// Informações básicas do PHP
echo "<h2>Informações do PHP</h2>";
echo "<p><strong>Versão do PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Servidor:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido') . "</p>";
echo "<p><strong>Data/Hora:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Desconhecido') . "</p>";

echo "<hr>";

// Verificar extensões necessárias
echo "<h2>Extensões PHP</h2>";
$extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✅ {$ext} - Carregada</p>";
    } else {
        echo "<p style='color: red;'>❌ {$ext} - Não carregada</p>";
    }
}

echo "<hr>";

// Verificar arquivos essenciais
echo "<h2>Arquivos do Sistema</h2>";
$files = [
    'includes/db.php',
    'config/config.php',
    'includes/Logger.php',
    'includes/CacheManager.php',
    'includes/Validator.php',
    'includes/session_init.php',
    '.env'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ {$file} - Existe</p>";
    } else {
        echo "<p style='color: red;'>❌ {$file} - Não encontrado</p>";
    }
}

echo "<hr>";

// Verificar diretórios
echo "<h2>Diretórios</h2>";
$directories = ['cache', 'logs', 'backups'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "<p style='color: green;'>✅ {$dir} - Existe e tem permissão de escrita</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ {$dir} - Existe mas sem permissão de escrita</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ {$dir} - Não existe</p>";
    }
}

echo "<hr>";

// Teste de conexão com banco (se possível)
echo "<h2>Teste de Conexão</h2>";
if (file_exists('includes/db.php')) {
    try {
        include_once 'includes/db.php';
        if (isset($pdo)) {
            $pdo->query('SELECT 1');
            echo "<p style='color: green;'>✅ Conexão com banco - OK</p>";
        } else {
            echo "<p style='color: red;'>❌ Variável \$pdo não encontrada</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erro na conexão: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Arquivo includes/db.php não encontrado</p>";
}

echo "<hr>";

// Links úteis
echo "<h2>Links Úteis</h2>";
echo "<p><a href='configurar_projeto.php'>🔧 Configurar Projeto</a></p>";
echo "<p><a href='admin/backup.php'>💾 Sistema de Backup</a></p>";
echo "<p><a href='newsletter'>📧 Newsletter</a></p>";
echo "<p><a href='index.php'>🏠 Voltar ao Site</a></p>";

echo "<hr>";
echo "<p><em>Arquivo gerado em: " . date('Y-m-d H:i:s') . "</em></p>";
?> 