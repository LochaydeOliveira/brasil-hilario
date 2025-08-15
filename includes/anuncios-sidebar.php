<?php
/**
 * SISTEMA DE ANÚNCIOS DA SIDEBAR - VERSÃO NOVA
 * 
 * Funcionalidade: Exibir anúncios da sidebar APENAS em posts específicos
 * 
 * Como funciona:
 * 1. Verifica se estamos em um post específico
 * 2. Busca anúncios configurados para sidebar
 * 3. Verifica se o post atual está na lista de posts permitidos
 * 4. Exibe apenas se o post estiver configurado
 */

// Verificar se estamos em um post específico
if (!isset($post) || !isset($post['id'])) {
    // Não estamos em um post específico, não exibir anúncios
    return;
}

$postId = $post['id'];

try {
    // Buscar anúncios da sidebar que estão ativos
    $sql = "
        SELECT a.id, a.titulo, a.imagem, a.link_compra, a.marca
        FROM anuncios a
        WHERE a.localizacao = 'sidebar' 
        AND a.ativo = 1
        ORDER BY a.criado_em DESC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($anuncios)) {
        return; // Nenhum anúncio da sidebar ativo
    }
    
    // Para cada anúncio, verificar se está configurado para este post
    foreach ($anuncios as $anuncio) {
        // Verificar se este anúncio está configurado para aparecer neste post
        $sql_check = "
            SELECT COUNT(*) as total
            FROM anuncios_posts ap
            WHERE ap.anuncio_id = ? AND ap.post_id = ?
        ";
        
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$anuncio['id'], $postId]);
        $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        // Se não há configuração específica, não exibir
        if ($result['total'] == 0) {
            continue;
        }
        
        // Exibir o anúncio
        echo '<li class="mb-3 anuncio-item">';
        echo '<div class="anuncio-card-sidebar">';
        echo '<div class="anuncio-patrocinado-badge-sidebar">Anúncio</div>';
        
        // Badge da marca
        if (!empty($anuncio['marca'])) {
            echo '<div class="marca-badge">';
            if ($anuncio['marca'] === 'shopee') {
                echo '<img src="https://brasilhilario.com.br/assets/img/logo-shopee.png" alt="Shopee">';
            } elseif ($anuncio['marca'] === 'amazon') {
                echo '<img src="https://brasilhilario.com.br/assets/img/logo-amazon.png" alt="Amazon">';
            }
            echo '</div>';
        }
        
        // Imagem do anúncio
        if (!empty($anuncio['imagem'])) {
            echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" onclick="registrarCliqueAnuncio(' . $anuncio['id'] . ', \'imagem\')">';
            echo '<img src="' . htmlspecialchars($anuncio['imagem']) . '" alt="' . htmlspecialchars($anuncio['titulo']) . '" class="anuncio-imagem-sidebar">';
            echo '</a>';
        }
        
        // Título do anúncio
        echo '<a href="' . htmlspecialchars($anuncio['link_compra']) . '" target="_blank" style="font-size: 15px!important;padding: 0 0.9rem!important;font-weight: 500!important;" class="anuncio-titulo-sidebar" onclick="registrarCliqueAnuncio(' . $anuncio['id'] . ', \'titulo\')">' . htmlspecialchars($anuncio['titulo']) . '</a>';
        
        echo '</div>';
        echo '</li>';
    }
    
} catch (Exception $e) {
    // Silenciar erros para não afetar o site
    error_log("Erro ao carregar anúncios da sidebar: " . $e->getMessage());
}
?> 