<?php
// php/logout.php - destruir sessão e retornar JSON
session_start();
// Limpar variáveis de sessão
$_SESSION = [];
// Apagar cookie de sessão se existir
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}
// Destruir sessão
session_destroy();

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['ok' => true, 'logged' => false]);
exit;
