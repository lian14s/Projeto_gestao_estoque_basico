<?php
require_once 'includes/validacao.php';

require_once 'config/conexao.php';

try {
    $sql_alerta = "SELECT p.*, c.nome as categoria_nome 
                   FROM produtos p 
                   INNER JOIN categorias c ON p.Id_categoria = c.Id_categoria 
                   WHERE p.quantidade <= p.estoque_minimo 
                   ORDER BY p.quantidade ASC";
            
    $stmt_alerta = $pdo->prepare($sql_alerta);
    $stmt_alerta->execute();
    $produtos_alerta = $stmt_alerta->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar os alertas: " . $e->getMessage());
}

try{
    $stmtCategorias = $pdo->prepare("SELECT Id_categoria, nome FROM categorias ORDER BY nome ASC");
    $stmtCategorias ->execute();
    $listaCategorias = $stmtCategorias->fetchAll();


} catch(PDOException $e){
    die("Erro ao recuperar as categorias " . $e->getMessage());
}

try{
    $sqlProdutos = "SELECT p.*, c.nome AS nome_categoria
    FROM produtos p
    INNER JOIN categorias c ON p.Id_categoria = c.Id_categoria
    ORDER BY p.nome ASC";

    $stmtProdutos = $pdo->prepare($sqlProdutos);
    $stmtProdutos ->execute();
    $listaProdutos = $stmtProdutos->fetchAll();
    

} catch(PDOException $e){
    die("Erro ao recuperar produtos " . $e->getMessage());
}
 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style> 
        .form-control:focus{
            box-shadow:none ; 
            border-color: #ced4da;
        }

    </style>
</head>
<body class="bg-light">

<!-- Menu Superior -->
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-box-fill me-2"></i>Gestão de Estoque
        </a>
        
        <div class="d-flex align-items-center">
            <span class="navbar-text text-white me-3 d-none d-sm-inline">
                Usuário: <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>
            </span>
            <a href="actions/autenticar-logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Sair
            </a>
        </div>
    </div>
</nav>
    
<!-- Conteúdo Principal -->

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-12">
            <h2 class="fw-bold">Bem-vindo!</h2>
            <p class="text-muted">Alterne entre as abas para gerenciar seus produtos e categorias.</p>
        </div>
    </div>

<!-- Botões de alternância -->
 <ul class="nav nav-pills mb-4" id="pills-tab">
    <li class="nav-item">
            <button class="nav-link active bg-danger text-white border-0" id="tab-falta" onclick="trocarAba('falta')" type="button">
                <i class="bi bi-exclamation-triangle-fill"></i> Em Falta
            </button>
        </li>
    <li class="nav-item ms-2">
        <button class="nav-link" id="tab-produtos" onclick="trocarAba('produtos')" type="button" role="tab">
            <i class="bi bi-box-seam"></i> Ver Produtos
        </button>
    </li>
    <li class="nav-item ms-2">
        <button class="nav-link" id="tab-categorias" onclick="trocarAba('categorias')" type="button" role="tab">
            <i class="bi bi-tags"></i> Ver Categorias
        </button>
    </li>
</ul>

<!-- Painel 1: Produtos em Falta -->
<div id="painel-falta" class="d-block">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Estoque em Falta</h4>
            <p class="text-muted">Produtos que atingiram o limite mínimo e precisam de reposição.</p>
        </div>
    </div>

    <div class="card shadow-sm border-danger border-opacity-25">
        <div class="card-body p-0">
            <?php if (count($produtos_alerta) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-danger">
                            <tr>
                                <th class="ps-4">Produto</th>
                                <th>Categoria</th>
                                <th class="text-center">Quantidade Atual</th>
                                <th class="text-center">Estoque Mínimo</th>
                                <th class="text-center pe-4">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtos_alerta as $produto): ?>
                                <tr>
                                    <td class="ps-4 fw-semibold"><?php echo htmlspecialchars($produto['nome']); ?></td>
                                    <td><span class="badge text-bg-secondary"><?php echo htmlspecialchars($produto['categoria_nome']); ?></span></td>
                                    
                                    <td class="text-center fw-bold <?php echo ($produto['quantidade'] == 0) ? 'text-danger' : 'text-warning'; ?>">
                                        <?php echo $produto['quantidade']; ?>
                                    </td>
                                    
                                    <td class="text-center text-muted"><?php echo $produto['estoque_minimo']; ?></td>
                                    
                                    <td class="text-center pe-4">
                                        <!-- O Botão Repor agora abre o Modal de Edição que já construímos! -->
                                        <button class="btn btn-success btn-sm" title="Repor Estoque"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarProduto"
                                            data-id="<?php echo $produto['Id_produto']; ?>"
                                            data-nome="<?php echo htmlspecialchars($produto['nome']); ?>"
                                            data-categoria="<?php echo $produto['Id_categoria']; ?>"
                                            data-qtd="<?php echo $produto['quantidade']; ?>"
                                            data-min="<?php echo $produto['estoque_minimo']; ?>"
                                            data-custo="<?php echo $produto['preco_custo']; ?>"
                                            data-margem="<?php echo $produto['porcentagem_lucro']; ?>"
                                            data-venda="<?php echo $produto['preco_venda']; ?>">
                                            <i class="bi bi-box-arrow-in-down me-1"></i>Repor
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center p-5">
                    <i class="bi bi-check-circle-fill text-success fs-1 mb-3 d-block"></i>
                    <h4 class="text-success mb-2">Estoque Saudável!</h4>
                    <p class="text-muted mb-0">Nenhum produto está abaixo do limite mínimo no momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Categorias -->
