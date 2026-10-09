<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'personal') {
    header("Location: index.php?erro=Acesso negado!");
    exit;
}

require 'conexao.php';
$id_personal = $_SESSION['id'];

// Buscar alunos vinculados a este personal para o filtro
$sqlAlunos = "SELECT u.id, u.nome FROM usuarios u 
              JOIN vinculo_personal_aluno v ON u.id = v.id_aluno 
              WHERE v.id_personal = ?";
$stmtAlunos = $pdo->prepare($sqlAlunos);
$stmtAlunos.execute([$id_personal]);
$alunosVinculados = $stmtAlunos->fetchAll();

$id_aluno_selecionado = $_GET['id_aluno'] ?? null;
$historico = [];

if ($id_aluno_selecionado) {
    // Buscar o histórico de cargas do aluno selecionado
    $sqlHistorico = "SELECT rc.data_registro, rc.carga_kg, e.nome AS exercicio_nome, t.titulo AS treino_titulo
                     FROM registro_cargas rc
                     JOIN treino_exercicios te ON rc.id_treino_exercicio = te.id
                     JOIN exercicios e ON te.id_exercicio = e.id
                     JOIN treinos t ON te.id_treino = t.id
                     WHERE rc.id_aluno = ?
                     ORDER BY rc.data_registro DESC, rc.id DESC";
    $stmtHist = $pdo->prepare($sqlHistorico);
    $stmtHist->execute([$id_aluno_selecionado]);
    $historico = $stmtHist->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Cargas - FitCore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container-painel">
    <a href="painel_personal.php" class="voltar">&larr; Voltar ao Painel</a>
    <h2>Histórico de Cargas dos Alunos</h2>
    <p style="color: #aaa; margin-bottom: 20px;">Acompanhe a evolução de carga dos seus alunos em tempo real.</p>

    <!-- FORMULÁRIO DE SELEÇÃO DE ALUNO -->
    <div class="form-vincular" style="margin-bottom: 30px;">
        <form action="historico_cargas.php" method="GET">
            <div class="input-group">
                <label>Selecione o Aluno para ver o Histórico</label>
                <select name="id_aluno" onchange="this.form.submit()" required>
                    <option value="">-- Escolha um Aluno --</option>
                    <?php foreach($alunosVinculados as $aluno): ?>
                        <option value="<?php echo $aluno['id']; ?>" <?php echo ($id_aluno_selecionado == $aluno['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($aluno['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <!-- TABELA DE HISTÓRICO -->
    <?php if($id_aluno_selecionado): ?>
        <h3>Registros de Carga</h3>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Treino</th>
                    <th>Exercício</th>
                    <th>Carga Registrada</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($historico) > 0): ?>
                    <?php foreach($historico as $h): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($h['data_registro'])); ?></td>
                            <td><?php echo htmlspecialchars($h['treino_titulo']); ?></td>
                            <td><?php echo htmlspecialchars($h['exercicio_nome']); ?></td>
                            <td><strong><?php echo $h['carga_kg']; ?> kg</strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #888;">Nenhum registro de carga encontrado para este aluno.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>