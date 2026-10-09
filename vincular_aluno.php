<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'personal') {
    header("Location: index.php?erro=Acesso negado!");
    exit;
}

require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_personal = $_SESSION['id'];
    $id_aluno = $_POST['id_aluno'];

    // Inserir o vínculo na tabela vinculo_personal_aluno
    $sql = "INSERT INTO vinculo_personal_aluno (id_personal, id_aluno) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$id_personal, $id_aluno])) {
        header("Location: alunos.php?sucesso=Aluno vinculado com sucesso!");
        exit;
    } else {
        header("Location: alunos.php?erro=Erro ao vincular aluno.");
        exit;
    }
}
?>