<div class="d-none" id="painel-categorias" role="tabpanel" tabindex="0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Categorias Cadastradas</h5>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCategoria">
                <i class="bi bi-plus-circle"></i> Cadastrar Categorias
            </button>
    </div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Nome da categoria</th>
                    <th class="text-end pe-4">Ações</th>
                </tr>
            </thead>
            <tbody>
    <?php if (count($listaCategorias) > 0): ?>

        <?php foreach ($listaCategorias as $categoria): ?>
            <tr>
                <!-- Exibe o Nome -->
                <td class="ps-4">
                    <?php echo htmlspecialchars($categoria['nome']); ?>
                </td>
                
                <!-- Botões de Ação -->
                <td class="text-end pe-4">
                    <button class="btn btn-sm btn-outline-primary me-1" title="Editar" data-id="<?php echo htmlspecialchars($categoria['Id_categoria']); ?>" data-nome="<?php echo htmlspecialchars($categoria['nome']);?>" data-bs-toggle="modal" data-bs-target="#modalEditarCategoria">
                        <i class="bi bi-pencil"></i>
                    </button>
                     <button class="btn btn-sm btn-outline-danger me-1" title="Excluir" data-id="<?php echo htmlspecialchars($categoria['Id_categoria']); ?>" data-nome="<?php echo htmlspecialchars($categoria['nome']);?>" data-bs-toggle="modal" data-bs-target="#modalExcluirCategoria">
                        <i class="bi bi-trash"></i>
                    </button>
                
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="2" class="text-center py-4 text-muted">
                Nenhuma categoria cadastrada ainda. Clique no botão acima para adicionar.
            </td>
        </tr>
    <?php endif; ?>
</tbody>
        </table>
    </div>
       
</div>

</div>

