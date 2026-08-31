<?php

/*
|--------------------------------------------------------------------------
| VERIFICAÇÃO DE ACESSO (PÁGINAS RESTRITAS A ADMINISTRADORES)
|--------------------------------------------------------------------------
| Inclua este arquivo no topo de qualquer página que só pode ser acessada
| por um usuário logado do tipo "A" (administrador). Ex.:
|
|     require 'verificacao.php';
|
| Se não houver sessão iniciada, session_start() é chamado aqui mesmo,
| então não é necessário chamá-lo antes de incluir este arquivo.
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
| USUÁRIO LOGADO, MAS SEM PERMISSÃO DE ADMINISTRADOR
|--------------------------------------------------------------------------
*/

if ($_SESSION["tipo_usuario"] !== "A") {
    header("Location: home.php?acesso=negado");
    exit;
}
