<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'personal') {
    header("Location: index.php?erro=Acesso negado!");
    exit;
}

require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $grupo_muscular = trim($_POST['grupo_muscular']);
    $criado_por = $_SESSION['id'];
    $caminho_arquivo = null;

    // Verificar se foi enviado um arquivo sem erros
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $arquivoTemp = $_FILES['foto']['tmp_name'];
        $nomeOriginal = basename($_FILES['foto']['name']);
        
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
        $novoNome = uniqid('ex_') . '.' . $extensao;
        
        $pastaDestino = 'uploads/';
        
        if (!is_dir($pastaDestino)) {
            mkdir($pastaDestino, 0777, true);
        }

        $caminhoDestino = $pastaDestino . $novoNome;

        if (move_uploaded_file($arquivoTemp, $caminhoDestino)) {
            $caminho_arquivo = $caminhoDestino;
        }
    }

    try {
        $sql = "INSERT INTO exercicios (nome, grupo_muscular, criado_por, foto) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $grupo_muscular, $criado_por, $caminho_arquivo]);

        header("Location: exercicios.php?sucesso=Exercício cadastrado com sucesso!");
        exit;
    } catch (\PDOException $e) {
        // Mostra o erro real do banco na URL para sabermos se é o nome duplicado ou outra coisa
        $erroMsg = urlencode("Erro no banco: " . $e->getMessage());
        header("Location: exercicios.php?erro=$erroMsg");
        exit;
    }
}
?>