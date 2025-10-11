<?php
// setup_db.php - cria o banco 'bangustore' e executa php/init_db.sql
// Acesse este script via navegador (http://localhost/BanguStore2025/php/setup_db.php) ou CLI

header('Content-Type: text/plain; charset=utf-8');
$host = '127.0.0.1';
$port = 3306;
$user = 'root';
$pass = '';
$dbName = 'bangustore';
$sqlFile = __DIR__ . '/init_db.sql';

try {
    // Conectar ao servidor MySQL sem selecionar banco
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Criar o banco se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $dbName . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Banco '$dbName' criado ou já existe.\n";

    // Selecionar o banco
    $pdo->exec("USE `" . $dbName . "`");

    // Ler arquivo SQL
    if (!file_exists($sqlFile)) {
        throw new Exception("Arquivo SQL não encontrado: $sqlFile");
    }
    $sql = file_get_contents($sqlFile);
    if ($sql === false) {
        throw new Exception("Falha ao ler o arquivo SQL: $sqlFile");
    }

    // Separar statements por ponto e vírgula e executar cada um
    $stmts = array_filter(array_map('trim', explode(';', $sql)));
    $executed = 0;
    foreach ($stmts as $stmt) {
        if ($stmt === '') continue;
        $pdo->exec($stmt);
        $executed++;
    }

    echo "Arquivo SQL executado. Statements executados: $executed\n";
    echo "Pronto.\n";
} catch (PDOException $e) {
    echo "Erro PDO: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}
