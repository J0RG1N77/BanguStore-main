<?php
session_start();
require_once __DIR__ . '/db.php';

// Endpoint de login para produção: aceita apenas POST e utiliza redirects
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pag-login.html');
    exit;
}

$login = trim($_POST['login'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($login) || empty($senha)) {
        $_SESSION['error'] = 'Preencha login e senha.';
        header('Location: ../pag-login.html');
        exit;
    }

    try {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT id, nome_completo, senha FROM usuarios WHERE login = :login LIMIT 1');
        $stmt->execute([':login' => $login]);
        $user = $stmt->fetch();

        $authOk = false;
        if ($user) {
            $hash = $user['senha'] ?? '';
            if ($hash && password_verify($senha, $hash)) {
                $authOk = true;
            }
        }

        if (!$authOk) {
            // Log detalhado no servidor para depuração local (não expor ao usuário)
            $userExists = $user ? 'yes' : 'no';
            $hashPresent = !empty($user['senha']) ? 'yes' : 'no';
            error_log("[login] failed login='$login' user_exists=$userExists hash_present=$hashPresent");

            $_SESSION['error'] = 'Usuário ou senha incorretos.';
            header('Location: ../pag-login.html');
            exit;
        }

        // Sucesso: armazenar dados essenciais na sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nome_completo'];

        header('Location: ../index.html');
        exit;
    } catch (Exception $e) {
        error_log('[login] exception: ' . $e->getMessage());
        $_SESSION['error'] = 'Erro ao processar login.';
        header('Location: ../pag-login.html');
        exit;
    }
