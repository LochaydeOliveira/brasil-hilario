<?php
// API SIMPLES e ROBUSTA para registrar cliques
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Responder a requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

// Receber dados
$input = file_get_contents('php://input');
$dados = json_decode($input, true);

// Validar dados
if (!$dados || !isset($dados['anuncio_id'])) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    exit;
}

try {
    // Conectar ao banco
    require_once '../config/database.php';
    
    // Verificar se o anúncio existe
    $stmt = $pdo->prepare("SELECT id FROM anuncios WHERE id = ? AND ativo = 1");
    $stmt->execute([$dados['anuncio_id']]);
    $anuncio = $stmt->fetch();
    
    if (!$anuncio) {
        echo json_encode(['success' => false, 'error' => 'Anúncio não encontrado']);
        exit;
    }
    
    // Registrar clique
    $sql = "INSERT INTO cliques_anuncios (anuncio_id, post_id, tipo_clique, ip_usuario, user_agent) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
        $dados['anuncio_id'],
        $dados['post_id'] ?? 0,
        $dados['tipo_clique'] ?? 'imagem',
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Clique registrado']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao registrar clique']);
    }
    
} catch (Exception $e) {
    error_log("Erro na API de cliques: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Erro no servidor']);
}
?> 