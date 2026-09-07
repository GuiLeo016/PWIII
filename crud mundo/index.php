<?php

session_start();

require_once 'conexao.php';

$mensagem = "";
$tipo_mensagem = "";


/*
|--------------------------------------------------------------------------
| CONFIGURAÇÕES DO BLOQUEIO POR TENTATIVAS
|--------------------------------------------------------------------------
*/

const TENTATIVAS_MAXIMAS = 3;
const MINUTOS_BLOQUEIO = 5;


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
                    tipo_usuario,
                    senha_temporaria,
                    tentativas_login,
                    bloqueado_ate
                FROM tb_usuario
                WHERE login_usuario = :login
                LIMIT 1";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":login" => $login
        ]);

        $usuario = $stmt->fetch();

        $estaBloqueado = $usuario
            && $usuario["bloqueado_ate"] !== null
            && strtotime($usuario["bloqueado_ate"]) > time();


        /*
        |--------------------------------------------------------------------------
        | CONTA TEMPORARIAMENTE BLOQUEADA
        |--------------------------------------------------------------------------
        */

        if ($estaBloqueado) {

            $minutosRestantes = (int) ceil((strtotime($usuario["bloqueado_ate"]) - time()) / 60);

            $mensagem = "Conta bloqueada temporariamente após várias tentativas incorretas. "
                      . "Tente novamente em aproximadamente {$minutosRestantes} minuto(s).";
            $tipo_mensagem = "erro";

            $sqlLog = "INSERT INTO tb_log (
                            usuario_log, acao_log, descricao_log, data_log, hora_log
                       ) VALUES (
                            :usuario, 'LOGIN_BLOQUEADO', 'Tentativa de login em conta bloqueada', CURDATE(), CURTIME()
                       )";
            $stmtLog = $pdo->prepare($sqlLog);
            $stmtLog->execute([":usuario" => $usuario["id_usuario"]]);


        /*
        |--------------------------------------------------------------------------
        | LOGIN CORRETO
        |--------------------------------------------------------------------------
        */

        } elseif ($usuario && password_verify($senha, $usuario["senha_usuario"])) {

            // Login certo: zera tentativas e qualquer bloqueio pendente
            $stmtReset = $pdo->prepare(
                "UPDATE tb_usuario SET tentativas_login = 0, bloqueado_ate = NULL WHERE id_usuario = :id"
            );
            $stmtReset->execute([":id" => $usuario["id_usuario"]]);

            session_regenerate_id(true);

            $_SESSION["id_usuario"] = $usuario["id_usuario"];
            $_SESSION["login_usuario"] = $usuario["login_usuario"];
            $_SESSION["tipo_usuario"] = $usuario["tipo_usuario"];
            $_SESSION["senha_temporaria"] = (int) $usuario["senha_temporaria"];

            $sqlLog = "INSERT INTO tb_log (
                            usuario_log, acao_log, descricao_log, data_log, hora_log
                       ) VALUES (
                            :usuario, 'LOGIN', 'Usuário realizou login no sistema', CURDATE(), CURTIME()
                       )";
            $stmtLog = $pdo->prepare($sqlLog);
            $stmtLog->execute([":usuario" => $usuario["id_usuario"]]);

            if ($_SESSION["senha_temporaria"] === 1) {
                header("Location: trocar_senha.php");
            } else {
                header("Location: home.php");
            }
            exit;


        /*
        |--------------------------------------------------------------------------
        | LOGIN INCORRETO
        |--------------------------------------------------------------------------
        */

        } else {

            if ($usuario) {

                $novasTentativas = $usuario["tentativas_login"] + 1;

                if ($novasTentativas >= TENTATIVAS_MAXIMAS) {

                    $bloqueadoAte = date("Y-m-d H:i:s", strtotime("+" . MINUTOS_BLOQUEIO . " minutes"));

                    $stmtBloqueio = $pdo->prepare(
                        "UPDATE tb_usuario SET tentativas_login = 0, bloqueado_ate = :bloqueado WHERE id_usuario = :id"
                    );
                    $stmtBloqueio->execute([
                        ":bloqueado" => $bloqueadoAte,
                        ":id" => $usuario["id_usuario"]
                    ]);

                    $mensagem = "Você errou a senha " . TENTATIVAS_MAXIMAS . " vezes seguidas. "
                              . "Sua conta foi bloqueada por " . MINUTOS_BLOQUEIO . " minutos.";

                    $acaoLog = "LOGIN_BLOQUEIO";
                    $descricaoLog = "Conta bloqueada após " . TENTATIVAS_MAXIMAS . " tentativas incorretas seguidas";

                } else {

                    $stmtTentativa = $pdo->prepare(
                        "UPDATE tb_usuario SET tentativas_login = :tentativas WHERE id_usuario = :id"
                    );
                    $stmtTentativa->execute([
                        ":tentativas" => $novasTentativas,
                        ":id" => $usuario["id_usuario"]
                    ]);

                    $restantes = TENTATIVAS_MAXIMAS - $novasTentativas;
                    $mensagem = "Usuário ou senha incorretos. Mais {$restantes} tentativa(s) "
                              . "antes do bloqueio temporário.";

                    $acaoLog = "LOGIN_FALHA";
                    $descricaoLog = "Tentativa de login com senha incorreta";
                }

                $sqlLog = "INSERT INTO tb_log (
                                usuario_log, acao_log, descricao_log, data_log, hora_log
                           ) VALUES (
                                :usuario, :acao, :descricao, CURDATE(), CURTIME()
                           )";
                $stmtLog = $pdo->prepare($sqlLog);
                $stmtLog->execute([
                    ":usuario" => $usuario["id_usuario"],
                    ":acao" => $acaoLog,
                    ":descricao" => $descricaoLog
                ]);

            } else {

                $mensagem = "Usuário ou senha incorretos.";

                $sqlLog = "INSERT INTO tb_log (
                                usuario_log, acao_log, descricao_log, data_log, hora_log
                           ) VALUES (
                                NULL, 'LOGIN_FALHA', 'Tentativa de login com usuário inexistente', CURDATE(), CURTIME()
                           )";
                $stmtLog = $pdo->prepare($sqlLog);
                $stmtLog->execute();
            }

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


                    <p class="link-troca">
                        Não tem uma conta? Peça a um administrador para cadastrar você.
                    </p>

                </section>

            </div>

        </div>

    </div>

</body>

</html>
