<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Usuário não autenticado.';
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if ($data === null) {
    $response['message'] = 'Dados inválidos.';
    echo json_encode($response);
    exit();
}

$nomeCompleto = $data['nomeCompleto'] ?? '';
$login = $data['login'] ?? '';
$celular = $data['celular'] ?? '';
$email = $data['email'] ?? '';

// Validação básica
if (empty($nomeCompleto) || empty($login) || empty($celular) || empty($email)) {
    $response['message'] = 'Todos os campos são obrigatórios.';
    echo json_encode($response);
    exit();
}

// Atualizar dados no banco de dados
try {
    $pdo = getPDO(); // Adicione esta linha
    $stmt = $pdo->prepare("UPDATE usuarios SET nome_completo = ?, login = ?, celular = ?, email = ? WHERE id = ?");

    $stmt->execute([$nomeCompleto, $login, $celular, $email, $user_id]);

    if ($stmt->rowCount() > 0) {
        $response['success'] = true;
        $response['message'] = 'Dados atualizados com sucesso.';
    } else {
        $response['message'] = 'Nenhum dado foi alterado ou usuário não encontrado.';
    }
} catch (PDOException $e) {
    $response['message'] = 'Erro no banco de dados: ' . $e->getMessage();
}

echo json_encode($response);
?>