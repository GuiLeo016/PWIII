<?php

session_start();

require_once 'conexao.php';

$mensagem = "";
$tipo_mensagem = "";


/*
|--------------------------------------------------------------------------
| PROCESSAMENTO DO LOGIN
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = trim($_POST["username"] ?? "");
    $senha = $_POST["user-password"] ?? "";

    if ($login === "" || $senha === "") {

        $mensagem = "Preencha o usuário e a senha.";
        $tipo_mensagem = "erro";

    } else {

        $sql = "SELECT
                    id_usuario,
                    login_usuario,
                    senha_usuario,
                    tipo_usuario
                FROM tb_usuario
                WHERE login_usuario = :login
                LIMIT 1";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":login" => $login
        ]);

        $usuario = $stmt->fetch();

        if ($usuario && $senha === $usuario["senha_usuario"]) {

            session_regenerate_id(true);

            $_SESSION["id_usuario"] = $usuario["id_usuario"];
            $_SESSION["login_usuario"] = $usuario["login_usuario"];
            $_SESSION["tipo_usuario"] = $usuario["tipo_usuario"];


            /*
            |--------------------------------------------------------------------------
            | REGISTRA LOGIN
            |--------------------------------------------------------------------------
            */

            $sqlLog = "INSERT INTO tb_log (
                            usuario_log,
                            acao_log,
                            descricao_log,
                            data_log,
                            hora_log
                       )
                       VALUES (
                            :usuario,
                            'LOGIN',
                            'Usuário realizou login no sistema',
                            CURDATE(),
                            CURTIME()
                       )";

            $stmtLog = $pdo->prepare($sqlLog);

            $stmtLog->execute([
                ":usuario" => $usuario["id_usuario"]
            ]);


            header("Location: home.php");
            exit;

        } else {

            /*
            |--------------------------------------------------------------------------
            | REGISTRA LOGIN INVÁLIDO
            |--------------------------------------------------------------------------
            */

            $sqlLog = "INSERT INTO tb_log (
                            usuario_log,
                            acao_log,
                            descricao_log,
                            data_log,
                            hora_log
                       )
                       VALUES (
                            NULL,
                            'LOGIN_FALHA',
                            'Tentativa de login com credenciais inválidas',
                            CURDATE(),
                            CURTIME()
                       )";

            $stmtLog = $pdo->prepare($sqlLog);

            $stmtLog->execute();

            $mensagem = "Usuário ou senha incorretos.";
            $tipo_mensagem = "erro";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <link
        rel="stylesheet"
        href="style.css"
    >

    <title>Login - CRUD Mundo</title>

</head>

<body>

    <div id="main">

        <div id="container">

            <h1>Explorador Geográfico</h1>

            <p>
                Faça login para acessar o sistema.
            </p>

            <div class="cascata-container">

                <section class="coluna-lista">

                    <h2>Login</h2>


                    <?php if ($mensagem !== ""): ?>

                        <div class="mensagem <?php echo $tipo_mensagem; ?>">

                            <?php
                            echo htmlspecialchars($mensagem);
                            ?>

                        </div>

                    <?php endif; ?>


                    <form
                        action=""
                        method="post"
                    >

                        <ul>

                            <li>

                                <label for="id-username">
                                    Nome de usuário
                                </label>

                                <input
                                    type="text"
                                    name="username"
                                    id="id-username"
                                    maxlength="30"
                                    required
                                    autocomplete="username"
                                >

                            </li>


                            <li>

                                <label for="id-user-password">
                                    Senha
                                </label>

                                <input
                                    type="password"
                                    name="user-password"
                                    id="id-user-password"
                                    required
                                    autocomplete="current-password"
                                >

                            </li>


                            <li>

                                <input
                                    type="submit"
                                    value="ENTRAR"
                                    id="btn-logar"
                                    class="btn-salvar"
                                >

                            </li>

                        </ul>

                    </form>

                </section>

            </div>

        </div>

    </div>

</body>

</html>