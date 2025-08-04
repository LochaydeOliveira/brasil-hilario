<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';

echo "<h1>Teste de Salvamento</h1>";

// Verificar se está logado
if (!isset($_SESSION['usuario_id'])) {
    echo "<p style='color: red;'>❌ Não está logado</p>";
    exit;
}

echo "<p style='color: green;'>✅ Logado como: " . $_SESSION['usuario_nome'] . "</p>";
echo "<p>Tipo: " . $_SESSION['usuario_tipo'] . "</p>";

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Dados Recebidos:</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Testar salvamento simples
    try {
        $pdo->beginTransaction();
        
        $titulo = $_POST['titulo'] ?? 'Teste';
        $conteudo = $_POST['conteudo'] ?? 'Conteúdo teste';
        $slug = 'teste-' . time();
        
        $stmt = $pdo->prepare("
            INSERT INTO posts (titulo, slug, conteudo, categoria_id, autor_id, criado_em, atualizado_em)
            VALUES (?, ?, ?, 1, ?, NOW(), NOW())
        ");
        
        $result = $stmt->execute([$titulo, $slug, $conteudo, $_SESSION['usuario_id']]);
        
        if ($result) {
            $pdo->commit();
            echo "<p style='color: green;'>✅ Salvamento funcionou!</p>";
        } else {
            $pdo->rollBack();
            echo "<p style='color: red;'>❌ Erro no salvamento</p>";
        }
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<h2>Formulário de Teste</h2>";
    echo "<form method='post'>";
    echo "<p><label>Título: <input type='text' name='titulo' value='Teste'></label></p>";
    echo "<p><label>Conteúdo: <textarea name='conteudo'>Conteúdo de teste</textarea></label></p>";
    echo "<p><button type='submit'>Testar Salvamento</button></p>";
    echo "</form>";
}

echo "<h2>Informações do Sistema:</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>PDO Drivers: " . implode(', ', PDO::getAvailableDrivers()) . "</p>";
echo "<p>Session ID: " . session_id() . "</p>";
?> 