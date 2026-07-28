<?php
// actions/delete-produto.php

require_once '../includes/validacao.php';
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id_produto = (int) ($_POST['id_produto'] ?? 0);

    if (empty($id_produto)) {
        header("Location: ../index.php?erro=id_invalido");
        exit;
    }

    try {
        $sql_busca = "SELECT imagem_produto FROM produtos WHERE Id_produto = :id_produto";
        $stmt_busca = $pdo->prepare($sql_busca);
        $stmt_busca->bindParam(':id_produto', $id_produto, PDO::PARAM_INT);
        $stmt_busca->execute();
        
        $produto = $stmt_busca->fetch(PDO::FETCH_ASSOC);

        $sql_delete = "DELETE FROM produtos WHERE Id_produto = :id_produto";
        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->bindParam(':id_produto', $id_produto, PDO::PARAM_INT);
        $stmt_delete->execute();

        if ($produto && !empty($produto['imagem_produto'])) {
            $caminho_arquivo = '../' . $produto['imagem_produto'];
            
            if (file_exists($caminho_arquivo)) {
                unlink($caminho_arquivo); 
            }
        }

        header("Location: ../index.php?sucesso=excluido");
        exit;

    } catch (PDOException $e) {
        die("Erro ao excluir o produto: " . $e->getMessage());
    }

} else {
    header("Location: ../index.php");
    exit;
}
