<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'personal') {
    header("Location: index.php?erro=Acesso negado!");
    exit;
}

require 'conexao.php';
$id_personal = $_SESSION['id'];

// Buscar exercícios padrão do sistema (criado_por IS NULL) ou criados por este personal
$sql = "SELECT * FROM exercicios WHERE criado_por IS NULL OR criado_por = ? ORDER BY grupo_muscular, nome";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_personal]);
$exercicios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Exercícios - FitCore</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .preview-container {
            margin-top: 10px;
            display: none;
            max-width: 150px;
            max-height: 150px;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #444;
        }
        .preview-container img, .preview-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
    </style>
</head>
<body>

<div class="container-painel" style="max-width: 900px;">
    <a href="painel_personal.php" class="voltar">&larr; Voltar ao Painel</a>
    <h2>Gerenciamento de Exercícios</h2>

    <?php if(isset($_GET['sucesso'])): ?>
        <div class="msg-sucesso"><?php echo htmlspecialchars($_GET['sucesso']); ?></div>
    <?php endif; ?>
    <?php if(isset($_GET['erro'])): ?>
        <div class="msg"><?php echo htmlspecialchars($_GET['erro']); ?></div>
    <?php endif; ?>

    <!-- FORMULÁRIO PARA CADASTRAR EXERCÍCIO COM UPLOAD E PRÉ-VISUALIZAÇÃO -->
    <div class="form-vincular" style="margin-bottom: 30px;">
        <h3>Cadastrar Novo Exercício</h3>
        <form action="cadastrar_exercicio.php" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label>Nome do Exercício</label>
                <input type="text" name="nome" placeholder="Ex: Supino Reto, Agachamento Livre" required>
            </div>
            <div class="input-group">
                <label>Grupo Muscular</label>
                <input type="text" name="grupo_muscular" placeholder="Ex: Peito, Pernas, Costas" required>
            </div>
            <div class="input-group">
                <label>Mídia Explicativa (Imagem, GIF ou Vídeo)</label>
                <input type="file" name="foto" id="inputFoto" accept="image/*,video/*" onchange="previewMidia(event)">
                
                <!-- Área de Pré-visualização antes de enviar -->
                <div id="previewBox" class="preview-container">
                    <img id="imgPreview" src="#" alt="Pré-visualização" style="display:none;">
                    <video id="videoPreview" controls style="display:none; width: 150px; height: 150px;"></video>
                </div>
            </div>
            <button type="submit">Cadastrar Exercício</button>
        </form>
    </div>

    <!-- LISTA DE EXERCÍCIOS -->
    <h3>Exercícios Disponíveis</h3>
    <table>
        <thead>
            <tr>
                <th>Mídia</th>
                <th>Nome</th>
                <th>Grupo Muscular</th>
                <th>Origem</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($exercicios) > 0): ?>
                <?php foreach($exercicios as $ex): ?>
                    <tr>
                        <td>
                            <?php if(!empty($ex['foto'])): ?>
                                <?php 
                                    $ext = strtolower(pathinfo($ex['foto'], PATHINFO_EXTENSION));
                                    if(in_array($ext, ['mp4', 'webm', 'ogg'])): 
                                ?>
                                    <video width="50" height="50" style="object-fit: cover; border-radius: 4px;" controls>
                                        <source src="<?php echo htmlspecialchars($ex['foto']); ?>" type="video/<?php echo $ext; ?>">
                                    </video>
                                <?php else: ?>
                                    <img src="<?php echo htmlspecialchars($ex['foto']); ?>" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color: #666; font-size: 12px;">Sem mídia</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($ex['nome']); ?></td>
                        <td><?php echo htmlspecialchars($ex['grupo_muscular']); ?></td>
                        <td><?php echo ($ex['criado_por'] === null) ? 'Sistema' : 'Personal (Você)'; ?></td>
                        <td>
                            <?php if($ex['criado_por'] === $id_personal): ?>
                                <a href="excluir_exercicio.php?id=<?php echo $ex['id']; ?>" style="color: #ff6b6b; text-decoration: none; font-weight: bold;" onclick="return confirm('Deseja realmente apagar este exercício?')">Excluir</a>
                            <?php else: ?>
                                <span style="color: #666; font-size: 12px;">Padrão</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #888;">Nenhum exercício cadastrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Script de Pré-visualização da Mídia -->
<script>
function previewMidia(event) {
    const file = event.target.files[0];
    const previewBox = document.getElementById('previewBox');
    const imgPreview = document.getElementById('imgPreview');
    const videoPreview = document.getElementById('videoPreview');

    if (file) {
        const fileURL = URL.createObjectURL(file);
        previewBox.style.display = 'block';

        if (file.type.startsWith('video/')) {
            videoPreview.src = fileURL;
            videoPreview.style.display = 'block';
            imgPreview.style.display = 'none';
        } else {
            imgPreview.src = fileURL;
            imgPreview.style.display = 'block';
            videoPreview.style.display = 'none';
        }
    } else {
        previewBox.style.display = 'none';
    }
}
</script>

</body>
</html>