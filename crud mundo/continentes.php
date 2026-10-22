<?php
require 'verificacao.php';
include 'conexao.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome_continente'];
    $populacao = $_POST['populacao_continente'];
    $area = $_POST['area_continente'];
    $total_paises = $_POST['total_paises'];

    try {
        $sql = "INSERT INTO tb_continentes (nome_continente, populacao_continente, area_continente, total_paises) 
                VALUES (:nome, :populacao, :area, :total_paises)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':populacao' => $populacao,
            ':area' => $area,
            ':total_paises' => $total_paises
        ]);
        $mensagem = "Continente cadastrado com sucesso!";
    } catch (PDOException $e) {
        $mensagem = "Erro ao cadastrar: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Continentes - CRUD Mundo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="main">

        <?php include 'header.php'; ?>

        <main id="container">

            <h1>Cadastrar Continente</h1>
            <p>Preencha os dados para cadastrar um novo continente.</p>

            <?php if ($mensagem): ?>
                <div class="mensagem <?= stripos($mensagem, 'sucesso') !== false ? 'sucesso' : 'erro' ?>" role="status">
                    <?= htmlspecialchars($mensagem) ?>
                </div>
            <?php endif; ?>

            <section class="form-container">
                <form action="continentes.php" method="POST">

                    <div class="campo campo-largo">
                        <label for="nome_continente">Nome do Continente</label>
                        <input type="text" name="nome_continente" id="nome_continente"  required>
                    </div>
                    <div class="campo">
                        <label for="populacao_continente">População</label>
                        <input type="number" name="populacao_continente" id="populacao_continente" min="1" required>
                    </div>
                    <div class="campo">
                        <label for="area_continente">Área (km²)</label>
                        <input type="number" name="area_continente" id="area_continente" step="0.01" min="1" required>
                    </div>
                    <div class="campo">
                        <label for="total_paises">Total de Países</label>
                        <input type="number" name="total_paises" id="total_paises" min="1" required>
                    </div>

                    <button type="submit" class="btn-salvar">Salvar</button>

                </form>
            </section>

        </main>

    </div>
</body>
</html>
