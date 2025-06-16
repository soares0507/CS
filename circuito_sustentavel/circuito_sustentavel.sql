-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 16-Jun-2025 às 13:57
-- Versão do servidor: 10.4.22-MariaDB
-- versão do PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `circuito_sustentavel`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `ADM`
--

CREATE TABLE `ADM` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `cpf` varchar(20) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Assinatura`
--

CREATE TABLE `Assinatura` (
  `id_assinatura` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_vendedor` int(11) DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `ativa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Carrinho`
--

CREATE TABLE `Carrinho` (
  `id_carrinho` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_vendedor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `Carrinho`
--

INSERT INTO `Carrinho` (`id_carrinho`, `id_cliente`, `id_vendedor`) VALUES
(1, 2, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `Cliente`
--

CREATE TABLE `Cliente` (
  `id_cliente` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `premium` tinyint(1) DEFAULT 0,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `telefone` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `Cliente`
--

INSERT INTO `Cliente` (`id_cliente`, `nome`, `email`, `cpf`, `senha`, `premium`, `data_criacao`, `telefone`) VALUES
(2, 'Viana', 'danterodrigues@gmail.com', '122.222.222-22', '$2y$10$JvCyKzdkKL8S8HpZH5QGluMYpvHucnJC1PbUhDqUVa.OPWV78jvXW', 0, '2025-06-16 08:56:25', '(22) 22222-2222');

-- --------------------------------------------------------

--
-- Estrutura da tabela `Comentario`
--

CREATE TABLE `Comentario` (
  `id_comentario` int(11) NOT NULL,
  `id_postagem` int(11) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_vendedor` int(11) DEFAULT NULL,
  `conteudo` text DEFAULT NULL,
  `data` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Cotidiano`
--

CREATE TABLE `Cotidiano` (
  `id_cotidiano` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_vendedor` int(11) DEFAULT NULL,
  `carro` varchar(50) DEFAULT NULL,
  `onibus` varchar(50) DEFAULT NULL,
  `luz` varchar(50) DEFAULT NULL,
  `gas` varchar(50) DEFAULT NULL,
  `carne` varchar(50) DEFAULT NULL,
  `reciclagem` varchar(50) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `Cotidiano`
--

INSERT INTO `Cotidiano` (`id_cotidiano`, `id_cliente`, `id_vendedor`, `carro`, `onibus`, `luz`, `gas`, `carne`, `reciclagem`, `estado`) VALUES
(1, NULL, 1, 'nenhum', 'nenhum', 'ate100', '2meses', '0', 'sempre', 'saudavel'),
(2, 2, NULL, 'nenhum', 'nenhum', 'ate100', '2meses', '0', 'sempre', 'saudavel');

-- --------------------------------------------------------

--
-- Estrutura da tabela `Cupom`
--

CREATE TABLE `Cupom` (
  `id_cupom` int(11) NOT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `desconto` decimal(5,2) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `Cupom`
--

INSERT INTO `Cupom` (`id_cupom`, `codigo`, `desconto`, `ativo`) VALUES
(1, 'VERDE15', '15.00', 1),
(2, 'ECOCASA10', '10.00', 1),
(3, 'BEMVINDOSAOCS', '30.00', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `Curtida`
--

CREATE TABLE `Curtida` (
  `id_curtida` int(11) NOT NULL,
  `id_postagem` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_vendedor` int(11) DEFAULT NULL,
  `data` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `DocumentosVendedor`
--

CREATE TABLE `DocumentosVendedor` (
  `id_documento` int(11) NOT NULL,
  `id_vendedor` int(11) DEFAULT NULL,
  `foto_usuario` varchar(255) DEFAULT NULL,
  `foto_rg_frente` varchar(255) DEFAULT NULL,
  `foto_rg_verso` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Endereco`
--

CREATE TABLE `Endereco` (
  `id_endereco` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_vendedor` int(11) DEFAULT NULL,
  `rua` varchar(255) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Item_Carrinho`
--

CREATE TABLE `Item_Carrinho` (
  `id_carrinho` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `quantidade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Item_Pedido`
--

CREATE TABLE `Item_Pedido` (
  `id_pedido` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `Item_Pedido`
--

INSERT INTO `Item_Pedido` (`id_pedido`, `id_produto`, `quantidade`, `preco`) VALUES
(1, 3, 1, '20000.00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `Moeda`
--

CREATE TABLE `Moeda` (
  `id_moeda` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_vendedor` int(11) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Pagamento`
--

CREATE TABLE `Pagamento` (
  `id_pagamento` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `forma_pagamento` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `Pagamento`
--

INSERT INTO `Pagamento` (`id_pagamento`, `id_pedido`, `forma_pagamento`, `status`) VALUES
(1, 1, 'pix', 'Confirmado');

-- --------------------------------------------------------

--
-- Estrutura da tabela `Pedido`
--

CREATE TABLE `Pedido` (
  `id_pedido` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_vendedor` int(11) DEFAULT NULL,
  `data` datetime DEFAULT current_timestamp(),
  `codigo_rastreio` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `Pedido`
--

INSERT INTO `Pedido` (`id_pedido`, `id_cliente`, `id_vendedor`, `data`, `codigo_rastreio`, `status`) VALUES
(1, 2, NULL, '2025-06-16 08:56:43', NULL, 'Pedido enviado ao vendedor');

-- --------------------------------------------------------

--
-- Estrutura da tabela `Pergunta`
--

CREATE TABLE `Pergunta` (
  `id_pergunta` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_vendedor` int(11) DEFAULT NULL,
  `id_produto` int(11) DEFAULT NULL,
  `texto` text DEFAULT NULL,
  `data` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Postagem`
--

CREATE TABLE `Postagem` (
  `id_postagem` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_vendedor` int(11) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `conteudo` text DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `data` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Produto`
--

CREATE TABLE `Produto` (
  `id_produto` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `estoque` int(11) DEFAULT NULL,
  `id_vendedor` int(11) DEFAULT NULL,
  `imagens` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `Produto`
--

INSERT INTO `Produto` (`id_produto`, `nome`, `descricao`, `preco`, `estoque`, `id_vendedor`, `imagens`) VALUES
(1, 'RTX 9060 TI', 'A PLACA DE VIDEO MAIS PODEROSA DO MERCADO ATUALMENTE', '80000.00', 4, 1, 'uploads_produtos/ar.jpeg'),
(2, 'Tubarão Com Tênis', 'Incrivel tubarao com tenis', '550.00', 7, 1, 'uploads_produtos/tralalelo.jpeg'),
(3, 'Playstation 6 Deluxe Edition', 'SIM! ja temos acesso antecipado ao novo e incrivel ps6', '20000.00', 3, 1, 'uploads_produtos/ps6.jpeg'),
(4, 'Xbox 720', 'Xbox 720 com acesso exclusivo ao Skate 4', '12000.00', 4, 1, 'uploads_produtos/xbox.webp'),
(5, 'Nintendo Switch 3 Plus', 'Nintendo switch 3 com os jogos exclusivos de preço base de R$400,00', '40000.00', 1, 1, 'uploads_produtos/nintendo.webp'),
(6, 'Jogo Eletrônico Almo Souls', 'Jogo no estilo souls', '600.00', 9, 1, 'uploads_produtos/almo.png'),
(7, 'Iphone 20', 'Novo iphone com a melhor qualidade de câmera do mercad', '75000.00', 8, 1, 'uploads_produtos/iphone.webp'),
(8, 'Notbook Mais Potente do Mundo', 'Notbook mais futurista do mundo', '86000.00', 6, 1, 'uploads_produtos/not.png');

-- --------------------------------------------------------

--
-- Estrutura da tabela `Reembolso`
--

CREATE TABLE `Reembolso` (
  `id_reembolso` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `motivo` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Resposta`
--

CREATE TABLE `Resposta` (
  `id_resposta` int(11) NOT NULL,
  `id_pergunta` int(11) DEFAULT NULL,
  `id_vendedor` int(11) DEFAULT NULL,
  `texto` text DEFAULT NULL,
  `data` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Vendedor`
--

CREATE TABLE `Vendedor` (
  `id_vendedor` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `cpf` varchar(50) DEFAULT NULL,
  `telefone` varchar(50) DEFAULT NULL,
  `premium` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `Vendedor`
--

INSERT INTO `Vendedor` (`id_vendedor`, `nome`, `email`, `senha`, `data_criacao`, `cpf`, `telefone`, `premium`) VALUES
(1, 'Mateus Soares Viana', 'capudo@gmail.com', '$2y$10$L4n2KQpgTHqq29lAH5lHn.VeaNUXJsAnp.xm9ddZqr17wK7dJKk9K', '2025-06-16 08:54:51', '555.555.555-55', '(11) 11111-1111', 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `ADM`
--
ALTER TABLE `ADM`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices para tabela `Assinatura`
--
ALTER TABLE `Assinatura`
  ADD PRIMARY KEY (`id_assinatura`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- Índices para tabela `Carrinho`
--
ALTER TABLE `Carrinho`
  ADD PRIMARY KEY (`id_carrinho`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- Índices para tabela `Cliente`
--
ALTER TABLE `Cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices para tabela `Comentario`
--
ALTER TABLE `Comentario`
  ADD PRIMARY KEY (`id_comentario`),
  ADD KEY `id_postagem` (`id_postagem`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- Índices para tabela `Cotidiano`
--
ALTER TABLE `Cotidiano`
  ADD PRIMARY KEY (`id_cotidiano`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- Índices para tabela `Cupom`
--
ALTER TABLE `Cupom`
  ADD PRIMARY KEY (`id_cupom`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices para tabela `Curtida`
--
ALTER TABLE `Curtida`
  ADD PRIMARY KEY (`id_curtida`),
  ADD UNIQUE KEY `unique_like` (`id_postagem`,`id_cliente`,`id_vendedor`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- Índices para tabela `DocumentosVendedor`
--
ALTER TABLE `DocumentosVendedor`
  ADD PRIMARY KEY (`id_documento`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- Índices para tabela `Endereco`
--
ALTER TABLE `Endereco`
  ADD PRIMARY KEY (`id_endereco`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- Índices para tabela `Item_Carrinho`
--
ALTER TABLE `Item_Carrinho`
  ADD PRIMARY KEY (`id_carrinho`,`id_produto`),
  ADD KEY `id_produto` (`id_produto`);

--
-- Índices para tabela `Item_Pedido`
--
ALTER TABLE `Item_Pedido`
  ADD PRIMARY KEY (`id_pedido`,`id_produto`),
  ADD KEY `id_produto` (`id_produto`);

--
-- Índices para tabela `Moeda`
--
ALTER TABLE `Moeda`
  ADD PRIMARY KEY (`id_moeda`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- Índices para tabela `Pagamento`
--
ALTER TABLE `Pagamento`
  ADD PRIMARY KEY (`id_pagamento`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Índices para tabela `Pedido`
--
ALTER TABLE `Pedido`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- Índices para tabela `Pergunta`
--
ALTER TABLE `Pergunta`
  ADD PRIMARY KEY (`id_pergunta`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_produto` (`id_produto`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- Índices para tabela `Postagem`
--
ALTER TABLE `Postagem`
  ADD PRIMARY KEY (`id_postagem`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- Índices para tabela `Produto`
--
ALTER TABLE `Produto`
  ADD PRIMARY KEY (`id_produto`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- Índices para tabela `Reembolso`
--
ALTER TABLE `Reembolso`
  ADD PRIMARY KEY (`id_reembolso`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Índices para tabela `Resposta`
--
ALTER TABLE `Resposta`
  ADD PRIMARY KEY (`id_resposta`),
  ADD KEY `id_pergunta` (`id_pergunta`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- Índices para tabela `Vendedor`
--
ALTER TABLE `Vendedor`
  ADD PRIMARY KEY (`id_vendedor`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `ADM`
--
ALTER TABLE `ADM`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Assinatura`
--
ALTER TABLE `Assinatura`
  MODIFY `id_assinatura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Carrinho`
--
ALTER TABLE `Carrinho`
  MODIFY `id_carrinho` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `Cliente`
--
ALTER TABLE `Cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `Comentario`
--
ALTER TABLE `Comentario`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Cotidiano`
--
ALTER TABLE `Cotidiano`
  MODIFY `id_cotidiano` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `Cupom`
--
ALTER TABLE `Cupom`
  MODIFY `id_cupom` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `Curtida`
--
ALTER TABLE `Curtida`
  MODIFY `id_curtida` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `DocumentosVendedor`
--
ALTER TABLE `DocumentosVendedor`
  MODIFY `id_documento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Endereco`
--
ALTER TABLE `Endereco`
  MODIFY `id_endereco` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Moeda`
--
ALTER TABLE `Moeda`
  MODIFY `id_moeda` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Pagamento`
--
ALTER TABLE `Pagamento`
  MODIFY `id_pagamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `Pedido`
--
ALTER TABLE `Pedido`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `Pergunta`
--
ALTER TABLE `Pergunta`
  MODIFY `id_pergunta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Postagem`
--
ALTER TABLE `Postagem`
  MODIFY `id_postagem` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Produto`
--
ALTER TABLE `Produto`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `Reembolso`
--
ALTER TABLE `Reembolso`
  MODIFY `id_reembolso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Resposta`
--
ALTER TABLE `Resposta`
  MODIFY `id_resposta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Vendedor`
--
ALTER TABLE `Vendedor`
  MODIFY `id_vendedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `Assinatura`
--
ALTER TABLE `Assinatura`
  ADD CONSTRAINT `Assinatura_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `Cliente` (`id_cliente`),
  ADD CONSTRAINT `Assinatura_ibfk_2` FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id_vendedor`);

--
-- Limitadores para a tabela `Carrinho`
--
ALTER TABLE `Carrinho`
  ADD CONSTRAINT `Carrinho_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `Cliente` (`id_cliente`) ON DELETE SET NULL,
  ADD CONSTRAINT `Carrinho_ibfk_2` FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id_vendedor`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `Comentario`
--
ALTER TABLE `Comentario`
  ADD CONSTRAINT `Comentario_ibfk_1` FOREIGN KEY (`id_postagem`) REFERENCES `Postagem` (`id_postagem`),
  ADD CONSTRAINT `Comentario_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `Cliente` (`id_cliente`),
  ADD CONSTRAINT `Comentario_ibfk_3` FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id_vendedor`);

--
-- Limitadores para a tabela `Cotidiano`
--
ALTER TABLE `Cotidiano`
  ADD CONSTRAINT `Cotidiano_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `Cliente` (`id_cliente`),
  ADD CONSTRAINT `Cotidiano_ibfk_2` FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id_vendedor`);

--
-- Limitadores para a tabela `Curtida`
--
ALTER TABLE `Curtida`
  ADD CONSTRAINT `Curtida_ibfk_1` FOREIGN KEY (`id_postagem`) REFERENCES `Postagem` (`id_postagem`),
  ADD CONSTRAINT `Curtida_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `Cliente` (`id_cliente`),
  ADD CONSTRAINT `Curtida_ibfk_3` FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id_vendedor`);

--
-- Limitadores para a tabela `DocumentosVendedor`
--
ALTER TABLE `DocumentosVendedor`
  ADD CONSTRAINT `DocumentosVendedor_ibfk_1` FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id_vendedor`);

--
-- Limitadores para a tabela `Endereco`
--
ALTER TABLE `Endereco`
  ADD CONSTRAINT `Endereco_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `Cliente` (`id_cliente`),
  ADD CONSTRAINT `Endereco_ibfk_2` FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id_vendedor`);

--
-- Limitadores para a tabela `Item_Carrinho`
--
ALTER TABLE `Item_Carrinho`
  ADD CONSTRAINT `Item_Carrinho_ibfk_1` FOREIGN KEY (`id_carrinho`) REFERENCES `Carrinho` (`id_carrinho`),
  ADD CONSTRAINT `Item_Carrinho_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `Produto` (`id_produto`);

--
-- Limitadores para a tabela `Item_Pedido`
--
ALTER TABLE `Item_Pedido`
  ADD CONSTRAINT `Item_Pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `Pedido` (`id_pedido`),
  ADD CONSTRAINT `Item_Pedido_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `Produto` (`id_produto`);

--
-- Limitadores para a tabela `Moeda`
--
ALTER TABLE `Moeda`
  ADD CONSTRAINT `Moeda_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `Cliente` (`id_cliente`),
  ADD CONSTRAINT `Moeda_ibfk_2` FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id_vendedor`);

--
-- Limitadores para a tabela `Pagamento`
--
ALTER TABLE `Pagamento`
  ADD CONSTRAINT `Pagamento_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `Pedido` (`id_pedido`);

--
-- Limitadores para a tabela `Pedido`
--
ALTER TABLE `Pedido`
  ADD CONSTRAINT `Pedido_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `Cliente` (`id_cliente`),
  ADD CONSTRAINT `Pedido_ibfk_2` FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id_vendedor`);

--
-- Limitadores para a tabela `Pergunta`
--
ALTER TABLE `Pergunta`
  ADD CONSTRAINT `Pergunta_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `Cliente` (`id_cliente`),
  ADD CONSTRAINT `Pergunta_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `Produto` (`id_produto`),
  ADD CONSTRAINT `Pergunta_ibfk_3` FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id_vendedor`);

--
-- Limitadores para a tabela `Postagem`
--
ALTER TABLE `Postagem`
  ADD CONSTRAINT `Postagem_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `Cliente` (`id_cliente`),
  ADD CONSTRAINT `Postagem_ibfk_2` FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id_vendedor`);

--
-- Limitadores para a tabela `Reembolso`
--
ALTER TABLE `Reembolso`
  ADD CONSTRAINT `Reembolso_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `Pedido` (`id_pedido`);

--
-- Limitadores para a tabela `Resposta`
--
ALTER TABLE `Resposta`
  ADD CONSTRAINT `Resposta_ibfk_1` FOREIGN KEY (`id_pergunta`) REFERENCES `Pergunta` (`id_pergunta`),
  ADD CONSTRAINT `Resposta_ibfk_2` FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id_vendedor`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
