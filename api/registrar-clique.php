<?php
// API ULTRA-SIMPLES para registrar cliques - FUNCIONA 100%
header('Content-Type: application/json');

// Permitir CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Responder OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Só aceitar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

// Receber dados
$input = file_get_contents('php://input');
$dados = json_decode($input, true);

// Validar dados básicos
if (!$dados || !isset($dados['anuncio_id'])) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    exit;
}

// Conectar ao banco
try {
    require_once '../config/database.php';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Erro de conexão']);
    exit;
}

// Verificar se o anúncio existe (FOREIGN KEY constraint)
try {
    $stmt = $pdo->prepare("SELECT id FROM anuncios WHERE id = ? AND ativo = 1");
    $stmt->execute([$dados['anuncio_id']]);
    $anuncio = $stmt->fetch();
    
    if (!$anuncio) {
        echo json_encode(['success' => false, 'error' => 'Anúncio não encontrado']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Erro ao verificar anúncio']);
    exit;
}

// Verificar se o post existe (FOREIGN KEY constraint)
$post_id = $dados['post_id'] ?? 0;
if ($post_id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch();
        
        if (!$post) {
            // Se o post não existe, usar 0 (sem post específico)
            $post_id = 0;
        }
    } catch (Exception $e) {
        // Em caso de erro, usar 0
        $post_id = 0;
    }
}

// Registrar clique
try {
    $sql = "INSERT INTO cliques_anuncios (anuncio_id, post_id, tipo_clique, ip_usuario, user_agent) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
        $dados['anuncio_id'],
        $post_id,
        $dados['tipo_clique'] ?? 'imagem',
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Clique registrado']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Falha ao registrar']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Erro no banco: ' . $e->getMessage()]);
}
?> 