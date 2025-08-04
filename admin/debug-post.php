<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';

echo "<h1>Debug do Post</h1>";

// Verificar se está logado
if (!isset($_SESSION['usuario_id'])) {
    echo "<p style='color: red;'>❌ Não está logado</p>";
    exit;
}

echo "<p style='color: green;'>✅ Logado como: " . $_SESSION['usuario_nome'] . "</p>";
echo "<p>Tipo: " . $_SESSION['usuario_tipo'] . "</p>";
echo "<p>ID do usuário: " . $_SESSION['usuario_id'] . "</p>";

// Verificar se foi fornecido um ID
if (!isset($_GET['id'])) {
    echo "<p style='color: red;'>❌ ID do post não fornecido</p>";
    exit;
}

$post_id = (int)$_GET['id'];
echo "<h2>Analisando Post ID: $post_id</h2>";

try {
    // Buscar o post
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo "<p style='color: red;'>❌ Post não encontrado</p>";
        exit;
    }

    echo "<h3>Dados do Post:</h3>";
    echo "<pre>";
    print_r($post);
    echo "</pre>";

    echo "<h3>Verificações de Permissão:</h3>";
    
    // Verificar se é admin
    $is_admin = is_admin();
    echo "<p>É admin? " . ($is_admin ? "✅ Sim" : "❌ Não") . "</p>";
    
    // Verificar se pode editar
    $can_edit = can_edit_post($post['autor_id']);
    echo "<p>Pode editar? " . ($can_edit ? "✅ Sim" : "❌ Não") . "</p>";
    
    // Verificar autor do post
    echo "<p>Autor do post: " . $post['autor_id'] . "</p>";
    echo "<p>Seu ID: " . $_SESSION['usuario_id'] . "</p>";
    
    // Verificar se o autor existe
    $stmt = $pdo->prepare("SELECT id, nome, tipo, status FROM usuarios WHERE id = ?");
    $stmt->execute([$post['autor_id']]);
    $autor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($autor) {
        echo "<p>Autor encontrado: " . $autor['nome'] . " (Tipo: " . $autor['tipo'] . ", Status: " . $autor['status'] . ")</p>";
    } else {
        echo "<p style='color: red;'>❌ Autor não encontrado!</p>";
    }

    // Verificar se o post tem problemas
    echo "<h3>Verificações do Post:</h3>";
    echo "<p>Título: " . (empty($post['titulo']) ? "❌ Vazio" : "✅ OK") . "</p>";
    echo "<p>Slug: " . (empty($post['slug']) ? "❌ Vazio" : "✅ OK") . "</p>";
    echo "<p>Conteúdo: " . (empty($post['conteudo']) ? "❌ Vazio" : "✅ OK") . "</p>";
    echo "<p>Categoria: " . (empty($post['categoria_id']) ? "❌ Vazio" : "✅ OK") . "</p>";
    echo "<p>Publicado: " . ($post['publicado'] ? "✅ Sim" : "❌ Não") . "</p>";

    // Verificar categoria
    $stmt = $pdo->prepare("SELECT id, nome FROM categorias WHERE id = ?");
    $stmt->execute([$post['categoria_id']]);
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($categoria) {
        echo "<p>Categoria: " . $categoria['nome'] . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Categoria não encontrada!</p>";
    }

    // Verificar tags do post
    echo "<h3>Tags do Post:</h3>";
    $stmt = $pdo->prepare("SELECT t.nome, t.id, pt.post_id, pt.tag_id FROM post_tags pt JOIN tags t ON pt.tag_id = t.id WHERE pt.post_id = ?");
    $stmt->execute([$post_id]);
    $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($tags) {
        echo "<p>Tags encontradas:</p>";
        foreach ($tags as $tag) {
            echo "<p>- " . htmlspecialchars($tag['nome']) . " (Tag ID: " . $tag['tag_id'] . ")</p>";
        }
    } else {
        echo "<p>Nenhuma tag encontrada</p>";
    }

    // Verificar se há tags duplicadas
    $stmt = $pdo->prepare("
        SELECT tag_id, COUNT(*) as count 
        FROM post_tags 
        WHERE post_id = ? 
        GROUP BY tag_id 
        HAVING COUNT(*) > 1
    ");
    $stmt->execute([$post_id]);
    $duplicatas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($duplicatas) {
        echo "<p style='color: red;'>❌ Tags duplicadas encontradas:</p>";
        foreach ($duplicatas as $dup) {
            echo "<p>- Tag ID " . $dup['tag_id'] . " aparece " . $dup['count'] . " vezes</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ Nenhuma tag duplicada encontrada</p>";
    }

    // Testar acesso ao formulário de edição
    echo "<h3>Teste de Acesso:</h3>";
    if ($can_edit) {
        echo "<p style='color: green;'>✅ Você pode editar este post</p>";
        echo "<p><a href='editar-post.php?id=$post_id' class='btn btn-primary'>Tentar Editar</a></p>";
    } else {
        echo "<p style='color: red;'>❌ Você NÃO pode editar este post</p>";
        
        if (!$is_admin) {
            echo "<p>Motivo: Você não é admin e não é o autor do post</p>";
        }
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<h3>Informações do Sistema:</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session Data:</p>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?> 