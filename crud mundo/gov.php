<?php
require 'verificacao.php';
include 'conexao.php';

$mensagem = "";


/*
|--------------------------------------------------------------------------
| CALCULA A IDADE COM BASE NA DATA DE NASCIMENTO
|--------------------------------------------------------------------------
*/

function calcularIdade(string $data_nascimento): int
{
    $nascimento = new DateTime($data_nascimento);
    $hoje = new DateTime();

    return $hoje->diff($nascimento)->y;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $idade = calcularIdade($_POST['data_nascimento']);

        $sql = "INSERT INTO tb_governantes (nome_governante, partido_politico, data_nascimento, idade, data_inicio_mandato, data_final_mandato) 
                VALUES (:nome, :partido, :nasc, :idade, :inicio, :fim)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $_POST['nome_governante'],
            ':partido' => $_POST['partido_politico'],
            ':nasc' => $_POST['data_nascimento'],
            ':idade' => $idade,
            ':inicio' => $_POST['data_inicio_mandato'],
            ':fim' => $_POST['data_final_mandato']
        ]);
        $mensagem = "Governante cadastrado com sucesso!";
    } catch (PDOException $e) {
        $mensagem = "Erro ao cadastrar: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Governantes - CRUD Mundo</title>
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
            <h1>Cadastrar Governante</h1>
            <?php if($mensagem) echo "<p class='mensagem'>$mensagem</p>"; ?>
            
            <div class="form-container">
                <form action="gov.php" method="POST">
                    <label>Nome do Governante:</label>
                    <input type="text" name="nome_governante" required>
                    
                    <label>Partido Político:</label>
                    <input type="text" name="partido_politico" required>
                    
                    <label>Data de Nascimento:</label>
                    <input type="date" name="data_nascimento" id="data_nascimento" required onchange="atualizarIdadePreview()">
                    <p id="idade-preview" class="idade-preview"></p>
                    
                    <label>Início do Mandato:</label>
                    <input type="date" name="data_inicio_mandato" required>
                    
                    <label>Final do Mandato:</label>
                    <input type="date" name="data_final_mandato" required>
                    
                    <button type="submit" class="btn-salvar">Salvar</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function atualizarIdadePreview() {
            const dataNascimento = document.getElementById('data_nascimento').value;
            const preview = document.getElementById('idade-preview');

            if (!dataNascimento) {
                preview.textContent = '';
                return;
            }

            const nascimento = new Date(dataNascimento + 'T00:00:00');
            const hoje = new Date();

            let idade = hoje.getFullYear() - nascimento.getFullYear();
            const aindaNaoFezAniversario =
                hoje.getMonth() < nascimento.getMonth() ||
                (hoje.getMonth() === nascimento.getMonth() && hoje.getDate() < nascimento.getDate());

            if (aindaNaoFezAniversario) {
                idade--;
            }

            preview.textContent = idade >= 0 ? `Idade calculada: ${idade} anos` : '';
        }
    </script>
</body>
</html>
