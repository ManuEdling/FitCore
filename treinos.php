<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'personal') {
    header("Location: index.php?erro=Acesso negado!");
    exit;
}

require 'conexao.php';
$id_personal = $_SESSION['id'];

// Processar a criação de um novo treino
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['criar_treino'])) {
    $id_aluno = $_POST['id_aluno'];
    $titulo = trim($_POST['titulo']);
    $observacoes = trim($_POST['observacoes']);

    $sql = "INSERT INTO treinos (id_aluno, id_personal, titulo, observacoes) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_aluno, $id_personal, $titulo, $observacoes]);
    
    header("Location: treinos.php?sucesso=Treino criado com sucesso!");
    exit;
}

// Processar exclusão de treino
if (isset($_GET['excluir'])) {
    $id_treino_del = $_GET['excluir'];
    $sqlDel = "DELETE FROM treinos WHERE id = ? AND id_personal = ?";
    $stmtDel = $pdo->prepare($sqlDel);
    $stmtDel->execute([$id_treino_del, $id_personal]);

    header("Location: treinos.php?sucesso=Treino excluído com sucesso!");
    exit;
}

// Buscar alunos vinculados a este personal para o select do formulário
$sqlAlunos = "SELECT u.id, u.nome FROM usuarios u 
              JOIN vinculo_personal_aluno v ON u.id = v.id_aluno 
              WHERE v.id_personal = ?";
$stmtAlunos = $pdo->prepare($sqlAlunos);
$stmtAlunos->execute([$id_personal]);
$alunosVinculados = $stmtAlunos->fetchAll();

// Buscar todos os treinos criados por este personal agrupados por aluno
$sqlTreinos = "SELECT t.*, u.id AS aluno_id, u.nome AS aluno_nome FROM treinos t
               JOIN usuarios u ON t.id_aluno = u.id
               WHERE t.id_personal = ?
               ORDER BY u.nome ASC, t.id DESC";
$stmtTreinos = $pdo->prepare($sqlTreinos);
$stmtTreinos->execute([$id_personal]);
$todosTreinos = $stmtTreinos->fetchAll();

// Organizar os treinos por aluno para facilitar a exibição em blocos
$treinosPorAluno = [];
foreach ($todosTreinos as $tr) {
    $treinosPorAluno[$tr['aluno_id']]['nome'] = $tr['aluno_nome'];
    $treinosPorAluno[$tr['aluno_id']]['treinos'][] = $tr;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Treinos - FitCore</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .aluno-bloco {
            background: #222;
            border: 1px solid #333;
            border-radius: 8px;
            margin-bottom: 15px;
            overflow: hidden;
        }
        .aluno-header {
            background: #2a2a2a;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: 0.2s;
        }
        .aluno-header:hover {
            background: #323232;
        }
        .aluno-header h3 {
            margin: 0;
            color: #d4af37;
            font-size: 16px;
            text-align: left;
        }
        .treinos-lista {
            padding: 15px 20px;
            display: none; /* Recolhido por padrão para manter a tela limpa */
            border-top: 1px solid #333;
            background: #1e1e1e;
        }
        .treino-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #2a2a2a;
        }
        .treino-item:last-child {
            border-bottom: none;
        }
        .acoes-treino a {
            margin-left: 15px;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="container-painel" style="max-width: 850px;">
    <a href="painel_personal.php" class="voltar">&larr; Voltar ao Painel</a>
    <h2>Gerenciamento de Treinos</h2>

    <?php if(isset($_GET['sucesso'])): ?>
        <div class="msg-sucesso"><?php echo htmlspecialchars($_GET['sucesso']); ?></div>
    <?php endif; ?>

    <!-- FORMULÁRIO PARA CRIAR TREINO -->
    <div class="form-vincular" style="margin-bottom: 30px;">
        <h3>Criar Nova Ficha de Treino</h3>
        <form action="treinos.php" method="POST">
            <input type="hidden" name="criar_treino" value="1">
            <div class="input-group">
                <label>Selecionar Aluno</label>
                <select name="id_aluno" required>
                    <option value="">-- Escolha o Aluno --</option>
                    <?php foreach($alunosVinculados as $aluno): ?>
                        <option value="<?php echo $aluno['id']; ?>"><?php echo htmlspecialchars($aluno['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <label>Título do Treino</label>
                <input type="text" name="titulo" placeholder="Ex: Treino A - Superior, Perna Foco em Quadríceps" required>
            </div>
            <div class="input-group">
                <label>Observações / Recomendações (Opcional)</label>
                <input type="text" name="observacoes" placeholder="Ex: Fazer aquecimento articular antes de iniciar">
            </div>
            <button type="submit">Criar Treino e Adicionar Exercícios</button>
        </form>
    </div>

    <!-- LISTA DE TREINOS AGRUPADOS POR ALUNO (ACORDEÃO) -->
    <h3>Treinos Criados por Aluno</h3>
    
    <?php if(count($treinosPorAluno) > 0): ?>
        <?php foreach($treinosPorAluno as $idAluno => $dados): ?>
            <div class="aluno-bloco">
                <!-- Cabeçalho clicável para expandir/recolher -->
                <div class="aluno-header" onclick="toggleTreinos(<?php echo $idAluno; ?>)">
                    <h3>👤 <?php echo htmlspecialchars($dados['nome']); ?> <span style="font-size: 12px; color: #888; font-weight: normal;">(<?php echo count($dados['treinos']); ?> treino(s))</span></h3>
                    <span style="color: #d4af37; font-size: 14px;" id="icone-<?php echo $idAluno; ?>">▼ Expandir</span>
                </div>

                <!-- Lista de treinos daquele aluno -->
                <div class="treinos-lista" id="lista-<?php echo $idAluno; ?>">
                    <?php foreach($dados['treinos'] as $tr): ?>
                        <div class="treino-item">
                            <div>
                                <strong style="color: #fff;"><?php echo htmlspecialchars($tr['titulo']); ?></strong>
                                <?php if(!empty($tr['observacoes'])): ?>
                                    <div style="font-size: 12px; color: #888; margin-top: 2px;">Obs: <?php echo htmlspecialchars($tr['observacoes']); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="acoes-treino">
                                <a href="montar_treino.php?id=<?php echo $tr['id']; ?>" style="color: #d4af37;">Gerenciar Exercícios</a>
                                <a href="treinos.php?excluir=<?php echo $tr['id']; ?>" style="color: #ff6b6b;" onclick="return confirm('Deseja realmente excluir este treino completo?')">Excluir</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center; color: #888; padding: 20px; background: #2a2a2a; border-radius: 5px;">Nenhum treino criado ainda.</p>
    <?php endif; ?>
</div>

<!-- Script para abrir/fechar os blocos de treino por aluno -->
<script>
function toggleTreinos(idAluno) {
    const lista = document.getElementById('lista-' + idAluno);
    const icone = document.getElementById('icone-' + idAluno);
    
    if (lista.style.display === 'block') {
        lista.style.display = 'none';
        icone.textContent = '▼ Expandir';
    } else {
        lista.style.display = 'block';
        icone.textContent = '▲ Recolher';
    }
}
</script>

</body>
</html>