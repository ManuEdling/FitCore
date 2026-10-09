<?php
// Ativar exibição de erros temporariamente para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipo = $_POST['tipo'];

    // Verificar se o e-mail já existe
    $sqlCheck = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = $pdo->prepare($sqlCheck);
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        header("Location: index.php?erro=E-mail já cadastrado!");
        exit;
    }

    // Inserir novo usuário
    $sqlInsert = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)";
    $stmtInsert = $pdo->prepare($sqlInsert);
    
    if ($stmtInsert->execute([$nome, $email, $senha, $tipo])) {
        header("Location: index.php?erro=Cadastro realizado com sucesso! Faça login.");
        exit;
    } else {
        header("Location: index.php?erro=Erro ao cadastrar. Tente novamente.");
        exit;
    }
}
?>