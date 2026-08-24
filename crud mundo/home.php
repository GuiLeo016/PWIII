<?php

session_start();

require_once 'conexao.php';

/*
|--------------------------------------------------------------------------
| VERIFICAÇÃO DE LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| DADOS DA SESSÃO
|--------------------------------------------------------------------------
*/

$nome_usuario = $_SESSION["login_usuario"];
$tipo_usuario = $_SESSION["tipo_usuario"];


/*
|--------------------------------------------------------------------------
| FILTROS
|--------------------------------------------------------------------------
*/

$continente_id = isset($_GET["continente"]) && ctype_digit($_GET["continente"])
    ? (int) $_GET["continente"]
    : null;

$pais_id = isset($_GET["pais"]) && ctype_digit($_GET["pais"])
    ? (int) $_GET["pais"]
    : null;

$cidade_id = isset($_GET["cidade"]) && ctype_digit($_GET["cidade"])
    ? (int) $_GET["cidade"]
    : null;


/*
|--------------------------------------------------------------------------
| CONTINENTES
|--------------------------------------------------------------------------
*/

$sqlContinentes = "
    SELECT
        id_continente,
        nome_continente
    FROM tb_continentes
    ORDER BY nome_continente
";

$stmtContinentes = $pdo->query($sqlContinentes);

$continentes = $stmtContinentes->fetchAll();


/*
|--------------------------------------------------------------------------
| DADOS DO CONTINENTE SELECIONADO
|--------------------------------------------------------------------------
*/

$continente = null;

if ($continente_id !== null) {

    $sqlContinente = "
        SELECT
            id_continente,
            nome_continente,
            populacao_continente,
            area_continente,
            total_paises
        FROM tb_continentes
        WHERE id_continente = :id
    ";

    $stmtContinente = $pdo->prepare($sqlContinente);

    $stmtContinente->execute([
        ":id" => $continente_id
    ]);

    $continente = $stmtContinente->fetch();

    /*
    |--------------------------------------------------------------------------
    | CONTINENTE NÃO ENCONTRADO
    |--------------------------------------------------------------------------
    */

    if (!$continente) {
        $continente_id = null;
    }
}


/*
|--------------------------------------------------------------------------
| PAÍSES DO CONTINENTE SELECIONADO
|--------------------------------------------------------------------------
*/

$paises = [];

if ($continente_id !== null) {

    $sqlPaises = "
        SELECT
            id_pais,
            nome_pais,
            populacao_pais,
            area_pais,
            idioma,
            clima_pais,
            regime_politico,
            moeda,
            governante_pais
        FROM tb_paises
        WHERE continente_pais = :continente
        ORDER BY nome_pais
    ";

    $stmtPaises = $pdo->prepare($sqlPaises);

    $stmtPaises->execute([
        ":continente" => $continente_id
    ]);

    $paises = $stmtPaises->fetchAll();
}


/*
|--------------------------------------------------------------------------
| DADOS DO PAÍS SELECIONADO
|--------------------------------------------------------------------------
*/

$pais = null;

if ($pais_id !== null) {

    $sqlPais = "
        SELECT
            p.id_pais,
            p.nome_pais,
            p.populacao_pais,
            p.area_pais,
            p.idioma,
            p.clima_pais,
            p.regime_politico,
            p.moeda,

            g.id_governante,
            g.nome_governante,
            g.partido_politico,
            g.data_nascimento,
            g.idade,
            g.data_inicio_mandato,
            g.data_final_mandato

        FROM tb_paises p

        INNER JOIN tb_governantes g
            ON p.governante_pais = g.id_governante

        WHERE p.id_pais = :pais
    ";

    $stmtPais = $pdo->prepare($sqlPais);

    $stmtPais->execute([
        ":pais" => $pais_id
    ]);

    $pais = $stmtPais->fetch();

    /*
    |--------------------------------------------------------------------------
    | VERIFICA SE O PAÍS PERTENCE AO CONTINENTE SELECIONADO
    |--------------------------------------------------------------------------
    */

    if ($pais && $continente_id !== null) {

        $sqlValidacao = "
            SELECT id_pais
            FROM tb_paises
            WHERE id_pais = :pais
            AND continente_pais = :continente
        ";

        $stmtValidacao = $pdo->prepare($sqlValidacao);

        $stmtValidacao->execute([
            ":pais" => $pais_id,
            ":continente" => $continente_id
        ]);

        if (!$stmtValidacao->fetch()) {
            $pais = null;
            $pais_id = null;
        }
    }
}


