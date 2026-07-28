<?php

require_once '../includes/validacao.php';
require_once '../config/conexao.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id_categoria = (int) ($_POST['id_categoria'] ?? 0);

    if (empty($id_categoria)) {
        header("Location: ../categorias.php?erro=id_invalido");
        exit;
    }

    try{
        $sql_verifica = "SELECT COUNT(*) FROM produtos WHERE Id_categoria = :id_categoria";
        $stmt_verifica = $pdo->prepare($sql_verifica);
        $stmt_verifica->bindParam(':id_categoria', $id_categoria);
        $stmt_verifica->execute();

        $quantidade_produtos = $stmt_verifica->fetchColumn();

        if($quantidade_produtos >0){
            header("Location:../index.php?erro=categoria_em_uso");
            exit;
        }

        $sql_excluir = "DELETE FROM categorias WHERE Id_categoria = :id_categoria";
        $stmt_excluir = $pdo->prepare($sql_excluir);
        $stmt_excluir->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmt_excluir->execute();

        header("Location: ../index.php?sucesso=categoria_excluida");
        exit;

    } catch (PDOException $e) {
        die("Erro ao processar a exclusão da categoria: " . $e->getMessage());
    }
} else{
    header("Location: ../index.php");
    exit;
}