<?php
require 'verificacao.php';
include 'conexao.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "INSERT INTO tb_cidades (nome_cidade, populacao_cidade, area_cidade, clima_cidade, data_fundacao, pais_cidade, governante_cidade) 
                VALUES (:nome, :pop, :area, :clima, :fundacao, :pais, :gov)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $_POST['nome_cidade'],
            ':pop' => $_POST['populacao_cidade'],
            ':area' => $_POST['area_cidade'],
            ':clima' => $_POST['clima_cidade'],
            ':fundacao' => $_POST['data_fundacao'],
            ':pais' => $_POST['pais_cidade'],
            ':gov' => $_POST['governante_cidade']
        ]);
        $mensagem = "Cidade cadastrada com sucesso!";
    } catch (PDOException $e) {
        $mensagem = "Erro ao cadastrar: " . $e->getMessage();
    }
}

$paises = $pdo->query("SELECT id_pais, nome_pais FROM tb_paises")->fetchAll(PDO::FETCH_ASSOC);
$governantes = $pdo->query("SELECT id_governante, nome_governante FROM tb_governantes")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cidades - CRUD Mundo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="main">

        <?php include 'header.php'; ?>

        <main id="container">

            <h1>Cadastrar Cidade</h1>
            <p>Preencha os dados para cadastrar uma nova cidade.</p>

            <?php if ($mensagem): ?>
                <div class="mensagem <?= stripos($mensagem, 'sucesso') !== false ? 'sucesso' : 'erro' ?>" role="status">
                    <?= htmlspecialchars($mensagem) ?>
                </div>
            <?php endif; ?>

            <section class="form-container">
                <form action="cidades.php" method="POST">

                    <div class="campo campo-largo">
                        <label for="nome_cidade">Nome da Cidade</label>
                        <input type="text" name="nome_cidade" id="nome_cidade"  required>
                    </div>
                    <div class="campo">
                        <label for="pais_cidade">País</label>
                        <select name="pais_cidade" id="pais_cidade" required>
                            <option value="">Selecione um País...</option>
                            <?php foreach ($paises as $p): ?>
                                <option value="<?= $p['id_pais'] ?>"><?= htmlspecialchars($p['nome_pais']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="campo">
                        <label for="governante_cidade">Prefeito/Governante</label>
                        <select name="governante_cidade" id="governante_cidade" required>
                            <option value="">Selecione um Governante...</option>
                            <?php foreach ($governantes as $g): ?>
                                <option value="<?= $g['id_governante'] ?>"><?= htmlspecialchars($g['nome_governante']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="campo">
                        <label for="populacao_cidade">População</label>
                        <input type="number" name="populacao_cidade" id="populacao_cidade"  required>
                    </div>
                    <div class="campo">
                        <label for="area_cidade">Área (km²)</label>
                        <input type="number" name="area_cidade" id="area_cidade" step="0.001" required>
                    </div>
                    <div class="campo">
                        <label for="clima_cidade">Clima</label>
                        <input type="text" name="clima_cidade" id="clima_cidade"  required>
                    </div>
                    <div class="campo">
                        <label for="data_fundacao">Data de Fundação</label>
                        <input type="date" name="data_fundacao" id="data_fundacao"  required>
                    </div>

                    <button type="submit" class="btn-salvar">Salvar</button>

                </form>
            </section>

        </main>

    </div>
</body>
</html>