<!-- Produtos  -->
<div class="d-none" id="painel-produtos" role="tabpanel" tabindex="0">
    <!-- Lista Produtos -->
        <div id="tela-lista-produtos" class="d-block"> 
           <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex w-50 gap-2">
                        <input type="text" class="form-control" id="buscaProduto" placeholder="Digite o nome do produto para buscar...">
                        <select class="form-select" id="filtroCategoria" style="min-width: 180px; width: auto;">
                            <option value="todas">Todas as categorias</option>
                            <?php foreach($listaCategorias as $categoria):?>
                                    <option value="<?php echo htmlspecialchars(strtolower($categoria['nome']));?>">
                                        <?php echo htmlspecialchars($categoria['nome']);?>
                                    </option>
                            <?php endforeach;?>
                        </select>       
                </div>
            <button class="btn btn-success" onclick="alternarTelaProduto('form')"> 
                <i class="bi bi-plus-circle"></i> Cadastrar Produtos
            </button> 
        </div>
            <div class="row g-4 mb-3" id="gridProdutos">
                <?php if(count($listaProdutos) > 0): ?>
                    <?php foreach($listaProdutos as $produto): ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3"> 
                            <div class="card h-100 shadow-sm border-0"> 
                                <?php if(!empty($produto['imagem_produto'])):?>
                                <img src="<?php echo htmlspecialchars($produto['imagem_produto']); ?>" class="card-img-top p-3" alt="Imagem de <?php echo htmlspecialchars($produto['nome']); ?>" style="height: 200px; object-fit: contain;">
                                <?php else: ?>
                                    <div class="bg-light d-flex justify-content-center align-items-center" style="height:200px;"> 
                                        <i class="bi bi-image text-secondary" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <span class="badge bg-secondary text-white mb-2 align-self-start"> 
                                        <?php echo htmlspecialchars($produto['nome_categoria']); ?>    
                                    </span>
                                    <h6 class="card-title fw-bold text-truncate" title="<?php echo htmlspecialchars($produto['nome']);?> "> 
                                        <?php echo htmlspecialchars($produto['nome']);?>
                                    </h6>
                                    <h5 class="text-success fw-bold mb-3"> 
                                        R$ <?php echo number_format($produto['preco_venda'], 2, ',','.');?>
                                    </h5>
                                    <div class="mb-4 small"> 
                                    <?php if($produto['quantidade'] <= $produto['estoque_minimo']):?>
                                    <span class="text-danger fw-bold"> 
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Estoque baixo: <?php echo $produto['quantidade'];?> un.
                                    </span>
                                    <?php else:?>
                                    <span class="text-muted"> Estoque atual: <?php echo $produto['quantidade'];?> un.</span>
                                    <?php endif;?>
                                    </div>
                                    <div class="mt-auto d-flex justify-content-between border-top pt-3"> 
                                        <button class="btn btn-sm btn-outline-primary w-100 me-3" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditarProduto"
                                        data-id="<?php echo $produto['Id_produto'];?>"
                                        data-nome="<?php echo htmlspecialchars($produto['nome']);?>"
                                        data-categoria="<?php echo $produto['Id_categoria'];?>"
                                        data-qtd="<?php echo $produto['quantidade'];?>"
                                        data-min="<?php echo $produto['estoque_minimo'];?>"
                                        data-custo="<?php echo $produto['preco_custo'];?>"
                                        data-margem="<?php echo $produto['porcentagem_lucro'];?>"
                                        data-venda="<?php echo $produto['preco_venda'];?>">
                                            <i class="bi bi-pencil"></i> Editar 
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Excluir"
                                        data-bs-toggle="modal" data-bs-target="#modalExcluirProduto"
                                        data-id="<?php echo $produto['Id_produto'];?>"
                                        data-nome="<?php echo htmlspecialchars($produto['nome']);?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <?php endforeach;?>
                            <div id="mensagemBuscaVazia" class="col-12 text-center py-5 d-none">
                                <i class="bi bi-search text-muted" style="font-size:3rem;"></i>
                                <h5 class="text-muted mt-3">Nenhum produto encontrado.</h5>
                                <p class="text-muted">Tente buscar por outros termos ou mudar a categoria.</p>                
                            </div>
                    <?php else: ?>
                        <div class="col-12 text-center py-5"> 
                            <i class="bi bi-box-seam text-muted" style="font-size:4rem;"></i>
                            <h4 class="text-muted mt-3">Nenhum produto encontrado.</h4>
                            <p class="text-muted">Cadastre o seu primeiro produto clicando no botão acima.</p>
                        </div>
                    <?php endif;?>
            </div>
            
        </div>
        <div id="tela-form-produtos" class="d-none">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Novo Produto</h5>
                <button class="btn btn-outline-secondary" onclick="alternarTelaProduto('lista')">
                    <i class="bi bi-arrow-left"></i> Voltar para lista
                </button>
            </div>
                <div class="card shadow-sm boder-sucess mb-4">
                    <div class="card-body">
                        <form action="actions\cad-produto.php" method="POST" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Nome do Produto<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nomeProduto" name="nome" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Categoria<span class="text-danger">*</span></label>
                                    <select class="form-select" id="categoriaProduto" name="Id_categoria" required>
                                        <option value=""> Selecione uma categoria...</option>
                                        <?php foreach ($listaCategorias as $categoria): ?>
                                            <option value="<?php echo $categoria['Id_categoria'];?>">
                                                <?php echo htmlspecialchars($categoria['nome']);?>
                                            </option>
                                            <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Quantidade Inicial <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="qtdProduto" name="quantidade" min="0" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Estoque Mínimo <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="minProduto" name="estoque_minimo" min="0" required>                                
                                </div>
                                <div class="col-md-4">
                                    <label for="precoCusto" class="form-label">Preço de Custo (R$) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="precoCusto" name="preco_custo" step="0.01" min="0" required oninput="calcularPrecoVenda()">
                                </div>
                                <div class="col-md-4">
                                    <label for="margemLucro" class="form-label">Margem de Lucro (%) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="margemLucro" name="margem_lucro" step="0.01" min="0" required oninput="calcularPrecoVenda()">
                                </div>
                                <div class="col-md-4">
                                    <label for="precoVenda" class="form-label">Preço de Venda Final (R$)</label>
                                    <input type="number" class="form-control bg-light" id="precoVenda" name="preco_venda" readonly>
                                <div class="form-text">Calculado automaticamente.</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label"> Imagem do Produto (opcional)</label>
                                    <input class="form-control" type="file" id="imagemProduto" name="imagem" accept="image/png, image/jpeg, image/webp">
                                    <div class="form-text">Para o cadastro de produtos, recomendamos imagens no formato quadrado.</div>
                                </div>
                                <div class="col-12 text-end mt-4">
                                    <button type="button" class="btn btn-danger me-2" onclick="alternarTelaProduto('lista')">Cancelar </button>
                                    <button type="submit" class="btn btn-success"><i class="bi bi-save me-2"></i>Salvar Produto</button>   
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
        </div>

