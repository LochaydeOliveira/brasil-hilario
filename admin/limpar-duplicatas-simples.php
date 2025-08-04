<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';

echo "<h1>Limpeza Simples de Tags Duplicadas - Post ID 34</h1>";

// Verificar se está logado
if (!isset($_SESSION['usuario_id'])) {
    echo "<p style='color: red;'>❌ Não está logado</p>";
    exit;
}

echo "<p style='color: green;'>✅ Logado como: " . $_SESSION['usuario_nome'] . "</p>";

try {
    // Verificar tags duplicadas antes da limpeza
    $stmt = $pdo->prepare("
        SELECT tag_id, COUNT(*) as count 
        FROM post_tags 
        WHERE post_id = 34 
        GROUP BY tag_id 
        HAVING COUNT(*) > 1
    ");
    $stmt->execute();
    $duplicatas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($duplicatas) {
        echo "<h3>Tags duplicadas encontradas:</h3>";
        foreach ($duplicatas as $dup) {
            echo "<p>- Tag ID " . $dup['tag_id'] . " aparece " . $dup['count'] . " vezes</p>";
        }
        
        echo "<h3>Iniciando limpeza...</h3>";
        
        // Método mais simples: deletar todas as tags do post e reinserir apenas uma de cada
        $pdo->beginTransaction();
        
        try {
            // 1. Pegar todas as tags únicas do post
            $stmt = $pdo->prepare("
                SELECT DISTINCT tag_id 
                FROM post_tags 
                WHERE post_id = 34
            ");
            $stmt->execute();
            $tags_unicas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<p>Tags únicas encontradas: " . count($tags_unicas) . "</p>";
            
            // 2. Deletar todas as tags do post
            $stmt = $pdo->prepare("DELETE FROM post_tags WHERE post_id = 34");
            $stmt->execute();
            echo "<p>Tags antigas removidas</p>";
            
            // 3. Reinserir apenas uma entrada para cada tag
            foreach ($tags_unicas as $tag_id) {
                $stmt = $pdo->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (34, ?)");
                $stmt->execute([$tag_id]);
                echo "<p>Tag ID $tag_id reinserida</p>";
            }
            
            $pdo->commit();
            echo "<p style='color: green;'>✅ Limpeza concluída com sucesso!</p>";
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
        
    } else {
        echo "<p style='color: green;'>✅ Nenhuma tag duplicada encontrada</p>";
    }
    
    // Verificar tags após a limpeza
    echo "<h3>Tags após a limpeza:</h3>";
    $stmt = $pdo->prepare("SELECT t.nome, t.id FROM post_tags pt JOIN tags t ON pt.tag_id = t.id WHERE pt.post_id = 34");
    $stmt->execute();
    $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($tags) {
        foreach ($tags as $tag) {
            echo "<p>- " . htmlspecialchars($tag['nome']) . " (ID: " . $tag['id'] . ")</p>";
        }
    } else {
        echo "<p>Nenhuma tag encontrada</p>";
    }
    
    // Verificar se ainda há duplicatas
    $stmt = $pdo->prepare("
        SELECT tag_id, COUNT(*) as count 
        FROM post_tags 
        WHERE post_id = 34 
        GROUP BY tag_id 
        HAVING COUNT(*) > 1
    ");
    $stmt->execute();
    $duplicatas_finais = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($duplicatas_finais) {
        echo "<p style='color: red;'>❌ Ainda há tags duplicadas após a limpeza!</p>";
    } else {
        echo "<p style='color: green;'>✅ Nenhuma tag duplicada restante</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<p><a href='posts.php'>Voltar para Posts</a></p>";
echo "<p><a href='debug-post.php?id=34'>Debug do Post 34</a></p>";
echo "<p><a href='teste-salvar.php'>Testar Salvamento</a></p>";
?> 