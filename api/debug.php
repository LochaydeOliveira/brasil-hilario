<?php
header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'message' => 'API está funcionando',
    'timestamp' => date('Y-m-d H:i:s'),
    'server' => $_SERVER['SERVER_NAME'] ?? 'unknown',
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown'
]);
?> 