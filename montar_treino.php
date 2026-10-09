<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'personal') {
    header("Location: index.php?erro=Acesso negado!");
    exit;
}

require 'conexao.php';
$id_personal = $_SESSION['id'];
$id_treino = $_GET['id'] ?? null;

if (!$id_treino) {
    header("Location: treinos.php");
    exit;
}

// Buscar dados do treino e validar se pertence ao personal
$sqlTreino = "SELECT t.*, u.nome AS aluno_nome FROM treinos t 
              JOIN usuarios u ON t.id_aluno = u.id 
              WHERE t.id = ? AND t.id_personal = ?";
$stmtTreino = $pdo->prepare($sqlTreino);
$stmtTreino->execute([$id_treino, $id_personal]);
$treino = $stmtTreino->fetch();

if (!$treino) {
    header("Location: treinos.php?erro=Treino não encontrado.");
    exit;
}

// Adicionar exercício ao treino
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adicionar_exercicio'])) {
    $id_exercicio = $_POST['id_exercicio'];
    $series = $_POST['series'];
    $repeticoes = trim($_POST['repeticoes']);
    $descanso_segundos = $_POST['descanso_segundos'];
    $ordem = $_POST['ordem'];

    $sqlAdd = "INSERT INTO treino_exercicios (id_treino, id_exercicio, series, repeticoes, descanso_segundos, ordem) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtAdd = $pdo->prepare($sqlAdd);
    $stmtAdd->execute([$id_treino, $id_exercicio, $series, $repeticoes, $descanso_segundos, $ordem]);
    
    header("Location: montar_treino.php?id=$id_treino&sucesso=Exercício adicionado!");
    exit;
}

// Excluir exercício do treino
if (isset($_GET['excluir_ex'])) {
    $id_te = $_GET['excluir_ex'];
    $sqlDel = "DELETE FROM treino_exercicios WHERE id = ? AND id_treino = ?";
    $stmtDel = $pdo->prepare($sqlDel);
    $stmtDel->execute([$id_te, $id_treino]);
    
    header("Location: montar_treino.php?id=$id_treino");
    exit;
}

// Buscar exercícios já vinculados a este treino
$sqlItens = "SELECT te.*, e.nome AS exercicio_nome, e.grupo_muscular 
             FROM treino_exercicios te
             JOIN exercicios e ON te.id_exercicio = e.id
             WHERE te.id_treino = ?
             ORDER BY te.ordem ASC";
$stmtItens = $pdo->prepare($sqlItens);
$stmtItens->execute([$id_treino]);
$exerciciosTreino = $stmtItens->fetchAll();

// Buscar lista de exercícios disponíveis para adicionar
$sqlDisponiveis = "SELECT * FROM exercicios WHERE criado_por IS NULL OR criado_por = ? ORDER BY nome";
$stmtDisp = $pdo->prepare($sqlDisponiveis);
$stmtDisp->execute([$id_personal]);
$exerciciosDisp = $stmtDisp->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Montar Treino - FitCore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container-painel">
    <a href="treinos.php" class="voltar">&larr; Voltar para Treinos</a>
    <h2>Treino: <?php echo htmlspecialchars($treino['titulo']); ?></h2>
    <p style="color: #aaa; margin-bottom: 20px;">Aluno: <strong><?php echo htmlspecialchars($treino['aluno_nome']); ?></strong></p>

    <?php if(isset($_GET['sucesso'])): ?>
        <div class="msg-sucesso"><?php echo htmlspecialchars($_GET['sucesso']); ?></div>
    <?php endif; ?>

    <!-- FORMULÁRIO PARA ADICIONAR EXERCÍCIO AO TREINO -->
    <div class="form-vincular" style="margin-bottom: 30px;">
        <h3>Adicionar Exercício ao Treino</h3>
        <form action="montar_treino.php?id=<?php echo $id_treino; ?>" method="POST">
            <input type="hidden" name="adicionar_exercicio" value="1">
            <div class="input-group">
                <label>Exercício</label>
                <select name="id_exercicio" required>
                    <option value="">-- Selecione o Exercício --</option>
                    <?php foreach($exerciciosDisp as $ex): ?>
                        <option value="<?php echo $ex['id']; ?>"><?php echo htmlspecialchars($ex['nome']); ?> (<?php echo htmlspecialchars($ex['grupo_muscular']); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <div class="input-group" style="flex: 1;">
                    <label>Séries</label>
                    <input type="number" name="series" value="3" required>
                </div>
                <div class="input-group" style="flex: 1;">
                    <label>Repetições</label>
                    <input type="text" name="repeticoes" placeholder="Ex: 10-12" required>
                </div>
                <div class="input-group" style="flex: 1;">
                    <label>Descanso (seg)</label>
                    <input type="number" name="descanso_segundos" value="60">
                </div>
                <div class="input-group" style="flex: 1;">
                    <label>Ordem</label>
                    <input type="number" name="ordem" value="1">
                </div>
            </div>
            <button type="submit">Adicionar ao Treino</button>
        </form>
    </div>

    <!-- LISTA DE EXERCÍCIOS DESTE TREINO -->
    <h3>Exercícios na Ficha</h3>
    <table>
        <thead>
            <tr>
                <th>Ordem</th>
                <th>Exercício</th>
                <th>Séries</th>
                <th>Repetições</th>
                <th>Descanso</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($exerciciosTreino) > 0): ?>
                <?php foreach($exerciciosTreino as $item): ?>
                    <tr>
                        <td><?php echo $item['ordem']; ?></td>
                        <td><?php echo htmlspecialchars($item['exercicio_nome']); ?></td>
                        <td><?php echo $item['series']; ?></td>
                        <td><?php echo htmlspecialchars($item['repeticoes']); ?></td>
                        <td><?php echo $item['descanso_segundos']; ?>s</td>
                        <td>
                            <a href="montar_treino.php?id=<?php echo $id_treino; ?>&excluir_ex=<?php echo $item['id']; ?>" style="color: #ff6b6b; text-decoration: none; font-weight: bold;" onclick="return confirm('Deseja remover este exercício do treino?')">Remover</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #888;">Nenhum exercício adicionado a este treino ainda.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>