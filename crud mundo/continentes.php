<?php
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
    <title>Cadastrar Continentes - CRUD Mundo</title>
    <link rel="stylesheet" href="style.css"> </head>
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
            <h1>Cadastrar Continente</h1>
            <?php if($mensagem) echo "<p class='mensagem'>$mensagem</p>"; ?>
            
            <div class="form-container">
                <form action="continentes.php" method="POST">
                    <label>Nome do Continente:</label>
                    <input type="text" name="nome_continente" required>
                    
                    <label>População:</label>
                    <input type="number" name="populacao_continente" required>
                    
                    <label>Área (km²):</label>
                    <input type="number" step="0.001" name="area_continente" required>
                    
                    <label>Total de Países:</label>
                    <input type="number" name="total_paises" required>
                    
                    <button type="submit" class="btn-salvar">Salvar Continente</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>