/*
|--------------------------------------------------------------------------
| CIDADES DO PAÍS SELECIONADO
|--------------------------------------------------------------------------
*/

$cidades = [];

if ($pais_id !== null) {

    $sqlCidades = "
        SELECT
            c.id_cidade,
            c.nome_cidade,
            c.populacao_cidade,
            c.area_cidade,
            c.clima_cidade,
            c.data_fundacao,

            g.id_governante,
            g.nome_governante

        FROM tb_cidades c

        INNER JOIN tb_governantes g
            ON c.governante_cidade = g.id_governante

        WHERE c.pais_cidade = :pais
        ORDER BY c.nome_cidade
    ";

    $stmtCidades = $pdo->prepare($sqlCidades);

    $stmtCidades->execute([
        ":pais" => $pais_id
    ]);

    $cidades = $stmtCidades->fetchAll();
}


/*
|--------------------------------------------------------------------------
| DADOS DA CIDADE SELECIONADA
|--------------------------------------------------------------------------
*/

$cidade = null;

if ($cidade_id !== null && $pais_id !== null) {

    $sqlCidade = "
        SELECT
            c.id_cidade,
            c.nome_cidade,
            c.populacao_cidade,
            c.area_cidade,
            c.clima_cidade,
            c.data_fundacao,

            g.id_governante,
            g.nome_governante,
            g.partido_politico,
            g.data_nascimento,
            g.idade,
            g.data_inicio_mandato,
            g.data_final_mandato

        FROM tb_cidades c

        INNER JOIN tb_governantes g
            ON c.governante_cidade = g.id_governante

        WHERE c.id_cidade = :cidade
        AND c.pais_cidade = :pais
    ";

    $stmtCidade = $pdo->prepare($sqlCidade);

    $stmtCidade->execute([
        ":cidade" => $cidade_id,
        ":pais" => $pais_id
    ]);

    $cidade = $stmtCidade->fetch();
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

    <title>Início - CRUD Mundo</title>

</head>

<body>

<div id="main">

    <!-- =====================================================
         HEADER
         ===================================================== -->

    <header id="header">

        <ul>

            <li>
                <a href="home.php">
                    Início
                </a>
            </li>

            <li>
                <a href="continentes.php">
                    Continentes
                </a>
            </li>

            <li>
                <a href="paises.php">
                    Países
                </a>
            </li>

            <li>
                <a href="cidades.php">
                    Cidades
                </a>
            </li>

            <li>
                <a href="gov.php">
                    Governantes
                </a>
            </li>

            <li>
                <a href="logout.php">
                    Sair
                </a>
            </li>

        </ul>

    </header>


    <!-- =====================================================
         CONTAINER PRINCIPAL
         ===================================================== -->

    <div id="container">

        <h1>
            Explorador Geográfico
        </h1>

        <p>
            Selecione um continente, um país e uma cidade
            para consultar suas informações.
        </p>


        <!-- =================================================
             FILTROS
             ================================================= -->

        <div class="cascata-container">


            <!-- =================================================
                 CONTINENTES
                 ================================================= -->

            <section class="coluna-lista">

                <h2>
                    Continentes
                </h2>

                <ul>

                    <?php foreach ($continentes as $item): ?>

                        <li>

                            <a
                                href="home.php?continente=<?php echo $item["id_continente"]; ?>"
                                class="<?php echo ($continente_id == $item["id_continente"]) ? "ativo" : ""; ?>"
                            >
                                <?php
                                echo htmlspecialchars(
                                    $item["nome_continente"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                );
                                ?>
                            </a>

                        </li>

                    <?php endforeach; ?>

                </ul>

            </section>


            <!-- =================================================
                 PAÍSES
                 ================================================= -->

            <?php if ($continente_id !== null): ?>

                <section class="coluna-lista">

                    <h2>
                        Países
                    </h2>

                    <ul>

                        <?php if (count($paises) > 0): ?>

                            <?php foreach ($paises as $item): ?>

                                <li>

                                    <a
                                        href="home.php?continente=<?php echo $continente_id; ?>&pais=<?php echo $item["id_pais"]; ?>"
                                        class="<?php echo ($pais_id == $item["id_pais"]) ? "ativo" : ""; ?>"
                                    >
                                        <?php
                                        echo htmlspecialchars(
                                            $item["nome_pais"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        );
                                        ?>
                                    </a>

                                </li>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <li>
                                <span class="sem-registros">
                                    Nenhum país cadastrado.
                                </span>
                            </li>

                        <?php endif; ?>

                    </ul>

                </section>

            <?php endif; ?>


            <!-- =================================================
                 CIDADES
                 ================================================= -->

            <?php if ($pais_id !== null): ?>

                <section class="coluna-lista">

                    <h2>
                        Cidades
                    </h2>

                    <ul>

                        <?php if (count($cidades) > 0): ?>

                            <?php foreach ($cidades as $item): ?>

                                <li>

                                    <a
                                        href="home.php?continente=<?php echo $continente_id; ?>&pais=<?php echo $pais_id; ?>&cidade=<?php echo $item["id_cidade"]; ?>"
                                        class="<?php echo ($cidade_id == $item["id_cidade"]) ? "ativo" : ""; ?>"
                                    >
                                        <?php
                                        echo htmlspecialchars(
                                            $item["nome_cidade"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        );
                                        ?>
                                    </a>

                                </li>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <li>
                                <span class="sem-registros">
                                    Nenhuma cidade cadastrada.
                                </span>
                            </li>

                        <?php endif; ?>

                    </ul>

                </section>

            <?php endif; ?>

        </div>


        <!-- =================================================
             INFORMAÇÕES DO CONTINENTE
             ================================================= -->

        <?php if ($continente): ?>

            <section class="info-container">

                <h2>
                    Dados do Continente
                </h2>

                <div class="info-grid">

                    <div class="info-item">
                        <strong>Nome</strong>
                        <span>
                            <?php echo htmlspecialchars(
                                $continente["nome_continente"],
                                ENT_QUOTES,
                                "UTF-8"
                            ); ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <strong>População</strong>
                        <span>
                            <?php echo number_format(
                                $continente["populacao_continente"],
                                0,
                                ",",
                                "."
                            ); ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <strong>Área</strong>
                        <span>
                            <?php echo number_format(
                                $continente["area_continente"],
                                3,
                                ",",
                                "."
                            ); ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <strong>Total de países</strong>
                        <span>
                            <?php echo $continente["total_paises"]; ?>
                        </span>
                    </div>

                </div>

            </section>

        <?php endif; ?>


        <!-- =================================================
             INFORMAÇÕES DO PAÍS
             ================================================= -->

        <?php if ($pais): ?>

            <section class="info-container">

                <h2>
                    Dados do País
                </h2>

                <div class="info-grid">

                    <div class="info-item">
                        <strong>Nome</strong>
                        <span>
                            <?php echo htmlspecialchars(
                                $pais["nome_pais"],
                                ENT_QUOTES,
                                "UTF-8"
                            ); ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <strong>População</strong>
                        <span>
                            <?php echo number_format(
                                $pais["populacao_pais"],
                                0,
                                ",",
                                "."
                            ); ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <strong>Área</strong>
                        <span>
                            <?php echo number_format(
                                $pais["area_pais"],
                                3,
                                ",",
                                "."
                            ); ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <strong>Idioma</strong>
                        <span>
                            <?php echo htmlspecialchars(
                                $pais["idioma"],
                                ENT_QUOTES,
                                "UTF-8"
                            ); ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <strong>Clima</strong>
                        <span>
                            <?php echo htmlspecialchars(
                                $pais["clima_pais"],
                                ENT_QUOTES,
                                "UTF-8"
                            ); ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <strong>Regime político</strong>
                        <span>
                            <?php echo htmlspecialchars(
                                $pais["regime_politico"],
                                ENT_QUOTES,
                                "UTF-8"
                            ); ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <strong>Moeda</strong>
                        <span>
                            <?php echo htmlspecialchars(
                                $pais["moeda"],
                                ENT_QUOTES,
                                "UTF-8"
                            ); ?>
                        </span>
                    </div>

                </div>


                <!-- =============================================
                     GOVERNANTE DO PAÍS
                     ============================================= -->

                <div class="governante-container">

                    <h3>
                        Governante do país
                    </h3>

                    <div class="info-grid">

                        <div class="info-item">

                            <strong>
                                Nome
                            </strong>

                            <span>
                                <?php echo htmlspecialchars(
                                    $pais["nome_governante"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ); ?>
                            </span>

                        </div>

                        <div class="info-item">

                            <strong>
                                Partido
                            </strong>

                            <span>
                                <?php echo htmlspecialchars(
                                    $pais["partido_politico"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ); ?>
                            </span>

                        </div>

                        <div class="info-item">

                            <strong>
                                Data de nascimento
                            </strong>

                            <span>
                                <?php echo date(
                                    "d/m/Y",
                                    strtotime($pais["data_nascimento"])
                                ); ?>
                            </span>

                        </div>

                        <div class="info-item">

                            <strong>
                                Idade
                            </strong>

                            <span>
                                <?php echo $pais["idade"]; ?> anos
                            </span>

                        </div>

                        <div class="info-item">

                            <strong>
                                Início do mandato
                            </strong>

                            <span>
                                <?php echo date(
                                    "d/m/Y",
                                    strtotime($pais["data_inicio_mandato"])
                                ); ?>
                            </span>

                        </div>

                        <div class="info-item">

                            <strong>
                                Final do mandato
                            </strong>

                            <span>
                                <?php echo date(
                                    "d/m/Y",
                                    strtotime($pais["data_final_mandato"])
                                ); ?>
                            </span>

                        </div>

                    </div>

                </div>

            </section>

        <?php endif; ?>


        <!-- =================================================
             INFORMAÇÕES DA CIDADE
             ================================================= -->

        <?php if ($cidade): ?>

            <section class="info-container">

                <h2>
                    Dados da Cidade
                </h2>

                <div class="info-grid">

                    <div class="info-item">

                        <strong>
                            Nome
                        </strong>

                        <span>
                            <?php echo htmlspecialchars(
                                $cidade["nome_cidade"],
                                ENT_QUOTES,
                                "UTF-8"
                            ); ?>
                        </span>

                    </div>


                    <div class="info-item">

                        <strong>
                            População
                        </strong>

                        <span>
                            <?php echo number_format(
                                $cidade["populacao_cidade"],
                                0,
                                ",",
                                "."
                            ); ?>
                        </span>

                    </div>


                    <div class="info-item">

                        <strong>
                            Área
                        </strong>

                        <span>
                            <?php echo number_format(
                                $cidade["area_cidade"],
                                3,
                                ",",
                                "."
                            ); ?>
                        </span>

                    </div>


                    <div class="info-item">

                        <strong>
                            Clima
                        </strong>

                        <span>
                            <?php echo htmlspecialchars(
                                $cidade["clima_cidade"],
                                ENT_QUOTES,
                                "UTF-8"
                            ); ?>
                        </span>

                    </div>


                    <div class="info-item">

                        <strong>
                            Data de fundação
                        </strong>

                        <span>
                            <?php echo date(
                                "d/m/Y",
                                strtotime($cidade["data_fundacao"])
                            ); ?>
                        </span>

                    </div>

                </div>


                <!-- =============================================
                     GOVERNANTE DA CIDADE
                     ============================================= -->

                <div class="governante-container">

                    <h3>
                        Governante da cidade
                    </h3>

                    <div class="info-grid">

                        <div class="info-item">

                            <strong>
                                Nome
                            </strong>

                            <span>
                                <?php echo htmlspecialchars(
                                    $cidade["nome_governante"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ); ?>
                            </span>

                        </div>

                        <div class="info-item">

                            <strong>
                                Partido
                            </strong>

                            <span>
                                <?php echo htmlspecialchars(
                                    $cidade["partido_politico"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ); ?>
                            </span>

                        </div>

                        <div class="info-item">

                            <strong>
                                Idade
                            </strong>

                            <span>
                                <?php echo $cidade["idade"]; ?> anos
                            </span>

                        </div>

                    </div>

                </div>

            </section>

        <?php endif; ?>


        <!-- =================================================
             MENSAGEM INICIAL
             ================================================= -->

        <?php if ($continente_id === null): ?>

            <section class="boas-vindas">

                <h2>
                    Bem-vindo, <?php echo htmlspecialchars(
                        $nome_usuario,
                        ENT_QUOTES,
                        "UTF-8"
                    ); ?>!
                </h2>

                <p>
                    Selecione um continente acima para começar
                    a explorar as informações geográficas.
                </p>

            </section>

        <?php endif; ?>

    </div>

</div>

</body>

</html>