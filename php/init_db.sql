-- Active: 1760019689552@@127.0.0.1@3306@bangustore
-- Cria tabela usuarios
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome_completo VARCHAR(255) NOT NULL,
  data_nascimento DATE,
  email VARCHAR(255) NOT NULL UNIQUE,
  sexo VARCHAR(20),
  cpf VARCHAR(14) NOT NULL UNIQUE,
  celular VARCHAR(15),
  login VARCHAR(100) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Tabela `categorias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS categorias (
  id_categoria INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  PRIMARY KEY (id_categoria)
);

-- -----------------------------------------------------
-- Tabela `produtos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS produtos (
  id_produto INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(255) NOT NULL,
  descricao TEXT NULL,
  preco DECIMAL(10, 2) NOT NULL,
  imagemURL VARCHAR(2083) NULL,
  estoque INT NOT NULL DEFAULT 0,
  categorias_id_categoria INT NOT NULL,
  PRIMARY KEY (id_produto),
  INDEX fk_produtos_categorias_idx (categorias_id_categoria ASC),
  CONSTRAINT fk_produtos_categorias
    FOREIGN KEY (categorias_id_categoria)
    REFERENCES categorias (id_categoria)
    ON DELETE RESTRICT -- Impede deletar uma categoria se houver produtos nela
    ON UPDATE CASCADE
);

-- -----------------------------------------------------
-- Tabela `enderecos`
-- (Colunas inferidas baseadas em uma estrutura comum)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS enderecos (
  id_endereco INT NOT NULL AUTO_INCREMENT,
  cep VARCHAR(9) NOT NULL,
  rua VARCHAR(255) NOT NULL,
  numero VARCHAR(20) NOT NULL,
  complemento VARCHAR(100) NULL,
  bairro VARCHAR(100) NOT NULL,
  cidade VARCHAR(100) NOT NULL,
  estado VARCHAR(2) NOT NULL,
  usuarios_id_usuario INT NOT NULL,
  PRIMARY KEY (id_endereco),
  INDEX fk_enderecos_usuarios_idx (usuarios_id_usuario ASC),
  CONSTRAINT fk_enderecos_usuarios
    FOREIGN KEY (usuarios_id_usuario)
    REFERENCES usuarios (id_usuario)
    ON DELETE CASCADE -- Se o usuário for deletado, seus endereços também são
    ON UPDATE CASCADE
);

-- Adicionar 'bairro' se estiver faltando
ALTER TABLE enderecos ADD COLUMN bairro VARCHAR(100) NOT NULL AFTER complemento;

-- Adicionar 'cidade' se estiver faltando
ALTER TABLE enderecos ADD COLUMN cidade VARCHAR(100) NOT NULL AFTER bairro;

-- Adicionar 'estado' se estiver faltando
ALTER TABLE enderecos ADD COLUMN estado VARCHAR(2) NOT NULL AFTER cidade;

-- -----------------------------------------------------
-- Tabela `pedidos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS pedidos (
  id_pedido INT NOT NULL AUTO_INCREMENT,
  datapedido TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  valorTotal DECIMAL(10, 2) NOT NULL,
  status VARCHAR(45) NOT NULL COMMENT 'Ex: Aprovado, Enviado, Entregue, Cancelado',
  usuarios_id_usuario INT NOT NULL,
  PRIMARY KEY (id_pedido),
  INDEX fk_pedidos_usuarios_idx (usuarios_id_usuario ASC),
  CONSTRAINT fk_pedidos_usuarios
    FOREIGN KEY (usuarios_id_usuario)
    REFERENCES usuarios (id_usuario)
    ON DELETE NO ACTION -- Não permite deletar um usuário que já fez pedidos
    ON UPDATE CASCADE
);

-- -----------------------------------------------------
-- Tabela `itenspedido` (ou Itens do Pedido)
-- (Colunas inferidas baseadas em uma estrutura comum para ligar produtos a pedidos)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS itenspedido (
  id_itempedido INT NOT NULL AUTO_INCREMENT,
  quantidade INT NOT NULL,
  preco_unitario DECIMAL(10, 2) NOT NULL,
  pedidos_id_pedido INT NOT NULL,
  produtos_id_produto INT NOT NULL,
  PRIMARY KEY (id_itempedido),
  INDEX fk_itenspedido_pedidos_idx (pedidos_id_pedido ASC),
  INDEX fk_itenspedido_produtos_idx (produtos_id_produto ASC),
  CONSTRAINT fk_itenspedido_pedidos
    FOREIGN KEY (pedidos_id_pedido)
    REFERENCES pedidos (id_pedido)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_itenspedido_produtos
    FOREIGN KEY (produtos_id_produto)
    REFERENCES produtos (id_produto)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
);


/* NOTA: A sua segunda imagem mostra a tabela 'pedidos_has_variacoesproduto'. 
  Isso sugere que você pode ter variações de produtos (ex: Tamanho P, M, G). 
  Abaixo está o código para essa tabela e uma tabela 'variacoesproduto' de exemplo.
  Se você não for usar variações, pode ignorar as duas tabelas abaixo.
*/

-- -----------------------------------------------------
-- Tabela `variacoesproduto` (Opcional, mas necessária para a tabela abaixo)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS variacoesproduto (
  id_variacao INT NOT NULL AUTO_INCREMENT,
  produtos_id_produto INT NOT NULL,
  nome_variacao VARCHAR(100) NOT NULL COMMENT 'Ex: Tamanho, Cor',
  valor_variacao VARCHAR(100) NOT NULL COMMENT 'Ex: P, M, G, Vermelho',
  estoque INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id_variacao),
  INDEX fk_variacoes_produtos_idx (produtos_id_produto ASC),
    CONSTRAINT fk_variacoes_produtos
    FOREIGN KEY (produtos_id_produto)
    REFERENCES produtos (id_produto)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- -----------------------------------------------------
-- Tabela `pedidos_has_variacoesproduto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS pedidos_has_variacoesproduto (
  pedidos_id_pedido INT NOT NULL,
  variacoesproduto_id_variacao INT NOT NULL,
  PRIMARY KEY (pedidos_id_pedido, variacoesproduto_id_variacao),
  INDEX fk_pedidos_has_variacoesproduto_variacoesproduto_idx (variacoesproduto_id_variacao ASC),
  INDEX fk_pedidos_has_variacoesproduto_pedidos_idx (pedidos_id_pedido ASC),
  CONSTRAINT fk_pedidos_has_variacoesproduto_pedidos
    FOREIGN KEY (pedidos_id_pedido)
    REFERENCES pedidos (id_pedido)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_pedidos_has_variacoesproduto_variacoesproduto
    FOREIGN KEY (variacoesproduto_id_variacao)
    REFERENCES variacoesproduto (id_variacao)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);