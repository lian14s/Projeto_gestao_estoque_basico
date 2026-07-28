<?php
$host = '127.0.0.1';
$dbname = 'gestao_estoque';
$usuario = 'root'; 
$senha = '';

try {
    // Instanciando o objeto PDO e criando a conexão
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $usuario, $senha);

    // Lança exceções (PDOException) caso ocorra algum erro no SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retorna os dados do banco como Arrays Associativos (ex: $linha['nome_produto'])
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

}
 catch (PDOException $e) {
    // Captura a exceção caso o banco esteja fora do ar ou a senha incorreta
    // O 'die' interrompe o carregamento do resto da página para evitar outros erros
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}



