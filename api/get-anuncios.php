<?php
header('Content-Type: application/json');

// Log para debug
error_log("API get-anuncios.php chamada - " . date('Y-m-d H:i:s'));

try {
    require_once '../config/config.php';
    require_once '../includes/db.php';
    require_once '../includes/AnunciosManager.php';

    // Verificar se é uma requisição GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        error_log("Método não permitido: " . $_SERVER['REQUEST_METHOD']);
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        exit;
    }

    // Obter parâmetros
    $localizacao = $_GET['localizacao'] ?? '';
    $postId = (int) ($_GET['post_id'] ?? 0);

    error_log("Parâmetros recebidos - localizacao: $localizacao, post_id: $postId");

    // Validar localização
    $localizacoesValidas = ['sidebar', 'conteudo'];
    if (!in_array($localizacao, $localizacoesValidas)) {
        error_log("Localização inválida: $localizacao");
        http_response_code(400);
        echo json_encode(['error' => 'Localização inválida']);
        exit;
    }

    $anunciosManager = new AnunciosManager($pdo);
    
    // Buscar anúncios para a localização
    $anuncios = $anunciosManager->getAnunciosPorLocalizacao($localizacao, $postId > 0 ? $postId : null);
    
    error_log("Anúncios encontrados para $localizacao: " . count($anuncios));
    
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
    
    $response = [
        'success' => true,
        'anuncios' => $anunciosComHTML,
        'total' => count($anunciosComHTML),
        'localizacao' => $localizacao,
        'post_id' => $postId
    ];
    
    error_log("Resposta da API: " . json_encode($response));
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Erro ao buscar anúncios: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor', 'message' => $e->getMessage()]);
} 