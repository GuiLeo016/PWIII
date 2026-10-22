<?php

/*
|--------------------------------------------------------------------------
| CABEÇALHO / MENU COMPARTILHADO
|--------------------------------------------------------------------------
| Usado por todas as páginas internas, dentro de <div id="main">:
|
|     <?php include 'header.php'; ?>
|
| Requer sessão iniciada (sessao.php ou verificacao.php já incluídos).
| Para adicionar uma nova página ao menu, basta incluir uma linha em
| $itensMenu: [arquivo, rótulo, somente administrador?]
*/

$paginaAtual = basename($_SERVER["SCRIPT_NAME"]);
$ehAdmin = ($_SESSION["tipo_usuario"] ?? "") === "A";

$itensMenu = [
    ["home.php",         "Início",            false],
    ["mapa.php",         "Mapa",              false],
    ["continentes.php",  "Continentes",       true],
    ["paises.php",       "Países",            true],
    ["cidades.php",      "Cidades",           true],
    ["gov.php",          "Governantes",       true],
    ["cadastro.php",     "Cadastrar Usuário", true],
    ["trocar_senha.php", "Alterar Senha",     false],
    ["logout.php",       "Sair",              false],
];
?>
<header id="header">

    <nav aria-label="Navegação principal">

        <button
            type="button"
            class="menu-toggle"
            aria-expanded="false"
            aria-controls="menu-principal"
        >
            <span aria-hidden="true">☰</span> Menu
        </button>

        <ul id="menu-principal">

            <?php foreach ($itensMenu as [$arquivo, $rotulo, $somenteAdmin]): ?>

                <?php if ($somenteAdmin && !$ehAdmin) continue; ?>

                <?php
                $classes = [];
                if ($arquivo === "logout.php") $classes[] = "sair";
                if ($arquivo === $paginaAtual) $classes[] = "ativo";
                ?>

                <li>
                    <a
                        href="<?= $arquivo ?>"
                        <?= $classes ? 'class="' . implode(" ", $classes) . '"' : "" ?>
                        <?= $arquivo === $paginaAtual ? 'aria-current="page"' : "" ?>
                    ><?= htmlspecialchars($rotulo) ?></a>
                </li>

            <?php endforeach; ?>

        </ul>

    </nav>

    <script>
        (function () {
            var botao = document.querySelector("#header .menu-toggle");
            var menu = document.getElementById("menu-principal");

            if (!botao || !menu) return;

            botao.addEventListener("click", function () {
                var aberto = menu.classList.toggle("aberto");
                botao.setAttribute("aria-expanded", aberto ? "true" : "false");
            });
        })();
    </script>

</header>
