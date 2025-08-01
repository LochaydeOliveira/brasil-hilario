<?php
require_once 'config/database.php';

try {
    $sql = file_get_contents('sql/sistema_grupos_anuncios.sql');
    
    // Dividir o SQL em comandos individuais
    $commands = explode(';', $sql);
    
    foreach ($commands as $command) {
        $command = trim($command);
        if (!empty($command)) {
            $pdo->exec($command);
            echo "Comando executado com sucesso: " . substr($command, 0, 50) . "...\n";
        }
    }
    
    echo "\n✅ Tabelas de grupos de anúncios criadas com sucesso!\n";
    
} catch (Exception $e) {
    echo "❌ Erro ao executar SQL: " . $e->getMessage() . "\n";
}
?> 