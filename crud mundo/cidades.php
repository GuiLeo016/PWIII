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
    <title>Cidades - CRUD Mundo</title>
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
            </ul>
        </div>
        
        <div id="container" style="height: auto; padding-bottom: 5ch;">
            <h1>Cidade</h1>
            <?php if($mensagem) echo "<p class='mensagem'>$mensagem</p>"; ?>
            
            <div class="form-container">
                <form action="cidades.php" method="POST">
                    <label>Nome da Cidade:</label>
                    <input type="text" name="nome_cidade" required>
                    
                    <label>País da Cidade:</label>
                    <select name="pais_cidade" required>
                        <option value="">Selecione um País...</option>
                        <?php foreach($paises as $p): ?>
                            <option value="<?= $p['id_pais'] ?>"><?= $p['nome_pais'] ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Prefeito/Governante:</label>
                    <select name="governante_cidade" required>
                        <option value="">Selecione um Governante...</option>
                        <?php foreach($governantes as $g): ?>
                            <option value="<?= $g['id_governante'] ?>"><?= $g['nome_governante'] ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>População:</label>
                    <input type="number" name="populacao_cidade" required>
                    
                    <label>Área (km²):</label>
                    <input type="number" step="0.001" name="area_cidade" required>
                    
                    <label>Clima:</label>
                    <input type="text" name="clima_cidade" required>
                    
                    <label>Data de Fundação:</label>
                    <input type="date" name="data_fundacao" required>
                    
                    <button type="submit" class="btn-salvar">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
