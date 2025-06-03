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
    telefone VARCHAR(50)
    
);

CREATE TABLE Vendedor (
    id_vendedor INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(255),
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    cpf VARCHAR(50),
    telefone VARCHAR(50),
    premium BOOLEAN DEFAULT FALSE
);

CREATE TABLE Produto (
    id_produto INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    descricao TEXT,
    preco DECIMAL(10, 2),
    estoque INT,
    id_vendedor INT,
    imagens TEXT,
    FOREIGN KEY (id_vendedor) REFERENCES Vendedor(id_vendedor)
);

CREATE TABLE Carrinho (
    id_carrinho INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT NULL,
    id_vendedor INT NULL,
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente) ON DELETE SET NULL,
    FOREIGN KEY (id_vendedor) REFERENCES Vendedor(id_vendedor) ON DELETE SET NULL
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
    id_vendedor INT NULL,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    codigo_rastreio VARCHAR(100) NULL,
    status VARCHAR(50),
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente),
    FOREIGN KEY (id_vendedor) REFERENCES Vendedor(id_vendedor)
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
    id_vendedor INT NULL,
    titulo VARCHAR(255),
    conteudo TEXT,
    imagem VARCHAR(255), -- Novo campo para imagem
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente),
    FOREIGN KEY (id_vendedor) REFERENCES Vendedor(id_vendedor)
);
CREATE TABLE Curtida (
    id_curtida INT AUTO_INCREMENT PRIMARY KEY,
    id_postagem INT NOT NULL,
    id_cliente INT NULL,
    id_vendedor INT NULL,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (id_postagem, id_cliente, id_vendedor),
    FOREIGN KEY (id_postagem) REFERENCES Postagem(id_postagem),
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente),
    FOREIGN KEY (id_vendedor) REFERENCES Vendedor(id_vendedor)
);

CREATE TABLE Comentario (
    id_comentario INT PRIMARY KEY AUTO_INCREMENT,
    id_postagem INT,
    id_cliente INT,
    id_vendedor INT NULL,
    conteudo TEXT,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_postagem) REFERENCES Postagem(id_postagem),
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente),
    FOREIGN KEY (id_vendedor) REFERENCES Vendedor(id_vendedor)
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
    id_vendedor INT NULL,
    data_inicio DATE,
    data_fim DATE,
    ativa BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente),
    FOREIGN KEY (id_vendedor) REFERENCES Vendedor(id_vendedor)
);
CREATE TABLE Moeda (
    id_moeda INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT,
    id_vendedor INT NULL,
    quantidade INT,
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente),
    FOREIGN KEY (id_vendedor) REFERENCES Vendedor(id_vendedor)
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
    id_vendedor INT NULL,
    id_produto INT,
    texto TEXT,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente),
    FOREIGN KEY (id_produto) REFERENCES Produto(id_produto),
    FOREIGN KEY (id_vendedor) REFERENCES Vendedor(id_vendedor)
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
    id_vendedor INT NULL,
    carro VARCHAR(50),
    onibus VARCHAR(50),
    luz VARCHAR(50),
    gas VARCHAR(50),
    carne VARCHAR(50),
    reciclagem VARCHAR(50),
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente),
    FOREIGN KEY (id_vendedor) REFERENCES Vendedor(id_vendedor)
);

CREATE TABLE Endereco (
    id_endereco INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT NULL,
    id_vendedor INT NULL,
    rua VARCHAR(255),
    numero VARCHAR(20),
    complemento VARCHAR(100),
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    estado VARCHAR(50),
    cep VARCHAR(20),
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente),
    FOREIGN KEY (id_vendedor) REFERENCES Vendedor(id_vendedor)
);

CREATE TABLE DocumentosVendedor (
    id_documento INT PRIMARY KEY AUTO_INCREMENT,
    id_vendedor INT,
    foto_usuario VARCHAR(255),
    foto_rg_frente VARCHAR(255),
    foto_rg_verso VARCHAR(255),
    FOREIGN KEY (id_vendedor) REFERENCES Vendedor(id_vendedor)
);
CREATE TABLE ADM (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cpf VARCHAR(20) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

