<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise Estatística - Relatório</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Relatório de Análise</h1>

    <?php
    // Verifica se a página foi acessada enviando o formulário
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['processar'])) {
        $turma_nome = $_POST['turma_nome'] ?? 'Não informada';
        $nomes = $_POST['nome'] ?? [];
        $notas1 = $_POST['nota1'] ?? [];
        $notas2 = $_POST['nota2'] ?? [];
        $trabalhos = $_POST['trabalho'] ?? [];

        $total_alunos = count($nomes);

        if ($total_alunos > 0) {
            $soma_medias_turma = 0;
            $maior_media = -1;
            $menor_media = 11;
            $aprovados = 0;
            $recuperacoes = 0;
            $reprovados = 0;
            $soma_total_notas = 0;

            $alunos_processados = [];

            for ($i = 0; $i < $total_alunos; $i++) {
                $nome = htmlspecialchars($nomes[$i]);
                $n1 = (float)$notas1[$i];
                $n2 = (float)$notas2[$i];
                $trab = (float)$trabalhos[$i];

                $media = ($n1 + $n2 + $trab) / 3;
                $soma_notas = $n1 + $n2 + $trab;
                $raiz_soma = sqrt($soma_notas);
                $maior_nota = max($n1, $n2, $trab);
                $menor_nota = min($n1, $n2, $trab);
                $dif_absoluta = abs($maior_nota - $menor_nota);

                if ($media >= 7.0) {
                    $situacao = "Aprovado";
                    $classe_css = "aprovado";
                    $aprovados++;
                } elseif ($media >= 5.0) {
                    $situacao = "Recuperação";
                    $classe_css = "recuperacao";
                    $recuperacoes++;
                } else {
                    $situacao = "Reprovado";
                    $classe_css = "reprovado";
                    $reprovados++;
                }

                $soma_medias_turma += $media;
                $soma_total_notas += $soma_notas;
                if ($media > $maior_media) $maior_media = $media;
                if ($media < $menor_media) $menor_media = $media;

                $alunos_processados[] = [
                    'nome' => $nome, 'n1' => $n1, 'n2' => $n2, 'trab' => $trab,
                    'media' => $media, 'raiz' => $raiz_soma, 'dif' => $dif_absoluta,
                    'situacao' => $situacao, 'classe' => $classe_css
                ];
            }

            $media_geral = $soma_medias_turma / $total_alunos;
            $percentual_aprovacao = ($aprovados / $total_alunos) * 100;

            echo "<h2>Turma: $turma_nome</h2>";
            
            echo "<table>";
            echo "<thead><tr><th>Aluno</th><th>P1</th><th>P2</th><th>Trab.</th><th>Média</th><th>Situação</th><th>Raiz</th><th>Dif.</th></tr></thead><tbody>";
            foreach ($alunos_processados as $aluno) {
                echo "<tr>
                        <td>{$aluno['nome']}</td>
                        <td>" . number_format($aluno['n1'], 1) . "</td>
                        <td>" . number_format($aluno['n2'], 1) . "</td>
                        <td>" . number_format($aluno['trab'], 1) . "</td>
                        <td><strong>" . number_format($aluno['media'], 2) . "</strong></td>
                        <td class='{$aluno['classe']}'>{$aluno['situacao']}</td>
                        <td>" . number_format($aluno['raiz'], 2) . "</td>
                        <td>" . number_format($aluno['dif'], 1) . "</td>
                      </tr>";
            }
            echo "</tbody></table>";

            echo "<div class='stats-box'>
                    <div>
                        <h3>Médias e Totais</h3>
                        <p>Média Geral: " . number_format($media_geral, 2) . "</p>
                        <p>Maior Média: " . number_format($maior_media, 2) . "</p>
                        <p>Menor Média: " . number_format($menor_media, 2) . "</p>
                        <p>Soma de Notas: " . number_format($soma_total_notas, 1) . "</p>
                    </div>
                    <div>
                        <h3>Contagem</h3>
                        <p class='aprovado'>Aprovados: $aprovados</p>
                        <p class='recuperacao'>Recuperação: $recuperacoes</p>
                        <p class='reprovado'>Reprovados: $reprovados</p>
                        <p>Aprovação: " . number_format($percentual_aprovacao, 1) . "%</p>
                    </div>
                  </div>";

            echo "<div class='feedback'>";
            if ($percentual_aprovacao >= 70) echo "<p class='aprovado'>Desempenho Excelente! A maior parte da turma atingiu os objetivos.</p>";
            elseif ($percentual_aprovacao >= 50) echo "<p class='recuperacao'>Desempenho Regular. É recomendável reforçar o conteúdo.</p>";
            else echo "<p class='reprovado'>Desempenho Crítico. Sugere-se revisão geral do conteúdo.</p>";
            echo "</div>";

            // Botão que volta para a página inicial
            echo "<a href='index.php' class='btn btn-secondary'>Realizar Nova Análise</a>";

        } else {
            echo "<p class='reprovado'>Nenhum aluno foi cadastrado válido.</p>";
            echo "<a href='index.php' class='btn btn-secondary'>Voltar</a>";
        }
    } else {
        // Cai aqui se alguém tentar acessar o processa.php diretamente pela URL
        echo "<h2>Acesso Inválido</h2>";
        echo "<p>Você precisa preencher o formulário primeiro.</p>";
        echo "<a href='index.php' class='btn'>Ir para o Formulário</a>";
    }
    ?>
</div>

</body>
</html>