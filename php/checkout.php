<?php
session_start();
require_once '../php/db.php'; // Ajuste o caminho conforme necessário

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Erro desconhecido.'];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Usuário não logado.';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $cart_data_json = $_POST['cart_data'] ?? '[]';
    $address_id = $_POST['address_id'] ?? null;
    $email = $_POST['email'] ?? ''; // Re-adicionando a variável email

    $cart_data = json_decode($cart_data_json, true); // Decodificando os dados do carrinho

    if (empty($cart_data) || !is_array($cart_data)) {
        $response['message'] = 'Dados do carrinho inválidos.';
        echo json_encode($response);
        exit();
    }

    if (empty($address_id)) {
        $response['message'] = 'ID do endereço inválido.';
        echo json_encode($response);
        exit();
    }

    // Calcular o valor total
    $total_value = 0;
    foreach ($cart_data as $item) {
        $total_value += ($item['qty'] ?? 0) * ($item['price'] ?? 0);
    }

    $status = 'Aprovado'; // Status inicial
    $order_date = date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        // Inserir na tabela de pedidos
        $stmt = $pdo->prepare("INSERT INTO pedidos (user_id, order_date, total_value, status, address_id, cart_data, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $order_date, $total_value, $status, $address_id, $cart_data_json, $email]);
        $order_id = $pdo->lastInsertId();

        // Inserir itens do pedido (se houver uma tabela separada para isso)
        // Exemplo:
        // foreach ($cart_data as $item) {
        //     $stmt_item = $pdo->prepare("INSERT INTO pedido_itens (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
        //     $stmt_item->execute([$order_id, $item['name'], $item['qty'], $item['price']]);
        // }

        $pdo->commit();
        $response['success'] = true;
        $response['message'] = 'Pedido realizado com sucesso!';
        $response['order_id'] = $order_id;

    } catch (PDOException $e) {
        $pdo->rollBack();
        $response['message'] = 'Erro ao processar o pedido: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
