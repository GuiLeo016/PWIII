<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise Estatística - Entrada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Análise Estatística Escolar</h1>

    <div id="setup-section">
        <h2>Configuração da Turma</h2>
        <div class="form-group">
            <label>Nome da Turma:</label>
            <input type="text" id="turma_nome" placeholder="Ex: 3º DS - Noite">
        </div>
        <div class="form-group">
            <label>Quantidade de Alunos:</label>
            <input type="number" id="qtd_alunos" min="1">
        </div>
        <button class="btn" onclick="gerarCampos()">Gerar Formulário</button>
    </div>

    <form method="POST" action="result.php" id="form-alunos" style="display:none;">
        <input type="hidden" name="turma_nome" id="hidden_turma_nome">
        <div id="alunos-container"></div>
        <button type="submit" name="processar" class="btn">Processar Dados</button>
        <button type="button" onclick="location.reload()" class="btn btn-secondary">Cancelar</button>
    </form>

    <script>
        function gerarCampos() {
            const turma = document.getElementById('turma_nome').value;
            const qtd = document.getElementById('qtd_alunos').value;
            const container = document.getElementById('alunos-container');
            if (!turma || qtd < 1) { alert("Preencha todos os campos."); return; }

            document.getElementById('hidden_turma_nome').value = turma;
            container.innerHTML = '';
            for (let i = 1; i <= qtd; i++) {
                container.innerHTML += `
                    <div class="student-card">
                        <h4>Aluno ${i}</h4>
                        <label>Nome:</label><input type="text" name="nome[]" required>
                        <label>Prova 1:</label><input type="number" step="0.1" name="nota1[]" required>
                        <label>Prova 2:</label><input type="number" step="0.1" name="nota2[]" required>
                        <label>Trabalho:</label><input type="number" step="0.1" name="trabalho[]" required>
                    </div>`;
            }
            document.getElementById('setup-section').style.display = 'none';
            document.getElementById('form-alunos').style.display = 'block';
        }
    </script>
</div>

</body>
</html>