<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../includes/db.php';
require_once 'includes/auth.php';

echo "<h1>Teste de Salvamento do Post ID 34</h1>";

// Verificar se está logado
if (!isset($_SESSION['usuario_id'])) {
    echo "<p style='color: red;'>❌ Não está logado</p>";
    exit;
}

echo "<p style='color: green;'>✅ Logado como: " . $_SESSION['usuario_nome'] . "</p>";

// Buscar o post ID 34
try {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = 34");
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo "<p style='color: red;'>❌ Post ID 34 não encontrado</p>";
        exit;
    }

    echo "<h3>Dados do Post:</h3>";
    echo "<p>Título: " . htmlspecialchars($post['titulo']) . "</p>";
    echo "<p>Autor ID: " . $post['autor_id'] . "</p>";

    // Verificar tags atuais
    $stmt = $pdo->prepare("SELECT t.nome, t.id FROM post_tags pt JOIN tags t ON pt.tag_id = t.id WHERE pt.post_id = 34");
    $stmt->execute();
    $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Tags atuais do post:</h3>";
    if ($tags) {
        foreach ($tags as $tag) {
            echo "<p>- " . htmlspecialchars($tag['nome']) . " (ID: " . $tag['id'] . ")</p>";
        }
    } else {
        echo "<p>Nenhuma tag encontrada</p>";
    }

    // Simular dados de salvamento
    $dados_teste = [
        'id' => 34,
        'titulo' => $post['titulo'] . ' (TESTE)',
        'slug' => $post['slug'],
        'conteudo' => $post['conteudo'],
        'resumo' => $post['resumo'],
        'categoria_id' => $post['categoria_id'],
        'autor_id' => $post['autor_id'],
        'publicado' => $post['publicado'],
        'tags' => 'Alexandre de Moraes, STF, PL, prisão Bolsonaro, PT, medidas cautelares, descumprimento, política brasileira'
    ];

    echo "<h3>Testando salvamento...</h3>";
    
    // Simular o processo de salvamento
    $pdo->beginTransaction();
    
    try {
        // Atualizar o post
        $stmt = $pdo->prepare("
            UPDATE posts 
            SET titulo = ?, slug = ?, conteudo = ?, resumo = ?, categoria_id = ?, publicado = ?, atualizado_em = NOW()
            WHERE id = ?
        ");
        $result = $stmt->execute([
            $dados_teste['titulo'], 
            $dados_teste['slug'], 
            $dados_teste['conteudo'], 
            $dados_teste['resumo'], 
            $dados_teste['categoria_id'], 
            $dados_teste['publicado'], 
            34
        ]);

        echo "<p>Atualização do post: " . ($result ? "✅ SUCESSO" : "❌ FALHA") . "</p>";

        // Remover tags antigas
        $stmt = $pdo->prepare("DELETE FROM post_tags WHERE post_id = ?");
        $stmt->execute([34]);
        echo "<p>Tags antigas removidas</p>";

        // Processar novas tags
        $tags = array_filter(array_map('trim', explode(',', $dados_teste['tags'])));
        echo "<p>Processando " . count($tags) . " tags...</p>";

        foreach ($tags as $tag_nome) {
            if (empty($tag_nome)) continue;

            $tag_slug = strtolower(trim(preg_replace('/[^a-z0-9-]/', '-', $tag_nome)));
            $tag_slug = preg_replace('/-+/', '-', $tag_slug);
            $tag_slug = trim($tag_slug, '-');

            $stmt = $pdo->prepare("SELECT id FROM tags WHERE slug = ?");
            $stmt->execute([$tag_slug]);
            $tag = $stmt->fetch(PDO::FETCH_ASSOC);
            $tag_id = $tag['id'] ?? null;

            if (!$tag_id) {
                $stmt = $pdo->prepare("INSERT INTO tags (nome, slug) VALUES (?, ?)");
                $stmt->execute([$tag_nome, $tag_slug]);
                $tag_id = $pdo->lastInsertId();
                echo "<p>Nova tag criada: $tag_nome (ID: $tag_id)</p>";
            } else {
                echo "<p>Tag existente: $tag_nome (ID: $tag_id)</p>";
            }

            // Usar INSERT IGNORE para evitar erros de chave duplicada
            $stmt = $pdo->prepare("INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?, ?)");
            $result = $stmt->execute([34, $tag_id]);
            echo "<p>Inserção de tag: " . ($result ? "✅ SUCESSO" : "❌ FALHA") . "</p>";
        }

        $pdo->commit();
        echo "<p style='color: green;'>✅ Teste concluído com sucesso!</p>";
        
        // Verificar tags após o teste
        $stmt = $pdo->prepare("SELECT t.nome, t.id FROM post_tags pt JOIN tags t ON pt.tag_id = t.id WHERE pt.post_id = 34");
        $stmt->execute();
        $tags_finais = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h3>Tags após o teste:</h3>";
        if ($tags_finais) {
            foreach ($tags_finais as $tag) {
                echo "<p>- " . htmlspecialchars($tag['nome']) . " (ID: " . $tag['id'] . ")</p>";
            }
        } else {
            echo "<p>Nenhuma tag encontrada</p>";
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<p style='color: red;'>❌ Erro durante o teste: " . $e->getMessage() . "</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<p><a href='posts.php'>Voltar para Posts</a></p>";
?> 