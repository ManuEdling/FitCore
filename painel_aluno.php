<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'aluno') {
    header("Location: index.php?erro=Acesso negado!");
    exit;
}

require 'conexao.php';
$id_aluno = $_SESSION['id'];

// Buscar treinos atribuídos a este aluno pelo personal
$sqlTreinos = "SELECT t.*, u.nome AS personal_nome FROM treinos t
               JOIN usuarios u ON t.id_personal = u.id
               WHERE t.id_aluno = ?
               ORDER BY t.id DESC";
$stmt = $pdo->prepare($sqlTreinos);
$stmt->execute([$id_aluno]);
$treinos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Aluno - FitCore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="dashboard">
    <div class="header-painel">
        <h2>Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?> (Aluno)</h2>
        <a href="index.php" class="btn-sair">Sair</a>
    </div>

    <p>Aqui estão os seus treinos criados pelo seu Personal Trainer. Escolha um treino para começar:</p>

    <h3 style="margin-top: 20px; color: #d4af37;">Meus Treinos</h3>
    <div class="menu-cards">
        <?php if(count($treinos) > 0): ?>
            <?php foreach($treinos as $tr): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($tr['titulo']); ?></h3>
                    <p>Personal: <?php echo htmlspecialchars($tr['personal_nome']); ?></p>
                    <?php if(!empty($tr['observacoes'])): ?>
                        <p style="font-size: 12px; color: #888;">Obs: <?php echo htmlspecialchars($tr['observacoes']); ?></p>
                    <?php endif; ?>
                    <a href="executar_treino.php?id=<?php echo $tr['id']; ?>">Iniciar Treino</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: #888; text-align: center; grid-column: 1 / -1;">Nenhum treino atribuído a você no momento.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>