</div>

</div>
<!-- Fim do conteúdo principal -->

<!--Modal de cadastro de categoria -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="actions/cad-categoria.php" method="POST">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Cadastrar Categoria</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome da Categoria</label>
                        <input type="text" class="form-control" id="nomeCategoria" name="nome" required>
                    </div>
                    <div class="modal-footer">
                        <button type="cancel" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Categoria</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de edição de categoria -->
<div class="modal fade" id="modalEditarCategoria" tabindex="-1" aria-labelledby="modalEditarCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog"> <!-- Removido o modal-lg -->
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edição de Categoria</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            
            <form action="actions/editar-categoria.php" method="POST" id="formEditarCategoria">
                <div class="modal-body">
                    <input type="hidden" name="id_categoria" id="editIdCategoria">
                    
                    <div class="mb-2">
                        <label for="editNomeCategoria" class="form-label">Nome da Categoria <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editNomeCategoria" name="nome" required>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de edição de produto -->
<div class="modal fade" id="modalEditarProduto" tabindex="-1" aria-labelledby="modalEditarProdutoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i>Edição de Produto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form action="actions/editar-produto.php" method="POST" enctype="multipart/form-data" id="formEditarProduto">
                     <input type="hidden" name="id_produto" id="editIdProduto">
                     <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nome do Produto<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editNomeProduto" name="nome" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoria<span class="text-danger">*</span></label>
                            <select class="form-select" id="editCategoriaProduto" name="Id_categoria" required>
                                <option value="">Selecione uma categoria...</option>4
                                <?php foreach($listaCategorias as $categoria):?>
                                    <option value="<?php echo $categoria['Id_categoria'];?>">
                                        <?php echo htmlspecialchars($categoria['nome']);?>
                                    </option>
                                    <?php endforeach;?>         
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantidade Atual<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="editQtdProduto" name="quantidade" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estoque Mínimo<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="editMinProduto" name="estoque_minimo" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Preço de Custo (R$)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="editPrecoCusto" name="preco_custo" step="0.01" min="0" required oninput="calcularPrecoVendaEdit()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Margem de Lucro (%)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="editMargemLucro" name="margem" step="0.01" min="0" required oninput="calcularPrecoVendaEdit()">
                        </div>
                         <div class="col-md-4">
                            <label class="form-label">Preço de Venda Final (R$)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control bg-light" id="editPrecoVenda" name="preco_venda" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Atualizar Imagem (opcional)</label>
                            <input class="form-control" type="file" id="editImagemProduto" name="imagem" accept="image/png, image/jpeg, image/webp">
                            <div class="form-text text-muted">Deixe em branco se quiser manter a imagem atual.</div>
                        </div>
                        <div class="col-12 text-end mt-4 border-top pt-3">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Salvar Alterações</button>
                        </div>
                     </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Excluir Produto -->
<div class="modal fade" id="modalExcluirProduto" tabindex="-1" aria-labelledby="modalExcluirProdutoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalExcluirProdutoLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body text-center pt-4 pb-4">
                <p class="mb-1">Você está prestes a excluir o produto:</p>
                <h4 class="fw-bold text-danger" id="nomeProdutoExclusao">Nome do produto</h4>
                <p class="text-muted small mt-3 mb-0">Está ação não poderá ser desfeita.</p>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <form action="actions/excluir-produto.php" method="POST">
                    <input type="hidden" name="id_produto" id="excluirIdProduto">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-2"></i>Sim, Excluir Produto</button>                    
                </form>
            </div>
        </div>                      
    </div>
</div>

<!-- Modal Excluir Categoria-->
<div class="modal fade" id="modalExcluirCategoria" tabindex="-1" aria-labelledby="modalExcluirCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalExcluirCategoriaLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body text-center pt-4 pb-4">
                <p class="mb-1">Você está prestes a excluir a categoria:</p>
                <h4 class="fw-bold text-danger" id="nomeCategoriaExclusao">Nome da categoria</h4>
                <p class="text-muted small mt-3 mb-0">Está ação não poderá ser desfeita.</p>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <form action="actions/excluir-categoria.php" method="POST">
                    <input type="hidden" name="id_categoria" id="excluirIdCategoria">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-2"></i>Sim, Excluir Categoria</button>                    
                </form>
            </div>
        </div>                      
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/javascript/script.js"></script>

<?php include_once 'includes/mensagens.php'; ?>

</body>
</html>