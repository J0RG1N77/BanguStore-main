<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'db.php';

$pdo = getPDO();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Erro desconhecido.'];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Usuário não autenticado.';
    echo json_encode($response);
    exit();
}

$userId = $_SESSION['user_id'];

// Obter o corpo da requisição JSON
$input = file_get_contents('php://input');
$addressData = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    $response['message'] = 'Dados JSON inválidos.';
    echo json_encode($response);
    exit();
}

// Validação básica dos dados recebidos
$requiredFields = ['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado'];
foreach ($requiredFields as $field) {
    if (empty($addressData[$field])) {
        $response['message'] = 'Campo obrigatório faltando: ' . $field;
        echo json_encode($response);
        exit();
    }
}

$cep = $addressData['cep'];
$logradouro = $addressData['logradouro']; // Usando 'logradouro' diretamente, conforme o banco de dados
$numero = $addressData['numero'];
$complemento = $addressData['complemento'] ?? null;
$bairro = $addressData['bairro'];
$cidade = $addressData['cidade'];
$estado = $addressData['estado'];

try {
    // Verificar se o endereço já existe para o usuário
    $stmt = $pdo->prepare("SELECT id_endereco FROM enderecos WHERE usuarios_id_usuario = ? AND cep = ? AND logradouro = ? AND numero = ? AND bairro = ? AND cidade = ? AND estado = ?");
    $stmt->execute([$userId, $cep, $logradouro, $numero, $bairro, $cidade, $estado]);
    $existingAddress = $stmt->fetch();

    if ($existingAddress) {
        $response['success'] = true;
        $response['message'] = 'Endereço já cadastrado.';
    } else {
        // Inserir novo endereço
        $stmt = $pdo->prepare("INSERT INTO enderecos (usuarios_id_usuario, cep, logradouro, numero, complemento, bairro, cidade, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $cep, $logradouro, $numero, $complemento, $bairro, $cidade, $estado]);

        if ($stmt->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = 'Endereço salvo com sucesso!';
        } else {
            $response['message'] = 'Falha ao salvar o endereço.';
        }
    }
} catch (PDOException $e) {
    $response['message'] = 'Erro no banco de dados: ' . $e->getMessage();
}

echo json_encode($response);
?>