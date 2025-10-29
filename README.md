# Visão Geral do `produtos.php`

O arquivo `produtos.php` é a página principal da loja, responsável por exibir os produtos disponíveis, gerenciar o carrinho de compras, a autenticação do usuário e a interação com modais de produtos.

## Funcionalidades Principais:

1.  **Exibição de Produtos:**
    *   **Conexão e Busca no Banco de Dados:** O script PHP no `produtos.php` utiliza a função `getPDO()` do arquivo `php/db.php` para estabelecer uma conexão com o banco de dados. Uma consulta SQL (`SELECT * FROM produtos`) é executada para recuperar todos os produtos da tabela `produtos`. Os resultados são então processados e iterados usando um loop `foreach` para exibir cada produto dinamicamente na página.
    *   Cada produto é exibido com imagem, nome, preço e um botão "Adicionar ao Carrinho".
    *   O preço é formatado para o padrão monetário brasileiro.

2.  **Modais de Produtos:**
    *   Ao clicar na imagem de um produto, um modal detalhado é exibido.
    *   Este modal mostra informações adicionais do produto, como descrição, estoque, opções de tamanho e benefícios da compra.
    *   Permite adicionar o produto ao carrinho diretamente do modal.

3.  **Carrinho de Compras:**
    *   Um ícone de carrinho no cabeçalho exibe a quantidade de itens no carrinho (`#cart-count`).
    *   Ao clicar no ícone, um dropdown mostra os itens adicionados e o total da compra.
    *   Possui um botão "Finalizar Compra" que redireciona para a página `carrinho.php`.

4.  **Autenticação e Gerenciamento de Conta:**
    *   Botão "Entrar" no cabeçalho que exibe um dropdown com opções de "Gerenciar Conta" e "Entrar / Cadastrar".
    *   Se o usuário estiver logado, o botão exibe o primeiro nome do usuário e a opção "Sair".
    *   O gerenciamento de conta permite ao usuário visualizar e atualizar seus dados (nome, login, celular, email) através de um modal.
    *   A sessão do usuário é sincronizada com o `localStorage` para manter o estado de login.

5.  **Carrossel de Promoções:**
    *   Uma seção de carrossel exibe banners de promoções, como "Promoção 1 - CUPOM10" e "Promoção 2 - Frete grátis".
    *   A funcionalidade do carrossel é controlada pelo script `JavaScript/carrossel.js`.

6.  **Design Responsivo e Estilização:**
    *   Utiliza arquivos CSS (`Styles/produtos.css`, `Styles/carinho.css`, `Styles/carrossel.css`) para estilização.
    *   Inclui estilos CSS diretamente no arquivo para os modais de produtos e elementos de conta.
    *   O layout é responsivo, adaptando-se a diferentes tamanhos de tela.

## Tecnologias Utilizadas:

*   **PHP:** Para a lógica de backend, conexão com o banco de dados e renderização dinâmica dos produtos.
*   **HTML:** Estrutura da página.
*   **CSS:** Estilização e responsividade.
*   **JavaScript:** Interatividade da página, gerenciamento de modais, carrinho de compras, autenticação de usuário e carrossel.
*   **FontAwesome:** Ícones.
*   **PDO:** Para interação segura com o banco de dados.

## Scripts JavaScript Externos:

*   `JavaScript/script.js`: Script principal para funcionalidades gerais da página.
*   `JavaScript/carrossel.js`: Gerencia o carrossel de promoções.

## Fluxo de Dados (Autenticação):

*   `php/current_user.php`: Verifica o status de login do usuário no servidor.
*   `php/update_user.php`: Atualiza os dados do usuário no banco de dados.
*   `php/logout.php`: Encerra a sessão do usuário.

Este arquivo é o coração da experiência de compra, apresentando os produtos de forma interativa e gerenciando as principais interações do usuário com a loja.