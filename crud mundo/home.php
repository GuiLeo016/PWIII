<?php
require_once 'conexao.php';

$continente_id = isset($_GET['continente']) ? $_GET['continente'] : null;
$pais_id = isset($_GET['pais']) ? $_GET['pais'] : null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Início - CRUD Mundo</title>
</head>
<body>
    <div id="main">
        <div id="header">
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="continentes.php">Continentes</a></li>
                <li><a href="paises.php">Países</a></li>
                <li><a href="cidades.php">Cidades</a></li>
                <li><a href="gov.php">Governantes</a></li>
            </ul>
        </div>
        <div id="container">
            <h1>Explorador Geográfico</h1>
            <p>Selecione um continente para ver seus países, e um país para ver suas cidades.</p>

            <div class="cascata-container">
                
                <section class="coluna-lista">
                    <h2>Continentes</h2>
                    <ul>
                        <?php
                        include 'conexao.php';
                        $sql = "SELECT id_continente, nome_continente FROM tb_continentes";
                        $stmt = $pdo->query($sql);
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $classe = ($continente_id == $row['id_continente']) ? 'class="ativo"' : '';
                            echo "<li><a href='index.php?continente={$row['id_continente']}' {$classe}>{$row['nome_continente']}</a></li>";
                        }
                        ?>
                    </ul>
                </section>

                <?php if ($continente_id): ?>
                <section class="coluna-lista">
                    <h2>Países</h2>
                    <ul>
                        <?php
                        $sql = "SELECT id_pais, nome_pais FROM tb_paises WHERE continente_pais = :cont_id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(['cont_id' => $continente_id]);
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $classe = ($pais_id == $row['id_pais']) ? 'class="ativo"' : '';
                            echo "<li><a href='index.php?continente={$continente_id}&pais={$row['id_pais']}' {$classe}>{$row['nome_pais']}</a></li>";
                        }
                        ?>
                    </ul>
                </section>
                <?php endif; ?>

                <?php if ($pais_id): ?>
                <section class="coluna-lista">
                    <h2>Cidades</h2>
                    <ul>
                        <?php
                        $sql = "SELECT id_cidade, nome_cidade FROM tb_cidades WHERE pais_cidade = :pais_id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(['pais_id' => $pais_id]);
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<li><a href='#'>{$row['nome_cidade']}</a></li>";
                        }
                        ?>
                    </ul>
                </section>
                <?php endif; ?>

            </div>
        </div>
    </div>
</body>
</html>