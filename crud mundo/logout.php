<?php

session_start();


/*
|--------------------------------------------------------------------------
| REGISTRA O LOGOUT (SE HOUVER USUÁRIO LOGADO)
|--------------------------------------------------------------------------
*/

if (isset($_SESSION["id_usuario"])) {

    require_once 'conexao.php';

    $sqlLog = "INSERT INTO tb_log (
                    usuario_log,
                    acao_log,
                    descricao_log,
                    data_log,
                    hora_log
               )
               VALUES (
                    :usuario,
                    'LOGOUT',
                    'Usuário encerrou a sessão no sistema',
                    CURDATE(),
                    CURTIME()
               )";

    $stmtLog = $pdo->prepare($sqlLog);

    $stmtLog->execute([
        ":usuario" => $_SESSION["id_usuario"]
    ]);
}


/*
|--------------------------------------------------------------------------
| ENCERRA A SESSÃO
|--------------------------------------------------------------------------
*/

$_SESSION = [];

session_unset();
session_destroy();

if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

header("Location: index.php");
exit;
