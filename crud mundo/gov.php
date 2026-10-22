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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Governantes - CRUD Mundo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="main">

        <?php include 'header.php'; ?>

        <main id="container">

            <h1>Cadastrar Governante</h1>
            <p>Preencha os dados para cadastrar um novo governante.</p>

            <?php if ($mensagem): ?>
                <div class="mensagem <?= stripos($mensagem, 'sucesso') !== false ? 'sucesso' : 'erro' ?>" role="status">
                    <?= htmlspecialchars($mensagem) ?>
                </div>
            <?php endif; ?>

            <section class="form-container">
                <form action="gov.php" method="POST">

                    <div class="campo campo-largo">
                        <label for="nome_governante">Nome do Governante</label>
                        <input type="text" name="nome_governante" id="nome_governante"  required>
                    </div>
                    <div class="campo campo-largo">
                        <label for="partido_politico">Partido Político</label>
                        <input type="text" name="partido_politico" id="partido_politico"  required>
                    </div>
                    <div class="campo">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input type="date" name="data_nascimento" id="data_nascimento" required onchange="atualizarIdadePreview()">
                        <span id="idade-preview" class="idade-preview">A idade é calculada automaticamente.</span>
                    </div>
                    <div class="campo">
                        <label for="data_inicio_mandato">Início do Mandato</label>
                        <input type="date" name="data_inicio_mandato" id="data_inicio_mandato"  required>
                    </div>
                    <div class="campo">
                        <label for="data_final_mandato">Final do Mandato</label>
                        <input type="date" name="data_final_mandato" id="data_final_mandato"  required>
                    </div>

                    <button type="submit" class="btn-salvar">Salvar</button>

                </form>
            </section>

        </main>

    </div>

    <script>
        function atualizarIdadePreview() {
            const dataNascimento = document.getElementById('data_nascimento').value;
            const preview = document.getElementById('idade-preview');

            if (!dataNascimento) {
                preview.textContent = 'A idade é calculada automaticamente.';
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
