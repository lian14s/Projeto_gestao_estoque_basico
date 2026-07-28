<?php 
require_once '../includes/validacao.php';
require_once '../config/conexao.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    // Correção no trim() e no cast de inteiros
    $nome = trim($_POST['nome'] ?? '');
    $id_categoria = (int) ($_POST['Id_categoria'] ?? 0);
    $quantidade = (int) ($_POST['quantidade'] ?? 0);
    $estoque_minimo = (int) ($_POST['estoque_minimo'] ?? 0);

    $preco_custo = (float) str_replace(',', '.', $_POST['preco_custo'] ?? 0);
    $margem_lucro = (float) str_replace(',', '.', $_POST['margem_lucro'] ?? 0);

    if(empty($nome) || empty($id_categoria)){
        header("Location: ../index.php?erro=campos_obrigatorios");
        exit;
    }

    $valor_lucro = $preco_custo * ($margem_lucro/100);
    $preco_venda = $preco_custo + $valor_lucro;

    $caminho_imagem_banco = null;
    $caminho_fisico_criado = null; // Variável de controle para o catch

    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK){

        $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $extensoes_permitidas = ['jpg', 'jpeg', 'png','webp'];

        if(in_array($extensao, $extensoes_permitidas)){
            $novo_nome_imagem = uniqid() . '.'. $extensao;
            $diretorio_destino = '../upload/produtos/';

            if(!is_dir($diretorio_destino)){
                mkdir($diretorio_destino, 0777, true);
            }
            $caminho_fisico = $diretorio_destino . $novo_nome_imagem;

            if(move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_fisico)){
                $caminho_imagem_banco = 'upload/produtos/' . $novo_nome_imagem;
                $caminho_fisico_criado = $caminho_fisico; // Guarda o caminho para caso precise deletar
            }
        } else {
            header("Location: ../index.php?erro=cad_formato_invalido");
            exit;
        }
    }

    try {
        $sql = "INSERT INTO produtos
                (nome, Id_categoria, quantidade, estoque_minimo, preco_custo, porcentagem_lucro, preco_venda, imagem_produto)
                VALUES (:nome, :Id_categoria, :quantidade, :estoque_minimo, :preco_custo, :porcentagem_lucro, :preco_venda, :imagem_produto)";
                
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':Id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmt->bindParam(':quantidade', $quantidade, PDO::PARAM_INT);
        $stmt->bindParam(':estoque_minimo', $estoque_minimo, PDO::PARAM_INT);
        $stmt->bindParam(':preco_custo', $preco_custo);
        $stmt->bindParam(':porcentagem_lucro', $margem_lucro);
        $stmt->bindParam(':preco_venda', $preco_venda);
        $stmt->bindParam(':imagem_produto', $caminho_imagem_banco);

        $stmt->execute();

        header("Location: ../index.php?sucesso=produto");
        exit;
        
    } catch (PDOException $e) {
        if ($caminho_fisico_criado && file_exists($caminho_fisico_criado)) {
            unlink($caminho_fisico_criado);
        }
        
        die("Erro ao cadastrar produto: " . $e->getMessage());
    }

} else {
    header("Location: ../index.php");
    exit;
}