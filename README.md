# Projeto Gestão Estoque Básico
Projeto de sistema web para gerenciar um estoque de produtos com php e banco de dados MySQL.

## 1. Visão Geral do projeto

### Problema

Dificuldade na gestão manual do armazém, falta de controle sobre produtos estocados, dificuldade na atualização de preços e ausência de previsibilidade sobre o que precisa ser reposto.

### Público-Alvo/Usuários

Gerente do armazém (usuário único).

### Contexto

Pequeno armazém comercial focado na venda de bebidas, doces, salgadinhos e itens de conveniência.

## 2. Tecnologias

* **Frontend**: HTML5, CSS3, JavaScript.
* **Backend**: PHP.
* **Banco de Dados:** MySQL (modelagem via MySQL Workbench) utilizando o servidor local XAMPP.
* **Segurança:** Conexão com o banco via PDO (PHP Data Objects) com uso estrito de Prepared Statements e Uso de hash para senha.

## 3. Requisitos Funcionais

* RF01: Fazer login.
* RF02: Gerenciar categorias.
* RF03: Gerenciar produtos.
* RF04: Pesquisar produtos.
* RF05: Exibir produtos em falta.
* RF06: Diminuir produtos.
* RF07: Repor produtos.

## 4. Requisitos Não Funcionais

* RNF01 - Armazenamento de Imagens: As imagens dos produtos não devem ser salvas no banco de dados. O sistema fará o upload do arquivo para uma pasta específica no servidor (uploads), salvando apenas o caminho do arquivo na tabela de produtos.
  
* RNF02 - Segurança de Banco de Dados: Todas as interações com o banco de dados devem ser protegidas contra ataques de Injeção SQL (SQL Injection). 

* RNF03 - Autenticação: A senha do usuário gerente deve ser armazenada de forma segura utilizando hash.

## 5. Regras de negócio

--> RN01 - Exclusividade de Categoria: Um produto não pode pertencer a mais de uma categoria simultaneamente (Ex: Bebida Alcoólica ≠ Refrigerante).

--> RN02 - Precificação e Lucro: No cadastro, o gerente insere o preço de custo e a porcentagem de lucro desejada. O sistema deve calcular o preço de venda automaticamente e exibir ele diretamente no formulário de cadastro de produto. O banco de dados armazenará os três valores: preco_custo, porcentagem_lucro e preco_venda.

--> RN03 - Validação do Preço de Venda: Para garantir a segurança dos valores armazenados no banco de dados relacionados ao preço dos produtos, o cálculo do preço de venda deve ser realizado individualmente em cada parte que necessita armazenar os valores de preco_custo, porcentagem_lucro e preco_venda.

--> RN04 - Remoção Automática de Produto Estocado: Ao realizar a reposição de um produto (aumentando sua quantidade para um valor acima do estoque mínimo), ele deve desaparecer automaticamente da lista de produtos em falta.

--> RN05 - Perfis de Usuário: O sistema não possui distinção de perfis (níveis de acesso), já que o acesso é exclusivo do gerente.

## 6. Estrutura da Interface

A interface será construída no modelo SPA (Single Page Application). Para alterar  








