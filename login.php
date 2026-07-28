<?php 
$login_erro = isset($_GET['erro']) && $_GET['erro'] == 1; 
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Estoque - Login</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class ="bg-light">

    <!-- Formulário de Login -->
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="card shadow-sm" style="width: 100%; max-width: 400px;">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h2 class="card-title h4">Gestão de Estoque</h2>
                    <p class="text-muted"> Acesso Restrito</p>
                    <form action="actions/autenticar.php" method="POST">
                        <div class="row mb-3">
                            <label for="usuario" class="col-sm-3 col-form-label text-start">Usuário</label>
                            <input type="text" class="form-control" name="usuario" id="usuario" required autofocus>
                        </div>
                        <div class="row mb-3">
                            <label for="senha" class="col-sm-3 col-form-label text-start">Senha</label>
                            <input type="password" class="form-control" name="senha" id="senha" required>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
                        </div> 
                    </form>
                </div>
            </div> 
        </div> 
    </div>

<!-- Mensagem de login inválido -->
<div class="toast-container position-fixed top-0 end-0 p-3"> 
    <div id="toastErro" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic = "true">
        <div class="d-flex">
            <div class="toast-body">
                <strong>Acesso negado!</strong> Usuário ou senha incorretos.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

    <!-- Bootstrap 5.3 Javascript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

<?php if ($login_erro): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let toastElement = document.getElementById('toastErro');
        let toast = new bootstrap.Toast(toastElement, { delay: 5000}); 
        toast.show();
        window.history.replaceState(null, null, window.location.pathname);
    });

</script>
<?php endif; ?>

</html>