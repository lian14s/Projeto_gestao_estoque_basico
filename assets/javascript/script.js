//Funções

function trocarAba(abaSelecionada){
    // Lista com os nomes de todas as abas que existem
    const abas = ['falta', 'produtos', 'categorias'];

    abas.forEach(aba => {
        const btn = document.getElementById(`tab-${aba}`);
        const painel = document.getElementById(`painel-${aba}`);

        if(btn && painel) {
            if(aba === abaSelecionada) {
                btn.classList.add('active');
                
                if(aba === 'falta') {
                    btn.classList.add('bg-danger', 'text-white');
                }
                
                painel.classList.replace('d-none', 'd-block');
            } else {
                btn.classList.remove('active');
                
                if(aba === 'falta') {
                    btn.classList.remove('bg-danger', 'text-white');
                }

                painel.classList.replace('d-block', 'd-none');
            }
        }
    });
}

function alternarTelaProduto(tela){
    const telaLista = document.getElementById('tela-lista-produtos');
    const telaForm = document.getElementById('tela-form-produtos');

    if(tela === 'form'){
        telaLista.classList.replace('d-block', 'd-none');
        telaForm.classList.replace('d-none', 'd-block');
    } else if (tela === 'lista'){
        telaForm.classList.replace('d-block','d-none');
        telaLista.classList.replace('d-none', 'd-block');
    }
}

function calcularPrecoVenda(){
    const custo = parseFloat(document.getElementById('precoCusto').value) || 0;
    const margem = parseFloat(document.getElementById('margemLucro').value) || 0;

    const valorLucro = custo * (margem/100);
    const precoVenda = custo + valorLucro;

    document.getElementById('precoVenda').value = precoVenda.toFixed(2);
}

function calcularPrecoVendaEdit(){
    const custo = parseFloat(document.getElementById('editPrecoCusto').value) || 0;
    const margem = parseFloat(document.getElementById('editMargemLucro').value) || 0;

    const lucro = custo * (margem/100);
    const precoFinal = custo + lucro;

    document.getElementById('editPrecoVenda').value = precoFinal.toFixed(2);
}

//Eventos

document.addEventListener('DOMContentLoaded', function(){
    
    // 1. Sistema de Busca e Filtro de Produtos
    const inputBusca = document.getElementById('buscaProduto');
    const filtroCategoria = document.getElementById('filtroCategoria');

    function filtrarProdutos() {
        const termoBusca = inputBusca ? inputBusca.value.toLowerCase() : '';
        const categoriaSelecionada = filtroCategoria ? filtroCategoria.value : 'todas';
        
        const colunasProdutos = document.querySelectorAll('#gridProdutos .col-12:not(#mensagemBuscaVazia)');
        let produtosVisiveis = 0

        colunasProdutos.forEach(function(coluna) {
            const tituloElement = coluna.querySelector('.card-title');
            const categoriaElement = coluna.querySelector('.badge'); 

            if (tituloElement && categoriaElement) {
                const tituloProduto = tituloElement.textContent.toLowerCase();
                const categoriaProduto = categoriaElement.textContent.toLowerCase().trim();

                const correspondeNome = tituloProduto.includes(termoBusca);
                
                const correspondeCategoria = (categoriaSelecionada === 'todas' || categoriaProduto === categoriaSelecionada);

                if (correspondeNome && correspondeCategoria) {
                    coluna.classList.remove('d-none');
                    produtosVisiveis++;
                } else {
                    coluna.classList.add('d-none');
                }
            }
        });
        const divMensagemVazia = document.getElementById('mensagemBuscaVazia');
        if(divMensagemVazia){
            if(produtosVisiveis === 0){
                divMensagemVazia.classList.remove('d-none');
            } else{
                divMensagemVazia.classList.add('d-none');
            }
        }
    }

    if (inputBusca) {
        inputBusca.addEventListener('input', filtrarProdutos);
    }
    if (filtroCategoria) {
        filtroCategoria.addEventListener('change', filtrarProdutos);
    }

    // 2. Modal Excluir Produto
    const modalExcluir = document.getElementById('modalExcluirProduto');
    if (modalExcluir) {
        modalExcluir.addEventListener('show.bs.modal', function (event) {
            const botao = event.relatedTarget;
            document.getElementById('excluirIdProduto').value = botao.getAttribute('data-id');
            document.getElementById('nomeProdutoExclusao').textContent = botao.getAttribute('data-nome');
        });
    }

    // 3. Modal Excluir Categoria
    const modalExcluirCategoria = document.getElementById('modalExcluirCategoria');
    if(modalExcluirCategoria){ 
        modalExcluirCategoria.addEventListener('show.bs.modal', function (event){
            const botao = event.relatedTarget;
            document.getElementById('excluirIdCategoria').value = botao.getAttribute('data-id');
            document.getElementById('nomeCategoriaExclusao').textContent = botao.getAttribute('data-nome');
        });
    }

    // 4. Modal Editar Produto
    const modalEditar = document.getElementById('modalEditarProduto');
    if(modalEditar){
        modalEditar.addEventListener('show.bs.modal', function (event){
            const botao = event.relatedTarget;
            document.getElementById('editIdProduto').value = botao.getAttribute('data-id');
            document.getElementById('editNomeProduto').value = botao.getAttribute('data-nome');
            document.getElementById('editCategoriaProduto').value = botao.getAttribute('data-categoria');
            document.getElementById('editQtdProduto').value = botao.getAttribute('data-qtd');
            document.getElementById('editMinProduto').value = botao.getAttribute('data-min');
            document.getElementById('editPrecoCusto').value = botao.getAttribute('data-custo');
            document.getElementById('editMargemLucro').value = botao.getAttribute('data-margem');
            document.getElementById('editPrecoVenda').value = botao.getAttribute('data-venda');
        });
    }

    // 5. Modal Editar Categoria
    const modalEditarCategoria = document.getElementById('modalEditarCategoria');
    if(modalEditarCategoria){
        modalEditarCategoria.addEventListener('show.bs.modal', function (event){
            const botao = event.relatedTarget;
            document.getElementById('editIdCategoria').value = botao.getAttribute('data-id');
            document.getElementById('editNomeCategoria').value = botao.getAttribute('data-nome');
        });
    }
});