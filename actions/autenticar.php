<?php
session_start();
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $usuario = trim($_POST['usuario']);
    $senha_digitada = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT Id_usuario, usuario, senha FROM usuario WHERE usuario = :usuario");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();

    $usuario_db = $stmt->fetch();

    if ($usuario_db && password_verify($senha_digitada, $usuario_db['senha'])) {
        
        $_SESSION['logado'] = true;
        $_SESSION['id_usuario'] = $usuario_db['Id_usuario'];
        $_SESSION['nome_usuario'] = $usuario_db['usuario'];

        header("Location: ../index.php");
        exit;
        
    } else {
        header("Location: ../login.php?erro=1");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}
