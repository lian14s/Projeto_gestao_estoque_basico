<?php

require_once '../includes/validacao.php';

require_once '../config/conexao.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id_categoria = (int)($_POST['id_categoria'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');

    if(empty($id_categoria) || empty($nome)){
        header("Location: ../index.php?erro=campos_obrigatorios_categoria");
        exit;
    }

    try{

    $sql = "UPDATE categorias SET nome = :nome WHERE Id_categoria = :Id_categoria";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome' , $nome);
    $stmt->bindParam(':Id_categoria', $id_categoria,PDO::PARAM_INT);
    $stmt->execute();

    header("Location: ../index.php?sucesso=categoria");
    exit;

    } catch(PDOException $e){
        die("Erro ao atualizar a categoria" . $e->getMessage());
    }

} else{
    header("Location:../index.php");
    exit;
}