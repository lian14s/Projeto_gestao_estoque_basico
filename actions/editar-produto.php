<?php

require_once '../includes/validacao.php';

require_once '../config/conexao.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
$id_produto = (int)($_POST['id_produto'] ?? 0);

$nome = trim($_POST['nome'] ?? '');
$id_categoria = $_POST['Id_categoria'] ?? null;
$quantidade = (int) ($_POST['quantidade'] ?? 0);
$estoque_minimo = (int) ($_POST['estoque_minimo'] ?? 0);
$preco_custo = (float) str_replace(',', '.', $_POST['preco_custo'] ?? 0);
$margem_lucro = (float) str_replace(',', '.', $_POST['margem'] ?? 0);

if(empty($id_produto) || empty($nome) || empty($id_categoria)){
    header("Location: ../index.php?erro=campos_obrigatorios_edicao");
    exit;
}

$valor_lucro = $preco_custo * ($margem_lucro/100);
$preco_venda = $preco_custo + $valor_lucro;

$caminho_imagem_banco = null;
$atualizar_imagem = false;

if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK){
    $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
    $extensoes_permitidas = ['jpg','jpeg','png','webp'];

    if(in_array($extensao, $extensoes_permitidas)){
        $novo_nome_imagem = uniqid() . '.' . $extensao;

        $diretorio_destino = '../upload/produtos/';

        if(!is_dir($diretorio_destino)){
            mkdir($diretorio_destino, 0777, true);
        }
        $caminho_fisico = $diretorio_destino . $novo_nome_imagem;

        if(move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_fisico)){
            $caminho_imagem_banco = 'upload/produtos/' . $novo_nome_imagem;
            $atualizar_imagem = true;
        }
    } else{
        header("Location: ../index.php?erro=formato_imagem_invalido");
        exit;
    }
}

try{

    $imagem_antiga = null;
    if($atualizar_imagem){
        $stmt_busca = $pdo->prepare("SELECT imagem_produto FROM produtos WHERE Id_produto = :id_produto");
        $stmt_busca->bindParam(':id_produto', $id_produto, PDO::PARAM_INT);
        $stmt_busca->execute();
        $produto_antigo = $stmt_busca->fetch(PDO::FETCH_ASSOC);

        if($produto_antigo && !empty($produto_antigo['imagem_produto'])){
            $imagem_antiga = $produto_antigo['imagem_produto'];
        }
        
        $sql = "UPDATE produtos SET
        nome = :nome,
        Id_categoria = :Id_categoria,
        quantidade = :quantidade,
        estoque_minimo = :estoque_minimo,
        preco_custo = :preco_custo,
        porcentagem_lucro = :porcentagem_lucro,
        preco_venda = :preco_venda,
        imagem_produto = :imagem_produto
        WHERE Id_produto = :id_produto";

    }else{
        $sql = "UPDATE produtos SET 
        nome = :nome, 
        Id_categoria = :Id_categoria, 
        quantidade = :quantidade, 
        estoque_minimo = :estoque_minimo, 
        preco_custo = :preco_custo, 
        porcentagem_lucro = :porcentagem_lucro, 
        preco_venda = :preco_venda 
        WHERE Id_produto = :id_produto";
        }
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':id_produto', $id_produto, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':Id_categoria', $id_categoria);
        $stmt->bindParam(':quantidade', $quantidade, PDO::PARAM_INT);
        $stmt->bindParam(':estoque_minimo', $estoque_minimo, PDO::PARAM_INT);
        $stmt->bindParam(':preco_custo', $preco_custo);
        $stmt->bindParam(':porcentagem_lucro',$margem_lucro);
        $stmt->bindParam(':preco_venda',$preco_venda);

        if($atualizar_imagem){
            $stmt->bindParam(':imagem_produto', $caminho_imagem_banco);
        }

        $stmt->execute();

        if($atualizar_imagem && $imagem_antiga){
            $caminho_antigo_fisico = '../' . $imagem_antiga;
            if(file_exists($caminho_antigo_fisico)){
                unlink($caminho_antigo_fisico);
            }
        }

        header("Location: ../index.php?sucesso=editado");
        exit;

    } catch(PDOException $e){
    die("Erro ao atualizar o produto " . $e->getMessage());
        }
} else{
        header("Location: ../index.php");
        exit;
}