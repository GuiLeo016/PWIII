<?php

$host = "localhost";
$dbname = "db_mundo";
$user = "root";
$password = "";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {

    die("Erro ao conectar ao banco de dados.");
}


/*
|--------------------------------------------------------------------------
| CRIA O USUÁRIO ADMINISTRADOR PADRÃO NA PRIMEIRA EXECUÇÃO
|--------------------------------------------------------------------------
| Sempre que o sistema roda e a tabela tb_usuario está vazia (banco recém
| criado), um usuário "admin" com senha "1234" é criado automaticamente,
| já com a senha em hash (password_hash). Depois da primeira execução,
| tb_usuario deixa de estar vazia e este bloco não roda mais.
*/

try {

    $totalUsuarios = (int) $pdo->query(
        "SELECT COUNT(*) FROM tb_usuario"
    )->fetchColumn();

    if ($totalUsuarios === 0) {

        $senhaHash = password_hash("1234", PASSWORD_DEFAULT);

        $stmtAdmin = $pdo->prepare(
            "INSERT INTO tb_usuario (login_usuario, senha_usuario, tipo_usuario)
             VALUES (:login, :senha, :tipo)"
        );

        $stmtAdmin->execute([
            ":login" => "admin",
            ":senha" => $senhaHash,
            ":tipo"  => "A"
        ]);
    }

} catch (PDOException $e) {

    // Se a tabela tb_usuario ainda não existir (banco não importado),
    // não faz nada aqui — os demais scripts vão acusar o erro normalmente.
}
