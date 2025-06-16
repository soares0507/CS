-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 16-Jun-2025 às 13:44
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
-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 16-Jun-2025 às 13:44
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
(3, 'Playstation 6 Deluxe Edition', 'SIM! ja temos acesso antecipado ao novo e incrivel ps6', '20000.00', 4, 1, 'uploads_produtos/ps6.jpeg'),
(4, 'Xbox 720', 'Xbox 720 com acesso exclusivo ao Skate 4', '12000.00', 4, 1, 'uploads_produtos/xbox.webp'),
(5, 'Nintendo Switch 3 Plus', 'Nintendo switch 3 com os jogos exclusivos de preço base de R$400,00', '40000.00', 1, 1, 'uploads_produtos/nintendo.webp'),
(6, 'Jogo Eletrônico Almo Souls', 'Jogo no estilo souls', '600.00', 9, 1, 'uploads_produtos/almo.png'),
(7, 'Iphone 20', 'Novo iphone com a melhor qualidade de câmera do mercad', '75000.00', 8, 1, 'uploads_produtos/iphone.webp'),
(8, 'Notbook Mais Potente do Mundo', 'Notbook mais futurista do mundo', '86000.00', 6, 1, 'uploads_produtos/not.png');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `Produto`
--
ALTER TABLE `Produto`
  ADD PRIMARY KEY (`id_produto`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `Produto`
--
ALTER TABLE `Produto`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `Produto`
--
ALTER TABLE `Produto`
  ADD CONSTRAINT `Produto_ibfk_1` FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id_vendedor`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

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
(3, 'Playstation 6 Deluxe Edition', 'SIM! ja temos acesso antecipado ao novo e incrivel ps6', '20000.00', 4, 1, 'uploads_produtos/ps6.jpeg'),
(4, 'Xbox 720', 'Xbox 720 com acesso exclusivo ao Skate 4', '12000.00', 4, 1, 'uploads_produtos/xbox.webp'),
(5, 'Nintendo Switch 3 Plus', 'Nintendo switch 3 com os jogos exclusivos de preço base de R$400,00', '40000.00', 1, 1, 'uploads_produtos/nintendo.webp'),
(6, 'Jogo Eletrônico Almo Souls', 'Jogo no estilo souls', '600.00', 9, 1, 'uploads_produtos/almo.png'),
(7, 'Iphone 20', 'Novo iphone com a melhor qualidade de câmera do mercad', '75000.00', 8, 1, 'uploads_produtos/iphone.webp'),
(8, 'Notbook Mais Potente do Mundo', 'Notbook mais futurista do mundo', '86000.00', 6, 1, 'uploads_produtos/not.png');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `Produto`
--
ALTER TABLE `Produto`
  ADD PRIMARY KEY (`id_produto`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `Produto`
--
ALTER TABLE `Produto`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `Produto`
--
ALTER TABLE `Produto`
  ADD CONSTRAINT `Produto_ibfk_1` FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id_vendedor`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
