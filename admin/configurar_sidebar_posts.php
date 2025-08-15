<?php
require_once '../config/config.php';
require_once '../config/database_unified.php';
require_once 'includes/auth.php';

// Verificar se o usuário está logado
check_login();

$dbManager = DatabaseManager::getInstance();

echo "<h2>⚙️ Configurando Sidebar para Posts Específicos</h2>";

try {
    // 1. Verificar grupos da sidebar
    echo "<h3>1. Grupos da sidebar atuais:</h3>";
    $grupos_sidebar = $dbManager->query("
        SELECT g.*, COUNT(gi.anuncio_id) as total_anuncios
        FROM grupos_anuncios g 
        LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
        WHERE g.localizacao = 'sidebar' AND g.ativo = 1
        GROUP BY g.id
        ORDER BY g.criado_em DESC
    ");
    
    if (empty($grupos_sidebar)) {
        echo "<p style='color: red;'>❌ Nenhum grupo da sidebar encontrado!</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Aparecer Início</th><th>Posts Específicos</th><th>Total Anúncios</th><th>Ações</th></tr>";
        foreach ($grupos_sidebar as $grupo) {
            echo "<tr>";
            echo "<td>{$grupo['id']}</td>";
            echo "<td>{$grupo['nome']}</td>";
            echo "<td>" . ($grupo['aparecer_inicio'] ? '✅ SIM' : '❌ NÃO') . "</td>";
            echo "<td>" . ($grupo['posts_especificos'] ? '✅ SIM' : '❌ NÃO') . "</td>";
            echo "<td>{$grupo['total_anuncios']}</td>";
            echo "<td>";
            echo "<a href='editar-grupo-anuncios.php?id={$grupo['id']}' class='btn btn-sm btn-primary'>Editar</a> ";
            echo "<a href='?configurar={$grupo['id']}' class='btn btn-sm btn-success'>Configurar Posts</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 2. Processar configuração se solicitado
    if (isset($_GET['configurar']) && is_numeric($_GET['configurar'])) {
        $grupo_id = (int)$_GET['configurar'];
        
        echo "<h3>2. Configurando grupo ID: $grupo_id</h3>";
        
        // Buscar posts disponíveis
        $posts = $dbManager->query("
            SELECT id, titulo, slug FROM posts 
            WHERE status = 'publicado' 
            ORDER BY data_publicacao DESC
        ");
        
        // Buscar posts já associados
        $posts_associados = $dbManager->query("
            SELECT post_id FROM grupos_anuncios_posts 
            WHERE grupo_id = ?
        ", [$grupo_id]);
        
        $posts_ids_associados = array_column($posts_associados, 'post_id');
        
        echo "<form method='POST'>";
        echo "<input type='hidden' name='grupo_id' value='$grupo_id'>";
        echo "<div class='mb-3'>";
        echo "<label class='form-label'><strong>Selecione os posts onde este grupo deve aparecer:</strong></label>";
        echo "<div style='max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;'>";
        
        foreach ($posts as $post) {
            $checked = in_array($post['id'], $posts_ids_associados) ? 'checked' : '';
            echo "<div class='form-check'>";
            echo "<input class='form-check-input' type='checkbox' name='posts[]' value='{$post['id']}' id='post_{$post['id']}' $checked>";
            echo "<label class='form-check-label' for='post_{$post['id']}'>";
            echo "<strong>{$post['titulo']}</strong> (ID: {$post['id']})";
            echo "</label>";
            echo "</div>";
        }
        
        echo "</div>";
        echo "</div>";
        
        echo "<div class='mb-3'>";
        echo "<div class='form-check'>";
        echo "<input class='form-check-input' type='checkbox' name='aparecer_inicio' id='aparecer_inicio' value='1' " . ($grupo['aparecer_inicio'] ? 'checked' : '') . ">";
        echo "<label class='form-check-label' for='aparecer_inicio'>";
        echo "<strong>Aparecer também na página inicial</strong>";
        echo "</label>";
        echo "</div>";
        echo "</div>";
        
        echo "<button type='submit' class='btn btn-primary'>Salvar Configuração</button>";
        echo "<a href='?' class='btn btn-secondary'>Cancelar</a>";
        echo "</form>";
    }
    
    // 3. Processar formulário
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grupo_id'])) {
        $grupo_id = (int)$_POST['grupo_id'];
        $posts_selecionados = $_POST['posts'] ?? [];
        $aparecer_inicio = isset($_POST['aparecer_inicio']) ? 1 : 0;
        
        try {
            $dbManager->beginTransaction();
            
            // Remover associações existentes
            $dbManager->execute("DELETE FROM grupos_anuncios_posts WHERE grupo_id = ?", [$grupo_id]);
            
            // Adicionar novas associações
            if (!empty($posts_selecionados)) {
                foreach ($posts_selecionados as $post_id) {
                    $dbManager->execute("
                        INSERT INTO grupos_anuncios_posts (grupo_id, post_id, criado_em) 
                        VALUES (?, ?, NOW())
                    ", [$grupo_id, $post_id]);
                }
            }
            
            // Atualizar configuração de aparecer na página inicial
            $dbManager->execute("
                UPDATE grupos_anuncios SET aparecer_inicio = ? WHERE id = ?
            ", [$aparecer_inicio, $grupo_id]);
            
            $dbManager->commit();
            
            echo "<div class='alert alert-success'>";
            echo "✅ Configuração salva com sucesso!";
            echo "<br>Posts associados: " . count($posts_selecionados);
            echo "<br>Aparecer na página inicial: " . ($aparecer_inicio ? 'SIM' : 'NÃO');
            echo "</div>";
            
        } catch (Exception $e) {
            $dbManager->rollback();
            echo "<div class='alert alert-danger'>❌ Erro ao salvar: " . $e->getMessage() . "</div>";
        }
    }
    
    // 4. Mostrar resumo final
    echo "<h3>3. Resumo da configuração:</h3>";
    $resumo = $dbManager->query("
        SELECT g.nome, g.aparecer_inicio, COUNT(gap.post_id) as total_posts,
               GROUP_CONCAT(p.titulo SEPARATOR ', ') as posts_titulos
        FROM grupos_anuncios g
        LEFT JOIN grupos_anuncios_posts gap ON g.id = gap.grupo_id
        LEFT JOIN posts p ON gap.post_id = p.id
        WHERE g.localizacao = 'sidebar' AND g.ativo = 1
        GROUP BY g.id
    ");
    
    if (empty($resumo)) {
        echo "<p style='color: orange;'>⚠️ Nenhum grupo da sidebar configurado.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Grupo</th><th>Aparecer Início</th><th>Posts Configurados</th><th>Lista de Posts</th></tr>";
        foreach ($resumo as $item) {
            echo "<tr>";
            echo "<td>{$item['nome']}</td>";
            echo "<td>" . ($item['aparecer_inicio'] ? '✅ SIM' : '❌ NÃO') . "</td>";
            echo "<td>{$item['total_posts']}</td>";
            echo "<td>" . ($item['posts_titulos'] ?: 'Nenhum') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<br><a href='anuncios.php'>← Voltar para Anúncios</a>";
?>
