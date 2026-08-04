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

* **Frontend**: HTML5, Bootstrap 5, CSS3, Javascript.
* **Backend**: PHP.
* **Banco de Dados:** MySQL (modelagem via MySQL Workbench) utilizando o servidor local XAMPP.
* **Segurança:** Conexão com o banco via PDO (PHP Data Objects) com uso estrito de Prepared Statements e Uso de hash para senha.

## 3. Requisitos Funcionais

* RF01: Fazer login.
* RF02: Gerenciar categorias.
* RF03: Gerenciar produtos.
* RF04: Pesquisar produtos.
* RF05: Exibir produtos em falta.
* RF06: Repor produtos.

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

## 6. Banco de Dados
O script SQL incluído neste repositório é responsável por inicializar o banco de dados relacional (gestao_estoque) que alimenta a aplicação. A modelagem foi desenhada para ser leve e garantir a integridade dos dados através de restrições de chaves estrangeiras.

O banco é composto por três tabelas principais:

* categorias: Armazena os grupos de classificação. Essencial para a organização do catálogo e para o funcionamento do filtro de pesquisas na interface.

* produtos: Tabela central do sistema. Registra as informações vitais de cada item, incluindo regras de precificação (custo, margem de lucro e preço de venda calculados), controle logístico (quantidade atual e estoque mínimo para o gatilho de alertas) e referências de mídia (caminho da imagem). Relaciona-se com categorias via Chave Estrangeira (ON UPDATE CASCADE).

* usuario: Tabela responsável pela autenticação e controle de acesso ao painel administrativo. O campo de senha (VARCHAR(255)) está dimensionado para receber armazenamento seguro via hash.

--> Nota para testes: O script contém um comando INSERT comentado que pode ser utilizado para gerar um usuário administrador padrão (gerente) no primeiro uso.

## 7. Estrutura da Interface

1. Visão Geral da Interface
   
O sistema "Gestão de Estoque" utiliza uma arquitetura de interface inspirada no modelo SPA (Single Page Application). A navegação tradicional entre múltiplas páginas (ex: lista.php, form.php) foi substituída por uma única interface central consolidada, onde o conteúdo é alternado dinamicamente via JavaScript. O design é responsivo e construído sobre o framework Bootstrap 5.

3. Estrutura da Página Central (index.php)
   
A página atua como o único ponto de entrada para as operações de estoque e divide-se nos seguintes componentes estruturais:

* Barra de Navegação Superior (Navbar):
    --> Interface limpa.
    --> Exibe o nome do sistema, a identificação do usuário logado e a ação de Logout.

* Controle de Abas (Pills Navigation):
    --> Em Falta (Ativa por padrão): Prioriza a ação imediata. Exibe uma tabela listando apenas produtos onde quantidade <= estoque_minimo. O botão "Repor" aciona diretamente o modal de edição.
    --> Ver Produtos: Exibe o inventário completo em formato de Grid de 4 colunas. Inclui um sistema de busca textual cruzada com filtro de categorias operado 100% no cliente (via JavaScript).
    --> Ver Categorias: Painel dedicado à visualização e gestão das categorias do sistema.

3. Navegação de Ações (Sistema de Modais)

A navegação vertical (troca de telas para formulários) foi inteiramente substituída por Modais sobrepostos.

* Modais de Criação: Acessados por botões globais ("Cadastrar Produto", "Nova Categoria").
* Modais de Edição: Alimentados dinamicamente via atributos HTML (data-id, data-nome, etc.) injetados no momento em que o usuário clica no botão "Editar" ou "Repor".
* Modais de Exclusão: Exigem confirmação visual (exibindo o nome do item) antes de permitir a exclusão.

4. Feedback Visual (Notificações)
   
As mensagens de sucesso e erro do servidor (ex: exclusão bloqueada, produto salvo) não recarregam uma tela em branco. O sistema utiliza um componente centralizado de Toasts Dinâmicos (includes/mensagens.php), que lê os parâmetros da URL ($_GET['sucesso'] ou $_GET['erro']) e exibe alertas flutuantes temporários no canto superior da tela, sobrepondo-se à interface sem interromper a navegação.








