<?php

$toast = null;

if (isset($_GET['sucesso'])) {
    $toast = ['cor' => 'text-bg-success', 'titulo' => 'Sucesso!'];
    
    switch ($_GET['sucesso']) {
        case 'categoria': 
        case 'editado': 
            $toast['msg'] = 'Operação realizada com sucesso!'; 
            break;
        case 'categoria_excluida': 
        case 'excluido':
            $toast['msg'] = 'Item excluído com sucesso!'; 
            break;
        case 'produto': 
            $toast['msg'] = 'Produto cadastrado com sucesso!'; 
            break;
    }
} elseif (isset($_GET['erro'])) {
    $toast = ['cor' => 'text-bg-danger', 'titulo' => 'Erro!'];
    
    switch ($_GET['erro']) {
        case 'categoria_em_uso':
            $toast['titulo'] = 'Ação bloqueada!';
            $toast['msg'] = 'Você não pode excluir esta categoria porque ainda existem produtos vinculados a ela.';
            break;
        case 'formato_imagem_invalido':
        case 'cad_formato_invalido':
            $toast['titulo'] = 'Formato inválido!';
            $toast['msg'] = 'São aceitos apenas os formatos: jpg, jpeg, png e webp.';
            break;
        case 'campos_obrigatorios':
        case 'campos_obrigatorios_edicao':
            $toast['titulo'] = 'Atenção!';
            $toast['msg'] = 'Por favor, preencha todos os campos obrigatórios.';
            break;
    }
}
?>

<?php if ($toast): ?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;"> 
    <div id="toastDinamico" class="toast align-items-center <?php echo $toast['cor']; ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <strong><?php echo $toast['titulo']; ?></strong>
                <?php if (isset($toast['msg'])): ?>
                    <p class="mb-0 mt-1"><?php echo $toast['msg']; ?></p>
                <?php endif; ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let toastElement = document.getElementById('toastDinamico');
        if (toastElement) {
            let toast = new bootstrap.Toast(toastElement, { delay: 4000 }); 
            toast.show();
            window.history.replaceState(null, null, window.location.pathname);
        }
    });
</script>
<?php endif; ?>

