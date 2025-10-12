<?php
session_start();
require_once 'db.php'; // Ajuste o caminho conforme necessário

$pdo = getPDO(); // Inicializa a variável $pdo

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Erro desconhecido.'];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Usuário não logado.';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];

    // Coletar e validar dados do endereço
    $cep = $_POST['cep'] ?? '';
    $logradouro = $_POST['logradouro'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $complemento = $_POST['complemento'] ?? null;
    $bairro = $_POST['bairro'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';

    // Validação básica (pode ser expandida)
    if (empty($cep) || empty($logradouro) || empty($numero) || empty($bairro) || empty($cidade) || empty($estado)) {
        $response['message'] = 'Todos os campos obrigatórios do endereço devem ser preenchidos.';
        echo json_encode($response);
        exit();
    }

    try {
        // Inserir na tabela de endereços
        $stmt = $pdo->prepare(
            "INSERT INTO enderecos (cep, logradouro, numero, complemento, bairro, cidade, estado, usuarios_id_usuario) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$cep, $logradouro, $numero, $complemento, $bairro, $cidade, $estado, $user_id]);
        $address_id = $pdo->lastInsertId();

        $response['success'] = true;
        $response['message'] = 'Endereço salvo com sucesso!';
        $response['address_id'] = $address_id;

    } catch (PDOException $e) {
        $response['message'] = 'Erro ao salvar o endereço: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Método de requisição inválido.';
}

echo json_encode($response);