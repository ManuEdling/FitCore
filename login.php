<?php
// Ativar exibição de erros temporariamente para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        // Salvar dados na sessão
        $_SESSION['id'] = $user['id'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['tipo'] = $user['tipo'];

        // Redirecionar conforme o tipo de conta
        if ($user['tipo'] === 'personal') {
            header("Location: painel_personal.php");
        } else {
            header("Location: painel_aluno.php");
        }
        exit;
    } else {
        header("Location: index.php?erro=E-mail ou senha incorretos!");
        exit;
    }
}
?>