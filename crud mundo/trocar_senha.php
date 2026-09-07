<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexao.php';

if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php");
    exit;
}

$mensagem = "";
$tipo_mensagem = "";

$obrigatorio = !empty($_SESSION["senha_temporaria"]);


/*
|--------------------------------------------------------------------------
| PROCESSAMENTO DA TROCA DE SENHA
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $senhaAtual = $_POST["senha_atual"] ?? "";
    $novaSenha = $_POST["nova_senha"] ?? "";
    $confirmarSenha = $_POST["confirmar_senha"] ?? "";

    $stmt = $pdo->prepare("SELECT senha_usuario FROM tb_usuario WHERE id_usuario = :id");
    $stmt->execute([":id" => $_SESSION["id_usuario"]]);
    $usuario = $stmt->fetch();

    if (!$usuario || !password_verify($senhaAtual, $usuario["senha_usuario"])) {

        $mensagem = "Senha atual incorreta.";
        $tipo_mensagem = "erro";

    } elseif (mb_strlen($novaSenha) < 4) {

        $mensagem = "A nova senha deve ter pelo menos 4 caracteres.";
        $tipo_mensagem = "erro";

    } elseif ($novaSenha !== $confirmarSenha) {

        $mensagem = "As senhas não coincidem.";
        $tipo_mensagem = "erro";

    } elseif ($novaSenha === "1234") {

        $mensagem = "Escolha uma senha diferente da senha padrão (1234).";
        $tipo_mensagem = "erro";

    } elseif (password_verify($novaSenha, $usuario["senha_usuario"])) {

        $mensagem = "A nova senha deve ser diferente da senha atual.";
        $tipo_mensagem = "erro";

    } else {

        $novoHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        $stmtUpdate = $pdo->prepare(
            "UPDATE tb_usuario SET senha_usuario = :senha, senha_temporaria = 0 WHERE id_usuario = :id"
        );
        $stmtUpdate->execute([
            ":senha" => $novoHash,
            ":id"    => $_SESSION["id_usuario"]
        ]);

        $_SESSION["senha_temporaria"] = 0;

        $sqlLog = "INSERT INTO tb_log (
                        usuario_log,
                        acao_log,
                        descricao_log,
                        data_log,
                        hora_log
                   )
                   VALUES (
                        :usuario,
                        'TROCA_SENHA',
                        'Usuário alterou a própria senha',
                        CURDATE(),
                        CURTIME()
                   )";

        $stmtLog = $pdo->prepare($sqlLog);
        $stmtLog->execute([":usuario" => $_SESSION["id_usuario"]]);

        header("Location: home.php");
        exit;
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

    <title>Alterar Senha - CRUD Mundo</title>

</head>

<body>

    <div id="main">

        <div id="container">

            <h1>Explorador Geográfico</h1>

            <p>
                <?php if ($obrigatorio): ?>
                    Por segurança, você precisa trocar a senha padrão antes de continuar.
                <?php else: ?>
                    Altere sua senha quando quiser.
                <?php endif; ?>
            </p>

            <div class="cascata-container">

                <section class="coluna-lista">

                    <h2>Alterar Senha</h2>

                    <?php if ($mensagem !== ""): ?>

                        <div class="mensagem <?php echo $tipo_mensagem; ?>">
                            <?php echo htmlspecialchars($mensagem); ?>
                        </div>

                    <?php endif; ?>

                    <form action="" method="post">

                        <ul>

                            <li>
                                <label for="id-senha-atual">
                                    Senha atual
                                </label>

                                <input
                                    type="password"
                                    name="senha_atual"
                                    id="id-senha-atual"
                                    required
                                    autocomplete="current-password"
                                >
                            </li>

                            <li>
                                <label for="id-nova-senha">
                                    Nova senha
                                </label>

                                <input
                                    type="password"
                                    name="nova_senha"
                                    id="id-nova-senha"
                                    required
                                    autocomplete="new-password"
                                >
                            </li>

                            <li>
                                <label for="id-confirmar-senha">
                                    Confirmar nova senha
                                </label>

                                <input
                                    type="password"
                                    name="confirmar_senha"
                                    id="id-confirmar-senha"
                                    required
                                    autocomplete="new-password"
                                >
                            </li>

                            <li>
                                <input
                                    type="submit"
                                    value="SALVAR NOVA SENHA"
                                    id="btn-logar"
                                    class="btn-salvar"
                                >
                            </li>

                        </ul>

                    </form>

                    <?php if (!$obrigatorio): ?>
                        <p class="link-troca">
                            <a href="home.php">Voltar ao início</a>
                        </p>
                    <?php endif; ?>

                </section>

            </div>

        </div>

    </div>

</body>

</html>