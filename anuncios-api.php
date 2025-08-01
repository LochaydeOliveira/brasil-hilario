<?php
// Verificar se é uma requisição AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($isAjax) {
    header('Content-Type: application/json');
    
    require_once 'config/config.php';
    require_once 'includes/db.php';
    require_once 'includes/AnunciosManager.php';

    // Verificar se é uma requisição GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        exit;
    }

    // Obter parâmetros
    $localizacao = $_GET['localizacao'] ?? '';
    $postId = (int) ($_GET['post_id'] ?? 0);

    // Validar localização
    $localizacoesValidas = ['sidebar', 'conteudo'];
    if (!in_array($localizacao, $localizacoesValidas)) {
        http_response_code(400);
        echo json_encode(['error' => 'Localização inválida']);
        exit;
    }

    try {
        $anunciosManager = new AnunciosManager($pdo);
        
        // Buscar anúncios para a localização
        $anuncios = $anunciosManager->getAnunciosPorLocalizacao($localizacao, $postId > 0 ? $postId : null);
        
        // Gerar HTML para cada anúncio
        $anunciosComHTML = [];
        foreach ($anuncios as $anuncio) {
            $anunciosComHTML[] = [
                'id' => $anuncio['id'],
                'titulo' => $anuncio['titulo'],
                'imagem' => $anuncio['imagem'],
                'link_compra' => $anuncio['link_compra'],
                'cta_ativo' => $anuncio['cta_ativo'],
                'cta_texto' => $anuncio['cta_texto'],
                'html' => $anunciosManager->gerarHTMLAnuncio($anuncio)
            ];
        }
        
        echo json_encode([
            'success' => true,
            'anuncios' => $anunciosComHTML,
            'total' => count($anunciosComHTML)
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro interno do servidor']);
    }
    exit;
}

// Se não for AJAX, mostrar página normal
?>
<!DOCTYPE html>
<html>
<head>
    <title>API de Anúncios</title>
</head>
<body>
    <h1>API de Anúncios</h1>
    <p>Este endpoint deve ser acessado via AJAX.</p>
</body>
</html> 