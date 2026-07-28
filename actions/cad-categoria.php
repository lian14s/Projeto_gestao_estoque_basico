<?php 

require_once '../includes/validacao.php';

require_once '../config/conexao.php';

if($_SERVER ['REQUEST_METHOD'] === 'POST'){

$nome_categoria = trim($_POST['nome']);

if(!empty($nome_categoria)){

try{
$stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (:nome)");

// Vincula a variável ao parâmetro da consulta
$stmt->bindParam(':nome', $nome_categoria);

$stmt ->execute();

header("location: ../index.php?sucesso=categoria");
exit;

} catch (PDOException $e){
    die ("Erro ao salvar categoria: " . $e->getMessage());
}

} else {
header("location: ../index.php??erro=categoria_vazia");
exit;
}

} else {
    header("location: ../index.php");
    exit;
}