<?php
session_start();
// Verifica se o usuário está logado e se é do tipo 'personal'
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'personal') {
    header("Location: index.php?erro=Acesso negado!");
    exit;
}

require 'conexao.php';
$id_personal = $_SESSION['id'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Personal - FitCore</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dashboard { max-width: 800px; margin: 50px auto; background: #1e1e1e; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
        .header-painel { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header-painel h2 { color: #d4af37; }
        .btn-sair { background: #ff6b6b; color: #fff; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 14px; }
        .btn-sair:hover { background: #ff5252; }
        .menu-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-top: 20px; }
        .card { background: #2a2a2a; padding: 20px; border-radius: 8px; text-align: center; border: 1px solid #444; transition: 0.3s; }
        .card:hover { border-color: #d4af37; transform: translateY(-3px); }
        .card h3 { margin-bottom: 10px; color: #fff; }
        .card p { color: #aaa; font-size: 14px; margin-bottom: 15px; }
        .card a { display: inline-block; padding: 10px 15px; background: #d4af37; color: #121212; font-weight: bold; border-radius: 5px; text-decoration: none; }
        .card a:hover { background: #b89728; }
    </style>
</head>
<body>

<div class="dashboard">
    <div class="header-painel">
        <h2>Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?> (Personal)</h2>
        <a href="index.php" class="btn-sair">Sair</a>
    </div>

    <p>Bem-vindo ao seu painel de controle do FitCore. O que você deseja gerenciar hoje?</p>

    <div class="menu-cards">
        <div class="card">
            <h3>Meus Alunos</h3>
            <p>Veja os alunos vinculados e gerencie novos vínculos.</p>
            <a href="alunos.php">Ver Alunos</a>
        </div>
        <div class="card">
            <h3>Gerenciar Exercícios</h3>
            <p>Cadastre novos exercícios para usar nos treinos.</p>
            <a href="exercicios.php">Ver Exercícios</a>
        </div>
        <div class="card">
            <h3>Montar Treinos</h3>
            <p>Crie e edite fichas de treino personalizadas.</p>
            <a href="treinos.php">Gerenciar Treinos</a>
        </div>
    </div>
</div>

</body>
</html>