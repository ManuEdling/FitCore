<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'aluno') {
    header("Location: index.php?erro=Acesso negado!");
    exit;
}

require 'conexao.php';
$id_aluno = $_SESSION['id'];
$id_treino = $_GET['id'] ?? null;

if (!$id_treino) {
    header("Location: painel_aluno.php");
    exit;
}

// Buscar dados do treino e validar se pertence ao aluno
$sqlTreino = "SELECT t.*, u.nome AS personal_nome FROM treinos t 
              JOIN usuarios u ON t.id_personal = u.id 
              WHERE t.id = ? AND t.id_aluno = ?";
$stmtTreino = $pdo->prepare($sqlTreino);
$stmtTreino->execute([$id_treino, $id_aluno]);
$treino = $stmtTreino->fetch();

if (!$treino) {
    header("Location: painel_aluno.php?erro=Treino não encontrado.");
    exit;
}

// Processar salvamento de carga
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['salvar_carga'])) {
    $id_treino_exercicio = $_POST['id_treino_exercicio'];
    $carga_kg = $_POST['carga_kg'];
    $data_registro = date('Y-m-d');

    // Inserir na tabela registro_cargas
    $sqlCarga = "INSERT INTO registro_cargas (id_aluno, id_treino_exercicio, carga_kg, data_registro) VALUES (?, ?, ?, ?)";
    $stmtCarga = $pdo->prepare($sqlCarga);
    $stmtCarga->execute([$id_aluno, $id_treino_exercicio, $carga_kg, $data_registro]);

    header("Location: executar_treino.php?id=$id_treino&sucesso=Carga salva com sucesso!");
    exit;
}

// Buscar exercícios do treino
$sqlItens = "SELECT te.*, e.nome AS exercicio_nome, e.grupo_muscular, e.foto 
             FROM treino_exercicios te
             JOIN exercicios e ON te.id_exercicio = e.id
             WHERE te.id_treino = ?
             ORDER BY te.ordem ASC";
$stmtItens = $pdo->prepare($sqlItens);
$stmtItens->execute([$id_treino]);
$exerciciosTreino = $stmtItens->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executar Treino - FitCore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container-painel">
    <a href="painel_aluno.php" class="voltar">&larr; Voltar aos Meus Treinos</a>
    <h2><?php echo htmlspecialchars($treino['titulo']); ?></h2>
    <p style="color: #aaa; margin-bottom: 20px;">Personal: <strong><?php echo htmlspecialchars($treino['personal_nome']); ?></strong></p>

    <?php if(!empty($treino['observacoes'])): ?>
        <p style="background: #252525; padding: 12px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #d4af37;">
            <strong>Observações do Personal:</strong> <?php echo htmlspecialchars($treino['observacoes']); ?>
        </p>
    <?php endif; ?>

    <?php if(isset($_GET['sucesso'])): ?>
        <div class="msg-sucesso"><?php echo htmlspecialchars($_GET['sucesso']); ?></div>
    <?php endif; ?>

    <h3>Lista de Exercícios</h3>
    
    <?php if(count($exerciciosTreino) > 0): ?>
        <?php foreach($exerciciosTreino as $item): ?>
            <?php 
                // Buscar a última carga registrada para este exercício específico por este aluno
                $sqlUltimaCarga = "SELECT carga_kg, data_registro FROM registro_cargas 
                                   WHERE id_aluno = ? AND id_treino_exercicio = ? 
                                   ORDER BY data_registro DESC, id DESC LIMIT 1";
                $stmtCargaAntiga = $pdo->prepare($sqlUltimaCarga);
                $stmtCargaAntiga->execute([$id_aluno, $item['id']]);
                $ultimaCarga = $stmtCargaAntiga->fetch();
            ?>
            <div class="exercicio-card">
                <h3><?php echo $item['ordem']; ?>. <?php echo htmlspecialchars($item['exercicio_nome']); ?></h3>
                <div class="detalhes-ex">
                    <span>Grupo: <strong><?php echo htmlspecialchars($item['grupo_muscular']); ?></strong></span>
                    <span>Séries: <strong><?php echo $item['series']; ?></strong></span>
                    <span>Repetições: <strong><?php echo htmlspecialchars($item['repeticoes']); ?></strong></span>
                    <span>Descanso: <strong><?php echo $item['descanso_segundos']; ?>s</strong></span>
                </div>

                <!-- Exibição da última carga registrada -->
                <div style="background: #1f1f1f; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #333; font-size: 13px; color: #ccc;">
                    <?php if($ultimaCarga): ?>
                        🔥 <strong>Última Carga Registrada:</strong> <span style="color: #d4af37; font-size: 15px; font-weight: bold;"><?php echo $ultimaCarga['carga_kg']; ?> kg</span> 
                        <span style="color: #777; font-size: 11px;">(em <?php echo date('d/m/Y', strtotime($ultimaCarga['data_registro'])); ?>)</span>
                    <?php else: ?>
                        💡 <strong>Última Carga Registrada:</strong> <span style="color: #888;">Nenhum registro anterior</span>
                    <?php endif; ?>
                </div>

                <!-- Exibição da Mídia (Imagem, GIF ou Vídeo enviado) -->
                <?php if(!empty($item['foto'])): ?>
                    <div style="margin-bottom: 15px;">
                        <?php 
                            $ext = strtolower(pathinfo($item['foto'], PATHINFO_EXTENSION));
                            if(in_array($ext, ['mp4', 'webm', 'ogg'])): 
                        ?>
                            <video width="100%" style="max-width: 300px; max-height: 200px; border-radius: 6px;" controls>
                                <source src="<?php echo htmlspecialchars($item['foto']); ?>" type="video/<?php echo $ext; ?>">
                                Seu navegador não suporta vídeos.
                            </video>
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($item['foto']); ?>" alt="Demonstração" style="max-width: 200px; max-height: 150px; border-radius: 6px; object-fit: cover; display: block;">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Formulário para anotar carga -->
                <form action="executar_treino.php?id=<?php echo $id_treino; ?>" method="POST" class="form-carga">
                    <input type="hidden" name="salvar_carga" value="1">
                    <input type="hidden" name="id_treino_exercicio" value="<?php echo $item['id']; ?>">
                    <div class="input-group" style="margin-bottom: 0; flex: 1;">
                        <input type="number" step="0.5" name="carga_kg" placeholder="Carga (kg)" required>
                    </div>
                    <button type="submit">Salvar Carga</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: #888; text-align: center;">Este treino ainda não possui exercícios cadastrados.</p>
    <?php endif; ?>

    <a href="painel_aluno.php" class="btn-finalizar">Finalizar Treino</a>
</div>

</body>
</html>