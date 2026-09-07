<?php

require 'verificacao.php';
include 'conexao.php';

$mensagem = "";


/*
|--------------------------------------------------------------------------
| SENHA PADRÃO PARA TODO NOVO USUÁRIO
|--------------------------------------------------------------------------
| Todo usuário cadastrado por um administrador recebe a mesma senha
| inicial. senha_temporaria = 1 obriga a troca dela no primeiro login
| (ver sessao.php).
*/

const SENHA_PADRAO = "1234";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = trim($_POST["username"] ?? "");
    $tipo  = $_POST["tipo_usuario"] ?? "U";
    $tipo  = in_array($tipo, ["U", "A"], true) ? $tipo : "U";

    if ($login === "") {

        $mensagem = "Informe o nome de usuário.";

    } elseif (mb_strlen($login) > 30) {

        $mensagem = "O nome de usuário pode ter no máximo 30 caracteres.";

    } else {

        $sqlVerifica = "SELECT id_usuario FROM tb_usuario WHERE login_usuario = :login LIMIT 1";
        $stmtVerifica = $pdo->prepare($sqlVerifica);
        $stmtVerifica->execute([":login" => $login]);

        if ($stmtVerifica->fetch()) {

            $mensagem = "Este nome de usuário já está em uso.";

        } else {

            try {

                $senhaHash = password_hash(SENHA_PADRAO, PASSWORD_DEFAULT);

                $sql = "INSERT INTO tb_usuario (login_usuario, senha_usuario, tipo_usuario, senha_temporaria)
                        VALUES (:login, :senha, :tipo, 1)";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ":login" => $login,
                    ":senha" => $senhaHash,
                    ":tipo"  => $tipo
                ]);

                $id_novo_usuario = $pdo->lastInsertId();

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
                                'Novo usuário cadastrado por um administrador',
                                CURDATE(),
                                CURTIME()
                           )";

                $stmtLog = $pdo->prepare($sqlLog);

                $stmtLog->execute([
                    ":usuario"     => $_SESSION["id_usuario"],
                    ":id_registro" => $id_novo_usuario
                ]);

                $mensagem = "Usuário \"$login\" cadastrado com sucesso! Senha padrão: "
                          . SENHA_PADRAO . " (será exigida a troca no primeiro login).";

            } catch (PDOException $e) {

                $mensagem = "Erro ao cadastrar: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário - CRUD Mundo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="main">
        <div id="header">
            <ul>
                <li><a href="home.php">Início</a></li>
                <li><a href="continentes.php">Continentes</a></li>
                <li><a href="paises.php">Países</a></li>
                <li><a href="cidades.php">Cidades</a></li>
                <li><a href="gov.php">Governantes</a></li>
                <li><a href="cadastro.php">Cadastrar Usuário</a></li>
                <li><a href="trocar_senha.php">Alterar Senha</a></li>
                <li><a href="logout.php">Sair</a></li>
            </ul>
        </div>

        <div id="container" style="height: auto; padding-bottom: 5ch;">
            <h1>Cadastrar Usuário</h1>
            <?php if ($mensagem) echo "<p class='mensagem'>$mensagem</p>"; ?>

            <div class="form-container">
                <form action="cadastro.php" method="POST">
                    <label>Nome de Usuário:</label>
                    <input type="text" name="username" maxlength="30" required>

                    <label>Tipo de Usuário:</label>
                    <select name="tipo_usuario" required>
                        <option value="U" selected>Usuário comum</option>
                        <option value="A">Administrador</option>
                    </select>

                    <p style="color:#aaa; font-size:1.7vh; margin-top:-0.8ch;">
                        A senha inicial de todo usuário cadastrado é sempre
                        <strong>1234</strong>. Ele será obrigado a trocá-la
                        no primeiro login.
                    </p>

                    <button type="submit" class="btn-salvar">Cadastrar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>