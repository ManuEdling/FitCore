<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'personal') {
    header("Location: index.php?erro=Acesso negado!");
    exit;
}

require 'conexao.php';
$id_personal = $_SESSION['id'];
$id_exercicio = $_GET['id'] ?? null;

if ($id_exercicio) {
    // Verificar se o exercício pertence a este personal antes de apagar
    $sqlBusca = "SELECT foto FROM exercicios WHERE id = ? AND criado_por = ?";
    $stmtBusca = $pdo->prepare($sqlBusca);
    $stmtBusca->execute([$id_exercicio, $id_personal]);
    $exercicio = $stmtBusca->fetch();

    if ($exercicio) {
        // Se houver arquivo de mídia salvo na pasta uploads, apagar o arquivo físico também
        if (!empty($exercicio['foto']) && file_exists($exercicio['foto'])) {
            unlink($exercicio['foto']);
        }

        // Deletar do banco de dados
        $sqlDel = "DELETE FROM exercicios WHERE id = ? AND criado_por = ?";
        $stmtDel = $pdo->prepare($sqlDel);
        $stmtDel->execute([$id_exercicio, $id_personal]);

        header("Location: exercicios.php?sucesso=Exercício excluído com sucesso!");
        exit;
    }
}

header("Location: exercicios.php?erro=Não foi possível excluir o exercício.");
exit;