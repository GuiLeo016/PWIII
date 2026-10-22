<?php
require 'verificacao.php';
include 'conexao.php';

$mensagem = "";

// Lógica de Inserção no Banco
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "INSERT INTO tb_paises (nome_pais, populacao_pais, area_pais, idioma, clima_pais, regime_politico, moeda, governante_pais, continente_pais) 
                VALUES (:nome, :pop, :area, :idioma, :clima, :regime, :moeda, :gov, :cont)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $_POST['nome_pais'],
            ':pop' => $_POST['populacao_pais'],
            ':area' => $_POST['area_pais'],
            ':idioma' => $_POST['idioma'],
            ':clima' => $_POST['clima_pais'],
            ':regime' => $_POST['regime_politico'],
            ':moeda' => $_POST['moeda'],
            ':gov' => $_POST['governante_pais'],
            ':cont' => $_POST['continente_pais']
        ]);
        $mensagem = "País cadastrado com sucesso!";
    } catch (PDOException $e) {
        $mensagem = "Erro ao cadastrar: " . $e->getMessage();
    }
}

// Buscando Continentes e Governantes para os campos <select>
$continentes = $pdo->query("SELECT id_continente, nome_continente FROM tb_continentes")->fetchAll(PDO::FETCH_ASSOC);
$governantes = $pdo->query("SELECT id_governante, nome_governante FROM tb_governantes")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Países - CRUD Mundo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="main">

        <?php include 'header.php'; ?>

        <main id="container">

            <h1>Cadastrar País</h1>
            <p>Preencha os dados para cadastrar um novo país.</p>

            <?php if ($mensagem): ?>
                <div class="mensagem <?= stripos($mensagem, 'sucesso') !== false ? 'sucesso' : 'erro' ?>" role="status">
                    <?= htmlspecialchars($mensagem) ?>
                </div>
            <?php endif; ?>

            <section class="form-container">
                <form action="paises.php" method="POST">

                    <div class="campo campo-largo">
                        <label for="nome_pais">Nome do País</label>
                        <input type="text" name="nome_pais" id="nome_pais"  required>
                    </div>
                    <div class="campo">
                        <label for="continente_pais">Continente</label>
                        <select name="continente_pais" id="continente_pais" required>
                            <option value="">Selecione um Continente...</option>
                            <?php foreach ($continentes as $cont): ?>
                                <option value="<?= $cont['id_continente'] ?>"><?= htmlspecialchars($cont['nome_continente']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="campo">
                        <label for="governante_pais">Governante</label>
                        <select name="governante_pais" id="governante_pais" required>
                            <option value="">Selecione um Governante...</option>
                            <?php foreach ($governantes as $gov): ?>
                                <option value="<?= $gov['id_governante'] ?>"><?= htmlspecialchars($gov['nome_governante']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="campo">
                        <label for="populacao_pais">População</label>
                        <input type="number" name="populacao_pais" id="populacao_pais"  required>
                    </div>
                    <div class="campo">
                        <label for="area_pais">Área (km²)</label>
                        <input type="number" name="area_pais" id="area_pais" step="0.001" required>
                    </div>
                    <div class="campo">
                        <label for="idioma">Idioma</label>
                        <input type="text" name="idioma" id="idioma"  required>
                    </div>
                    <div class="campo">
                        <label for="clima_pais">Clima</label>
                        <input type="text" name="clima_pais" id="clima_pais"  required>
                    </div>
                    <div class="campo">
                        <label for="regime_politico">Regime Político</label>
                        <input type="text" name="regime_politico" id="regime_politico"  required>
                    </div>
                    <div class="campo">
                        <label for="moeda">Moeda</label>
                        <input type="text" name="moeda" id="moeda"  required>
                    </div>

                    <button type="submit" class="btn-salvar">Salvar</button>

                </form>
            </section>

        </main>

    </div>
</body>
</html>
