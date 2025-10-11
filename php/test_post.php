<?php
// test_post.php - debug endpoint que retorna o POST recebido em JSON
header('Content-Type: application/json; charset=utf-8');
http_response_code(200);

echo json_encode([
    'method' => $_SERVER['REQUEST_METHOD'],
    'query' => $_GET,
    'post' => $_POST,
    'raw' => file_get_contents('php://input')
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
