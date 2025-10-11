<?php
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pag-cadastro.html');
    exit;
}

$nome = trim($_POST['nome_completo'] ?? '');
    $data_nasc = $_POST['data_nascimento'] ?? null;
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $sexo = $_POST['sexo'] ?? null;
    $celular = preg_replace('/\D/', '', $_POST['celular'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($login) || empty($senha) || !$email) {
        $msg = urlencode('Preencha todos os campos obrigatórios corretamente.');
        header('Location: ../pag-cadastro.html?error=' . $msg);
        exit;
    }

    try {
        $pdo = getPDO();

        // checar login ou email duplicado
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE login = :login OR email = :email LIMIT 1');
        $stmt->execute([':login' => $login, ':email' => $email]);
        if ($stmt->fetch()) {
            $msg = urlencode('Login ou email já cadastrado.');
            header('Location: ../pag-cadastro.html?error=' . $msg);
            exit;
        }

        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $insert = $pdo->prepare('INSERT INTO usuarios (nome_completo, data_nascimento, email, sexo, celular, login, senha) VALUES (:nome, :nasc, :email, :sexo, :celular, :login, :senha)');
        $ok = $insert->execute([
            ':nome' => $nome,
            ':nasc' => $data_nasc ?: null,
            ':email' => $email,
            ':sexo' => $sexo,
            ':celular' => $celular,
            ':login' => $login,
            ':senha' => $hash,
        ]);

    if ($ok) {
        $msg = urlencode('Cadastro realizado com sucesso. Faça login.');
        header('Location: ../pag-login.html?success=' . $msg);
        exit;
    } else {
        $msg = urlencode('Erro ao cadastrar usuário.');
        header('Location: ../pag-cadastro.html?error=' . $msg);
        exit;
    }
} catch (Exception $e) {
    $msg = urlencode('Erro interno: ' . $e->getMessage());
    header('Location: ../pag-cadastro.html?error=' . $msg);
    exit;
}
