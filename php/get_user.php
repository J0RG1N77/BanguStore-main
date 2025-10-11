<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

$response = ['success' => false, 'message' => '', 'user' => null];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Usuário não autenticado.';
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT id, nome_completo, cpf, telefone_celular, email FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $response['success'] = true;
        $response['user'] = [
            'id' => $user['id'],
            'nome_completo' => $user['nome_completo'],
            'cpf' => $user['cpf'],
            'telefone_celular' => $user['telefone_celular'],
            'email' => $user['email']
        ];
    } else {
        $response['message'] = 'Usuário não encontrado.';
    }
} catch (PDOException $e) {
    $response['message'] = 'Erro no banco de dados: ' . $e->getMessage();
}

echo json_encode($response);
?>