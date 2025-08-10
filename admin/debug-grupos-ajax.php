<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/GruposAnunciosManager.php';

// Verificar login
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    exit;
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$gruposManager = new GruposAnunciosManager($pdo);

$grupoId = (int)$_POST['grupo_id'];
$postId = $_POST['post_id'] ? (int)$_POST['post_id'] : null;
$isHomePage = (bool)$_POST['is_home_page'];

// Testar o filtro
$resultado = $gruposManager->debugGrupoParaPost($grupoId, $postId, $isHomePage);

// Retornar JSON
header('Content-Type: application/json');
echo json_encode($resultado); 