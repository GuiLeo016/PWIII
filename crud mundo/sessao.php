<?php

/*
|--------------------------------------------------------------------------
| GUARDA DE SESSÃO (uso em toda página que exige usuário logado)
|--------------------------------------------------------------------------
| Inclua no topo de qualquer página que só pode ser acessada por quem
| já fez login:
|
|     require 'sessao.php';
|
| Além de exigir login, este arquivo obriga o usuário a trocar a senha
| antes de acessar qualquer outra página, enquanto a senha dele ainda for
| a senha padrão temporária (flag senha_temporaria = 1 no banco).
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| USUÁRIO NÃO LOGADO
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| SENHA TEMPORÁRIA AINDA NÃO TROCADA
|--------------------------------------------------------------------------
| Só deixa passar direto quem já está indo para trocar_senha.php ou sair
| (logout.php) — qualquer outra página redireciona para a troca de senha.
*/

$paginaAtual = basename($_SERVER["SCRIPT_NAME"]);
$paginasLiberadasComSenhaTemporaria = ["trocar_senha.php", "logout.php"];

if (
    !empty($_SESSION["senha_temporaria"]) &&
    !in_array($paginaAtual, $paginasLiberadasComSenhaTemporaria, true)
) {
    header("Location: trocar_senha.php");
    exit;
}