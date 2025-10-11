<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

// Se não há sessão ativa, retornar logged:false
if (empty($_SESSION['user_id'])) {
    echo json_encode(['logged' => false]);
    exit;
}

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT id, nome_completo, email, login, celular FROM usuarios WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['logged' => false]);
        exit;
    }

    echo json_encode([
        'logged' => true,
        'user' => [
            'id' => $user['id'],
            'nome_completo' => $user['nome_completo'],
            'email' => $user['email'],
            'login' => $user['login'] ?? null,
            'celular' => $user['celular'] ?? null
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
} catch (Exception $e) {
    error_log('[current_user] exception: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['logged' => false, 'error' => 'server_error']);
    exit;
}
