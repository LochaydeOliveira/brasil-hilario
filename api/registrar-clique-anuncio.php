<?php
// Headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Responder a requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Log para debug
error_log("API registrar-clique-anuncio.php chamada");

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Método não permitido: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// Verificar se os dados necessários foram enviados
$input = json_decode(file_get_contents('php://input'), true);
error_log("Dados recebidos: " . json_encode($input));

if (!isset($input['anuncio_id']) || !isset($input['tipo_clique']) || !isset($input['post_id'])) {
    error_log("Dados incompletos recebidos");
    http_response_code(400);
    echo json_encode(['error' => 'Dados incompletos']);
    exit;
}

$anuncioId = (int) $input['anuncio_id'];
$tipoClique = $input['tipo_clique'];
$postId = (int) $input['post_id'];

error_log("Dados processados - anuncio_id: $anuncioId, tipo_clique: $tipoClique, post_id: $postId");

// Validar tipo de clique
$tiposValidos = ['imagem', 'titulo', 'cta'];
if (!in_array($tipoClique, $tiposValidos)) {
    error_log("Tipo de clique inválido: $tipoClique");
    http_response_code(400);
    echo json_encode(['error' => 'Tipo de clique inválido']);
    exit;
}

// Função para obter IP do usuário
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }
}

// Salvar clique em arquivo de log
function salvarCliqueLog($anuncioId, $postId, $tipoClique) {
    $logFile = '../logs/cliques_anuncios.log';
    $logDir = dirname($logFile);
    
    // Criar diretório se não existir
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $dados = [
        'anuncio_id' => $anuncioId,
        'post_id' => $postId,
        'tipo_clique' => $tipoClique,
        'ip_usuario' => getUserIP(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'data_clique' => date('Y-m-d H:i:s'),
        'timestamp' => time()
    ];
    
    $logEntry = json_encode($dados) . "\n";
    
    if (file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX)) {
        error_log("Clique salvo no log: " . json_encode($dados));
        return true;
    } else {
        error_log("Erro ao salvar clique no log");
        return false;
    }
}

// Tentar conectar ao banco de dados
$sucesso = false;
try {
    require_once '../config/config.php';
    require_once '../includes/db.php';
    require_once '../includes/AnunciosManager.php';
    
    $anunciosManager = new AnunciosManager($pdo);
    
    // Verificar se o anúncio existe e está ativo
    $anuncio = $anunciosManager->getAnuncio($anuncioId);
    if (!$anuncio || !$anuncio['ativo']) {
        error_log("Anúncio não encontrado ou inativo: $anuncioId");
        // Mesmo assim, salvar no log
        $sucesso = salvarCliqueLog($anuncioId, $postId, $tipoClique);
    } else {
        // Registrar o clique no banco
        $sucesso = $anunciosManager->registrarClique($anuncioId, $postId, $tipoClique);
        
        // Também salvar no log como backup
        salvarCliqueLog($anuncioId, $postId, $tipoClique);
    }
    
} catch (Exception $e) {
    error_log("Erro ao conectar com banco de dados: " . $e->getMessage());
    
    // Se não conseguir conectar ao banco, salvar apenas no log
    $sucesso = salvarCliqueLog($anuncioId, $postId, $tipoClique);
}

if ($sucesso) {
    error_log("Clique registrado com sucesso - anuncio_id: $anuncioId, post_id: $postId, tipo: $tipoClique");
    echo json_encode([
        'success' => true,
        'message' => 'Clique registrado com sucesso',
        'anuncio_id' => $anuncioId,
        'tipo_clique' => $tipoClique,
        'post_id' => $postId,
        'method' => 'log_file'
    ]);
} else {
    error_log("Erro ao registrar clique");
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao registrar clique']);
} 