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
    <title>Cadastrar Países - CRUD Mundo</title>
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
            <h1>País</h1>
            <?php if($mensagem) echo "<p class='mensagem'>$mensagem</p>"; ?>
            
            <div class="form-container">
                <form action="paises.php" method="POST">
                    <label>Nome do País:</label>
                    <input type="text" name="nome_pais" required>
                    
                    <label>Continente:</label>
                    <select name="continente_pais" required>
                        <option value="">Selecione um Continente...</option>
                        <?php foreach($continentes as $cont): ?>
                            <option value="<?= $cont['id_continente'] ?>"><?= $cont['nome_continente'] ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Governante:</label>
                    <select name="governante_pais" required>
                        <option value="">Selecione um Governante...</option>
                        <?php foreach($governantes as $gov): ?>
                            <option value="<?= $gov['id_governante'] ?>"><?= $gov['nome_governante'] ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>População:</label>
                    <input type="number" name="populacao_pais" required>
                    
                    <label>Área (km²):</label>
                    <input type="number" step="0.001" name="area_pais" required>
                    
                    <label>Idioma:</label>
                    <input type="text" name="idioma" required>
                    
                    <label>Clima:</label>
                    <input type="text" name="clima_pais" required>
                    
                    <label>Regime Político:</label>
                    <input type="text" name="regime_politico" required>
                    
                    <label>Moeda:</label>
                    <input type="text" name="moeda" required>
                    
                    <button type="submit" class="btn-salvar">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
