CREATE DATABASE circuito_sustentavel;
use circuito_sustentavel;
CREATE TABLE Cliente (
    id_cliente INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    cpf VARCHAR(14) UNIQUE,
    senha VARCHAR(255),
    premium BOOLEAN DEFAULT FALSE,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    telefone VARCHAR(50),
    data_nascimento DATETIME;
);

CREATE TABLE Vendedor (
    id_vendedor INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(255),
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    cpf VARCHAR(50),
    telefone VARCHAR(50),
    data_nascimento DATETIME;
);

CREATE TABLE Produto (
    id_produto INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    descricao TEXT,
    preco DECIMAL(10, 2),
    estoque INT,
    id_vendedor INT,
    FOREIGN KEY (id_vendedor) REFERENCES Vendedor(id_vendedor)
);

CREATE TABLE Carrinho (
    id_carrinho INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT,
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente)
);

CREATE TABLE Item_Carrinho (
    id_carrinho INT,
    id_produto INT,
    quantidade INT,
    PRIMARY KEY (id_carrinho, id_produto),
    FOREIGN KEY (id_carrinho) REFERENCES Carrinho(id_carrinho),
    FOREIGN KEY (id_produto) REFERENCES Produto(id_produto)
);
CREATE TABLE Pedido (
    id_pedido INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50),
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente)
);

CREATE TABLE Item_Pedido (
    id_pedido INT,
    id_produto INT,
    quantidade INT,
    preco DECIMAL(10, 2),
    PRIMARY KEY (id_pedido, id_produto),
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido),
    FOREIGN KEY (id_produto) REFERENCES Produto(id_produto)
);

CREATE TABLE Pagamento (
    id_pagamento INT PRIMARY KEY AUTO_INCREMENT,
    id_pedido INT,
    forma_pagamento VARCHAR(50),
    status VARCHAR(50),
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido)
);
CREATE TABLE Postagem (
    id_postagem INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT,
    titulo VARCHAR(255),
    conteudo TEXT,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente)
);

CREATE TABLE Comentario (
    id_comentario INT PRIMARY KEY AUTO_INCREMENT,
    id_postagem INT,
    id_cliente INT,
    conteudo TEXT,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_postagem) REFERENCES Postagem(id_postagem),
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente)
);
CREATE TABLE Reembolso (
    id_reembolso INT PRIMARY KEY AUTO_INCREMENT,
    id_pedido INT,
    motivo TEXT,
    status VARCHAR(50),
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido)
);
CREATE TABLE Assinatura (
    id_assinatura INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT,
    data_inicio DATE,
    data_fim DATE,
    ativa BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente)
);
CREATE TABLE Moeda (
    id_moeda INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT,
    quantidade INT,
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente)
);

CREATE TABLE Cupom (
    id_cupom INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(50) UNIQUE,
    desconto DECIMAL(5, 2),
    ativo BOOLEAN DEFAULT TRUE
);
CREATE TABLE Pergunta (
    id_pergunta INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT,
    id_produto INT,
    texto TEXT,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente),
    FOREIGN KEY (id_produto) REFERENCES Produto(id_produto)
);

CREATE TABLE Resposta (
    id_resposta INT PRIMARY KEY AUTO_INCREMENT,
    id_pergunta INT,
    id_vendedor INT,
    texto TEXT,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pergunta) REFERENCES Pergunta(id_pergunta),
    FOREIGN KEY (id_vendedor) REFERENCES Vendedor(id_vendedor)
);

CREATE TABLE Cotidiano (
    id_cotidiano INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT,
    carro VARCHAR(50),
    onibus VARCHAR(50),
    luz VARCHAR(50),
    gas VARCHAR(50),
    carne VARCHAR(50),
    reciclagem VARCHAR(50),
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente)
);
