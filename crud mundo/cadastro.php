<?php

require_once 'conexao.php';

$mensagem = "";
$tipo_mensagem = "";


/*
|--------------------------------------------------------------------------
| PROCESSAMENTO DO CADASTRO
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = trim($_POST["username"] ?? "");
    $senha = $_POST["user-password"] ?? "";
    $confirmar = $_POST["user-password-confirm"] ?? "";

    if ($login === "" || $senha === "" || $confirmar === "") {

        $mensagem = "Preencha todos os campos.";
        $tipo_mensagem = "erro";

    } elseif (mb_strlen($login) > 30) {

        $mensagem = "O nome de usuário pode ter no máximo 30 caracteres.";
        $tipo_mensagem = "erro";

    } elseif (mb_strlen($senha) < 4) {

        $mensagem = "A senha deve ter pelo menos 4 caracteres.";
        $tipo_mensagem = "erro";

    } elseif ($senha !== $confirmar) {

        $mensagem = "As senhas não coincidem.";
        $tipo_mensagem = "erro";

    } else {

        /*
        |--------------------------------------------------------------------------
        | VERIFICA SE O USUÁRIO JÁ EXISTE
        |--------------------------------------------------------------------------
        */

        $sqlVerifica = "SELECT id_usuario FROM tb_usuario WHERE login_usuario = :login LIMIT 1";
        $stmtVerifica = $pdo->prepare($sqlVerifica);
        $stmtVerifica->execute([":login" => $login]);

        if ($stmtVerifica->fetch()) {

            $mensagem = "Este nome de usuário já está em uso.";
            $tipo_mensagem = "erro";

        } else {

            try {

                /*
                |--------------------------------------------------------------------------
                | CADASTRA O NOVO USUÁRIO
                |--------------------------------------------------------------------------
                | A senha nunca é gravada em texto puro: password_hash() gera um hash
                | (com salt embutido) usando o algoritmo padrão do PHP. O login compara
                | com password_verify(), então o hash original nunca precisa ser revertido.
                */

                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                $sql = "INSERT INTO tb_usuario (login_usuario, senha_usuario, tipo_usuario)
                        VALUES (:login, :senha, :tipo)";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ":login" => $login,
                    ":senha" => $senhaHash,
                    ":tipo"  => "U"
                ]);

                $id_novo_usuario = $pdo->lastInsertId();


                /*
                |--------------------------------------------------------------------------
                | REGISTRA O CADASTRO NO LOG
                |--------------------------------------------------------------------------
                */

                $sqlLog = "INSERT INTO tb_log (
                                usuario_log,
                                acao_log,
                                tabela_log,
                                id_registro,
                                descricao_log,
                                data_log,
                                hora_log
                           )
                           VALUES (
                                :usuario,
                                'CADASTRO',
                                'tb_usuario',
                                :id_registro,
                                'Novo usuário cadastrado no sistema',
                                CURDATE(),
                                CURTIME()
                           )";

                $stmtLog = $pdo->prepare($sqlLog);

                $stmtLog->execute([
                    ":usuario"     => $id_novo_usuario,
                    ":id_registro" => $id_novo_usuario
                ]);

                $mensagem = "Cadastro realizado com sucesso! Você já pode fazer login.";
                $tipo_mensagem = "sucesso";

            } catch (PDOException $e) {

                $mensagem = "Erro ao cadastrar usuário. Tente novamente.";
                $tipo_mensagem = "erro";
            }
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

    <title>Cadastro - CRUD Mundo</title>

</head>

<body>

    <div id="main">

        <div id="container">

            <h1>Explorador Geográfico</h1>

            <p>
                Crie sua conta para acessar o sistema.
            </p>

            <div class="cascata-container">

                <section class="coluna-lista">

                    <h2>
                        Cadastro
                    </h2>


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
                                    value="<?php echo isset($login) ? htmlspecialchars($login) : ''; ?>"
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
                                    autocomplete="new-password"
                                >

                            </li>


                            <li>

                                <label for="id-user-password-confirm">
                                    Confirmar senha
                                </label>

                                <input
                                    type="password"
                                    name="user-password-confirm"
                                    id="id-user-password-confirm"
                                    required
                                    autocomplete="new-password"
                                >

                            </li>


                            <li>

                                <input
                                    type="submit"
                                    value="CADASTRAR"
                                    id="btn-logar"
                                    class="btn-salvar"
                                >

                            </li>

                        </ul>

                    </form>


                    <p class="link-troca">
                        Já tem cadastro?
                        <a href="index.php">
                            Faça login aqui
                        </a>
                    </p>

                </section>

            </div>

        </div>

    </div>

</body>

</html>
