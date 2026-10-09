<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'personal') {
    header("Location: index.php?erro=Acesso negado!");
    exit;
}

require 'conexao.php';
$id_personal = $_SESSION['id'];

// Buscar alunos já vinculados
$sqlVinculados = "SELECT u.id, u.nome, u.email 
                  FROM usuarios u 
                  JOIN vinculo_personal_aluno v ON u.id = v.id_aluno 
                  WHERE v.id_personal = ?";
$stmt = $pdo->prepare($sqlVinculados);
$stmt->execute([$id_personal]);
$alunosVinculados = $stmt->fetchAll();

// Buscar alunos disponíveis para vínculo
$sqlDisponiveis = "SELECT id, nome, email FROM usuarios 
                   WHERE tipo = 'aluno' 
                   AND id NOT IN (
                       SELECT id_aluno FROM vinculo_personal_aluno WHERE id_personal = ?
                   )";
$stmt2 = $pdo->prepare($sqlDisponiveis);
$stmt2->execute([$id_personal]);
$alunosDisponiveis = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Alunos - FitCore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container-painel">
    <a href="painel_personal.php" class="voltar">&larr; Voltar ao Painel</a>
    <h2>Gerenciamento de Alunos</h2>

    <?php if(isset($_GET['sucesso'])): ?>
        <div class="msg-sucesso"><?php echo htmlspecialchars($_GET['sucesso']); ?></div>
    <?php endif; ?>

    <h3>Meus Alunos Vinculados</h3>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($alunosVinculados) > 0): ?>
                <?php foreach($alunosVinculados as $aluno): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['email']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2" style="text-align: center; color: #888;">Nenhum aluno vinculado no momento.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="form-vincular">
        <h3>Vincular Novo Aluno</h3>
        <form action="vincular_aluno.php" method="POST">
            <div class="input-group">
                <label>Selecione o Aluno</label>
                <select name="id_aluno" required>
                    <option value="">-- Escolha um aluno --</option>
                    <?php foreach($alunosDisponiveis as $disp): ?>
                        <option value="<?php echo $disp['id']; ?>"><?php echo htmlspecialchars($disp['nome']); ?> (<?php echo htmlspecialchars($disp['email']); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">Vincular Aluno</button>
        </form>
    </div>
</div>

</body>
</html>