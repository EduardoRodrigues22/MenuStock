-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2026 at 05:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `menustock`
--

-- --------------------------------------------------------

--
-- Table structure for table `carrinho`
--

CREATE TABLE `carrinho` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `prato_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `obs_item` text DEFAULT NULL,
  `preco_unit` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carrinho`
--

INSERT INTO `carrinho` (`id`, `usuario_id`, `prato_id`, `quantidade`, `obs_item`, `preco_unit`, `created_at`, `updated_at`) VALUES
(44, 11, 2, 1, '', 24.90, '2026-06-01 00:01:11', '2026-06-01 00:01:11'),
(50, 10, 2, 2, '', 24.90, '2026-06-01 03:11:30', '2026-06-01 03:11:34');

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ordem_exibicao` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `descricao`, `ordem_exibicao`, `created_at`, `updated_at`) VALUES
(1, 'Entradas', 'Petiscos e aperitivos para comecar bem', 1, '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(2, 'Pratos Principais', 'Os grandes classicos da nossa cozinha', 2, '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(3, 'Massas', 'Massas artesanais feitas no dia', 3, '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(4, 'Grelhados', 'Carnes e peixes na brasa', 4, '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(5, 'Sobremesas', 'Doces para adocar o final da refeicao', 5, '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(6, 'Bebidas', 'Sucos, refrigerantes e drinques', 6, '2026-05-08 16:48:24', '2026-05-08 16:48:24');

-- --------------------------------------------------------

--
-- Table structure for table `ingredientes`
--

CREATE TABLE `ingredientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `unidade` varchar(20) DEFAULT 'g' COMMENT 'g, ml, unidade, colher...',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredientes`
--

INSERT INTO `ingredientes` (`id`, `nome`, `unidade`, `created_at`, `updated_at`) VALUES
(1, 'Farinha de trigo', 'g', '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(2, 'Leite integral', 'ml', '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(3, 'Ovos', 'uni', '2026-05-08 16:48:24', '2026-05-22 18:34:14'),
(4, 'Manteiga', 'g', '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(5, 'Alho', 'dente', '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(6, 'Azeite', 'ml', '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(9, 'Frango', 'g', '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(10, 'Carne bovina', 'g', '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(11, 'Mussarela', 'g', '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(12, 'Tomate', 'uni', '2026-05-08 16:48:24', '2026-05-22 18:33:40'),
(13, 'Massa de macarrao', 'g', '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(14, 'Creme de leite', 'ml', '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(15, 'Chocolate', 'g', '2026-05-08 16:48:24', '2026-05-08 16:48:24'),
(16, 'Pão italiano', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(17, 'Espaguete', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(18, 'Fettuccine', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(19, 'Penne', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(20, 'Rigatoni', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(21, 'Massa fresca lasanha', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(22, 'Bacalhau', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(23, 'Salmão', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(24, 'Camarão', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(25, 'Picanha', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(26, 'Costela de porco', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(27, 'Contra-filé', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(28, 'Ancho', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(29, 'Guanciale', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(30, 'Queijo parmesão', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(31, 'Queijo pecorino', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(32, 'Leite condensado', 'ml', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(33, 'Cream cheese', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(34, 'Presunto', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(35, 'Molho de tomate', 'ml', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(36, 'Molho madeira', 'ml', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(37, 'Molho barbecue', 'ml', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(38, 'Bechamel', 'ml', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(39, 'Pesto de manjericão', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(40, 'Chimichurri', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(41, 'Vinagrete', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(42, 'Mix de funghi', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(43, 'Rúcula', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(44, 'Azeitonas', 'g', '2026-05-22 12:37:46', '2026-05-22 13:08:52'),
(45, 'Aspargos', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(46, 'Legumes grelhados', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(47, 'Manjericão fresco', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(48, 'Hortelã', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(49, 'Mix de ervas finas', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(50, 'Azeite trufado', 'ml', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(51, 'Manteiga de alho', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(52, 'Manteiga de ervas', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(53, 'Limão siciliano', 'uni', '2026-05-22 12:37:46', '2026-05-22 18:33:59'),
(54, 'Limão taiti', 'uni', '2026-05-22 12:37:46', '2026-05-22 18:34:04'),
(55, 'Maracujá', 'uni', '2026-05-22 12:37:46', '2026-05-22 18:34:09'),
(56, 'Morango', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(57, 'Framboesa', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(58, 'Mirtilo', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(59, 'Pêssego', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(60, 'Laranja', 'uni', '2026-05-22 12:37:46', '2026-05-22 18:33:54'),
(61, 'Calda de caramelo', 'ml', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(62, 'Farinha de rosca', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(63, 'Farofa', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(64, 'Arroz branco', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(65, 'Arroz negro', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(66, 'Batata rústica', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(67, 'Purê de batata', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(68, 'Pinoli', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(69, 'Chocolate meio amargo', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(70, 'Biscoito base', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(71, 'Sorvete de baunilha', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(72, 'Sorvete de creme', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(73, 'Chá preto', 'g', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(74, 'Água mineral', 'ml', '2026-05-22 12:37:46', '2026-05-22 12:37:46'),
(75, 'Gelo', 'uni', '2026-05-22 12:37:46', '2026-05-22 18:33:49'),
(76, 'Batata Frita', 'g', '2026-05-22 13:03:28', '2026-05-22 13:03:28'),
(77, 'Ovos de Codorna', 'g', '2026-05-22 13:09:36', '2026-05-22 13:09:36'),
(78, 'Batata Frita ou Purê de Batata', 'g', '2026-05-24 16:07:35', '2026-05-24 16:07:35'),
(80, 'Feijão-tropeiro', 'g', '2026-05-24 16:08:39', '2026-05-24 16:08:39');

-- --------------------------------------------------------

--
-- Table structure for table `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `prato_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `preco_unit` decimal(10,2) NOT NULL COMMENT 'Preco congelado no momento do pedido',
  `obs_item` text DEFAULT NULL COMMENT 'Ex: sem cebola'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `itens_pedido`
--

INSERT INTO `itens_pedido` (`id`, `pedido_id`, `prato_id`, `quantidade`, `preco_unit`, `obs_item`) VALUES
(17, 11, 2, 1, 24.90, ''),
(18, 12, 1, 4, 18.90, ''),
(19, 13, 3, 13, 42.90, ''),
(20, 14, 3, 1, 42.90, ''),
(21, 15, 2, 1, 24.90, ''),
(22, 16, 21, 3, 34.90, ''),
(23, 17, 17, 2, 21.90, '');

-- --------------------------------------------------------

--
-- Table structure for table `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `status` enum('recebido','preparo','pronto','entregue','cancelado') NOT NULL DEFAULT 'recebido',
  `obs_geral` text DEFAULT NULL COMMENT 'Observacao geral do pedido',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mesa` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `status`, `obs_geral`, `total`, `created_at`, `updated_at`, `mesa`) VALUES
(11, 10, 'recebido', '', 24.90, '2026-05-22 13:23:20', '2026-05-22 13:23:20', NULL),
(12, 10, 'preparo', '', 75.60, '2026-05-22 13:24:27', '2026-05-22 13:26:08', NULL),
(13, 11, 'entregue', '', 557.70, '2026-05-22 13:25:13', '2026-05-22 13:33:35', NULL),
(14, 10, 'cancelado', '', 42.90, '2026-05-22 13:26:32', '2026-05-22 13:33:32', NULL),
(15, 10, 'preparo', '', 24.90, '2026-06-01 01:02:32', '2026-06-01 01:03:13', 1),
(16, NULL, 'recebido', '', 104.70, '2026-06-01 01:03:33', '2026-06-01 01:03:33', 3),
(17, NULL, 'recebido', '', 43.80, '2026-06-01 02:26:54', '2026-06-01 02:26:54', 2);

-- --------------------------------------------------------

--
-- Table structure for table `pratos`
--

CREATE TABLE `pratos` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `tempo_preparo` int(11) DEFAULT 0 COMMENT 'Tempo em minutos',
  `disponivel` tinyint(1) DEFAULT 1 COMMENT '1=disponivel 0=indisponivel',
  `imagem` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pratos`
--

INSERT INTO `pratos` (`id`, `categoria_id`, `nome`, `descricao`, `preco`, `tempo_preparo`, `disponivel`, `imagem`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bruschetta Classica', 'Pao italiano grelhado com tomate, alho e manjericao fresco', 18.90, 15, 1, '76b5ed441a7509959348e7d5.jpg', '2026-05-08 16:48:24', '2026-05-24 15:58:45'),
(2, 1, 'Bolinho de Bacalhau', 'Bolinhos crocantes de bacalhau com maionese de ervas', 24.90, 30, 1, '25cb9aebacf054d84601b131.webp', '2026-05-08 16:48:24', '2026-05-24 15:58:17'),
(3, 2, 'Frango ao Molho Madeira', 'File de frango grelhado com molho madeira, aroz e purê de batatas', 42.90, 120, 1, '48f1d6caf31973e82785417e.webp', '2026-05-08 16:48:24', '2026-05-22 13:11:56'),
(4, 2, 'Picanha na Brasa', 'Picanha grelhada no ponto com farofa e vinagrete', 140.00, 120, 1, '42615c200a39efa3485f868e.webp', '2026-05-08 16:48:24', '2026-05-24 16:01:24'),
(5, 3, 'Carbonara Tradicional', 'Espaguete com guanciale, ovos, queijo pecorino e pimenta', 39.90, 40, 1, '6e93c2bd782fefa9ab21954c.webp', '2026-05-08 16:48:24', '2026-05-24 16:13:54'),
(6, 3, 'Fettuccine ao Funghi', 'Fettuccine com mix de funghi e creme de leite', 44.90, 45, 1, 'a15308534b5c4443a4495ccd.jpg', '2026-05-08 16:48:24', '2026-05-24 16:15:11'),
(7, 4, 'Salmao Grelhado', 'File de salmao com legumes grelhados e limao siciliano', 64.90, 30, 1, 'afb2a07d092b8996bba72e6e.jpg', '2026-05-08 16:48:24', '2026-05-24 16:56:56'),
(8, 5, 'Petit Gateau', 'Bolinho de chocolate quente com sorvete de baunilha', 22.90, 30, 1, 'fa028976254e7efe217658d7.png', '2026-05-08 16:48:24', '2026-05-24 17:00:58'),
(9, 5, 'Pudim de Leite Condensado', 'Pudim cremoso com calda de caramelo', 16.90, 40, 1, '3330cf043c10ca8f73e166ac.webp', '2026-05-08 16:48:24', '2026-05-24 17:01:51'),
(10, 6, 'Suco de Laranja Natural', 'Suco espremido na hora, 500ml', 12.90, 15, 1, '18d438c32b4b42dce9af2d8c.webp', '2026-05-08 16:48:24', '2026-05-24 17:06:10'),
(11, 1, 'Tábua de Frios', 'Seleção de queijos, presuntos e azeitonas com torradas', 38.90, 45, 1, '4be512fb9af6204238e471c8.jpg', '2026-05-22 12:31:36', '2026-05-24 15:59:49'),
(12, 2, 'Filé à Parmegiana', 'Filé empanado com molho de tomate, presunto e queijo gratinado, acompanha arroz e fritas', 54.90, 60, 1, '98063e03da16cfb268bc9b30.jpg', '2026-05-22 12:31:36', '2026-05-24 16:00:34'),
(13, 3, 'Lasanha à Bolonhesa', 'Camadas de massa fresca com molho bolonhesa e bechamel gratinado', 46.90, 60, 1, '522bc7b62e5f41d7f6d165d3.jpg', '2026-05-22 12:31:36', '2026-05-24 16:16:02'),
(14, 4, 'Costela de Porco Grelhada', 'Costela suína marinada, grelhada lentamente com molho barbecue artesanal', 62.90, 30, 1, 'e2629ee156e84af6be565d44.png', '2026-05-22 12:31:36', '2026-05-24 16:55:22'),
(15, 4, 'Frango Grelhado com Ervas', 'Peito de frango grelhado com mix de ervas finas, acompanha legumes salteados', 38.90, 20, 1, '23b8ae1202b8f977ab4e2ba9.webp', '2026-05-22 12:31:36', '2026-05-24 16:56:03'),
(16, 4, 'Ancho Grelhado', 'Corte ancho argentino grelhado ao ponto com manteiga de alho, chimichurri e ovos', 94.90, 50, 1, 'a7af77da322c491706756647.webp', '2026-05-22 12:31:36', '2026-05-24 16:18:58'),
(17, 5, 'Brownie com Sorvete', 'Brownie quente de chocolate meio amargo com sorvete de creme e calda de chocolate', 21.90, 30, 1, '200e6f820a0173d4a075de18.jpg', '2026-05-22 12:31:36', '2026-05-24 16:57:41'),
(18, 6, 'Limonada Suíça', 'Limonada cremosa com leite condensado e hortelã, 400ml', 14.90, 20, 1, '85d9e0160ba98d87f28f86bc.webp', '2026-05-22 12:31:36', '2026-05-24 17:04:10'),
(19, 6, 'Refrigerante Lata', 'Coca-Cola, Guaraná Antarctica ou Fanta, 350ml', 8.00, 5, 1, '704f30980d54d11975f9a96d.jpg', '2026-05-22 12:31:36', '2026-05-24 17:05:22'),
(20, 6, 'Água Mineral', 'Água mineral sem gás ou com gás, 500ml', 5.00, 5, 1, 'a10b973e037913714e02a684.webp', '2026-05-22 12:31:36', '2026-05-24 17:02:51'),
(21, 1, 'Carpaccio de Carne', 'Finas fatias de filé mignon crú com rúcula, parmesão e azeite trufado', 34.90, 30, 1, '24e790fd0c1f20ff2610f084.webp', '2026-05-22 12:33:15', '2026-05-24 15:59:35'),
(22, 1, 'Camarão Empanado', 'Camarões empanados crocantes com molho tártaro e limão', 42.90, 30, 1, '4c3a75b872f5098ec0c0be70.jpg', '2026-05-22 12:33:15', '2026-05-24 15:59:25'),
(23, 2, 'Salmão ao Molho de Maracujá', 'Filé de salmão grelhado com molho de maracujá, arroz negro e aspargos', 72.90, 60, 1, '2c89a2080fd7719d14735b28.png', '2026-05-22 12:33:15', '2026-05-24 16:11:47'),
(24, 2, 'Costelinha de Porco ao Barbecue', 'Costelinha suína assada lentamente com molho barbecue defumado, acompanha purê rústico', 67.90, 90, 1, '591aa3b69f2840ef8d1536f3.webp', '2026-05-22 12:33:15', '2026-05-24 16:00:25'),
(25, 3, 'Penne ao Pesto de Manjericão', 'Penne com pesto fresco de manjericão, pinoli torrado e parmesão ralado', 36.90, 40, 1, '0c57a881f67a5e75a4ca315f.jpg', '2026-05-22 12:33:15', '2026-05-24 16:17:08'),
(26, 3, 'Rigatoni à Arrabiata', 'Rigatoni com molho de tomate apimentado, alho e azeitonas pretas', 33.90, 30, 1, '11860fa1f8522127e4a6a775.jpg', '2026-05-22 12:33:15', '2026-05-24 16:18:05'),
(27, 4, 'Contra-Filé Grelhado', 'Contra-filé grelhado no ponto com manteiga de ervas, acompanha batata rústica', 58.90, 45, 1, '1158945d28a82ce73e86f337.png', '2026-05-22 12:33:15', '2026-05-24 16:21:01'),
(28, 5, 'Cheesecake de Frutas Vermelhas', 'Cheesecake cremoso com calda de morango, framboesa e mirtilo', 24.90, 35, 1, 'ac7ac508e6c114f7d535a02d.jpg', '2026-05-22 12:33:15', '2026-05-24 16:58:40'),
(29, 5, 'Mousse de Maracujá', 'Mousse aerado de maracujá com calda concentrada da fruta', 17.90, 45, 1, '4be4b4a1ebe18096611279a1.jpg', '2026-05-22 12:33:15', '2026-05-24 17:00:15'),
(30, 6, 'Chá Gelado de Pêssego', 'Chá preto gelado com pêssego natural e hortelã, 400ml', 11.90, 10, 1, 'ad1e7d309226829c9de630c5.jpg', '2026-05-22 12:33:15', '2026-05-24 17:03:38');

-- --------------------------------------------------------

--
-- Table structure for table `prato_ingrediente`
--

CREATE TABLE `prato_ingrediente` (
  `prato_id` int(11) NOT NULL,
  `ingrediente_id` int(11) NOT NULL,
  `quantidade` decimal(8,2) DEFAULT 1.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prato_ingrediente`
--

INSERT INTO `prato_ingrediente` (`prato_id`, `ingrediente_id`, `quantidade`) VALUES
(1, 12, 1.00),
(1, 16, 1.00),
(1, 47, 1.00),
(2, 22, 1.00),
(3, 9, 1.00),
(3, 36, 1.00),
(3, 64, 1.00),
(3, 67, 1.00),
(4, 25, 1.00),
(4, 41, 1.00),
(4, 63, 1.00),
(4, 64, 1.00),
(4, 76, 1.00),
(4, 80, 1.00),
(5, 3, 1.00),
(5, 17, 1.00),
(5, 29, 1.00),
(5, 31, 1.00),
(6, 5, 1.00),
(6, 14, 1.00),
(6, 18, 1.00),
(6, 42, 1.00),
(7, 23, 1.00),
(7, 46, 1.00),
(7, 49, 1.00),
(7, 53, 1.00),
(8, 69, 1.00),
(8, 71, 1.00),
(8, 72, 1.00),
(9, 2, 1.00),
(9, 32, 1.00),
(9, 61, 1.00),
(10, 60, 1.00),
(10, 75, 1.00),
(11, 11, 1.00),
(11, 16, 1.00),
(11, 30, 1.00),
(11, 34, 1.00),
(11, 44, 1.00),
(11, 77, 1.00),
(12, 9, 1.00),
(12, 10, 1.00),
(12, 64, 1.00),
(12, 78, 1.00),
(13, 10, 1.00),
(13, 21, 1.00),
(13, 30, 1.00),
(13, 35, 1.00),
(13, 38, 1.00),
(13, 64, 1.00),
(14, 26, 1.00),
(14, 37, 1.00),
(15, 9, 1.00),
(15, 46, 1.00),
(15, 49, 1.00),
(16, 3, 1.00),
(16, 28, 1.00),
(16, 40, 1.00),
(16, 51, 1.00),
(17, 15, 1.00),
(17, 69, 1.00),
(17, 72, 1.00),
(18, 32, 1.00),
(18, 48, 1.00),
(18, 54, 1.00),
(18, 74, 1.00),
(18, 75, 1.00),
(19, 75, 1.00),
(20, 74, 1.00),
(20, 75, 1.00),
(21, 10, 1.00),
(21, 30, 1.00),
(21, 50, 1.00),
(22, 24, 1.00),
(23, 23, 1.00),
(23, 45, 1.00),
(23, 55, 1.00),
(23, 65, 1.00),
(24, 26, 1.00),
(24, 37, 1.00),
(24, 64, 1.00),
(24, 67, 1.00),
(24, 76, 1.00),
(24, 80, 1.00),
(25, 6, 1.00),
(25, 19, 1.00),
(25, 30, 1.00),
(25, 39, 1.00),
(25, 64, 1.00),
(25, 68, 1.00),
(26, 5, 1.00),
(26, 6, 1.00),
(26, 20, 1.00),
(26, 35, 1.00),
(26, 44, 1.00),
(26, 64, 1.00),
(27, 27, 1.00),
(27, 52, 1.00),
(27, 76, 1.00),
(28, 32, 1.00),
(28, 33, 1.00),
(28, 56, 1.00),
(28, 57, 1.00),
(28, 58, 1.00),
(28, 70, 1.00),
(29, 14, 1.00),
(29, 32, 1.00),
(29, 55, 1.00),
(30, 48, 1.00),
(30, 59, 1.00),
(30, 73, 1.00),
(30, 74, 1.00),
(30, 75, 1.00);

-- --------------------------------------------------------

--
-- Table structure for table `promocoes`
--

CREATE TABLE `promocoes` (
  `id` int(11) NOT NULL,
  `prato_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` enum('desconto_percentual','desconto_fixo','combo') NOT NULL DEFAULT 'desconto_percentual',
  `valor` decimal(10,2) NOT NULL COMMENT 'Percentual (%) ou valor fixo (R$)',
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `ativa` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promocoes`
--

INSERT INTO `promocoes` (`id`, `prato_id`, `nome`, `descricao`, `tipo`, `valor`, `data_inicio`, `data_fim`, `ativa`, `created_at`, `updated_at`) VALUES
(5, 4, 'Lula', '', 'desconto_fixo', 0.01, '2026-05-17', '2026-06-30', 1, '2026-05-22 15:01:34', '2026-05-28 14:37:51');

-- --------------------------------------------------------

--
-- Table structure for table `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `horario` time NOT NULL,
  `num_pessoas` int(11) NOT NULL DEFAULT 1,
  `status` enum('pendente','confirmada','cancelada') NOT NULL DEFAULT 'pendente',
  `observacao` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservas`
--

INSERT INTO `reservas` (`id`, `usuario_id`, `data`, `horario`, `num_pessoas`, `status`, `observacao`, `created_at`, `updated_at`) VALUES
(10, 10, '2026-05-22', '22:02:00', 22, 'cancelada', 'a', '2026-05-22 13:23:45', '2026-05-28 14:37:20'),
(12, 10, '2026-05-22', '14:51:00', 2, 'cancelada', 'cancelar', '2026-05-22 13:24:14', '2026-05-22 13:27:45'),
(13, 11, '2026-05-22', '22:22:00', 2, 'confirmada', 'asd', '2026-05-22 13:25:03', '2026-06-01 02:49:04'),
(16, 10, '2026-06-01', '22:02:00', 2, 'pendente', '', '2026-06-01 03:10:20', '2026-06-01 03:10:20');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `tipo` enum('admin','garcom','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `telefone`, `tipo`, `created_at`, `updated_at`) VALUES
(7, 'euadm@menustock.com.br', 'euadm@menustock.com.br', '$2y$10$Zo/9Y3bXuYWMhzxQD918zOcVWviPPW1IFC2xxwi9Yk11nu2nl.Oqm', '', 'admin', '2026-05-11 12:02:09', '2026-05-28 17:03:18'),
(8, 'garçon@garcom.menustock.com.br', 'garcon@garcom.menustock.com.br', '$2y$10$xgxj9YHnw/8RIjEPlHSTHuQ7cpDylPDCarjTya5zYjUNAPOT4avZC', '', 'garcom', '2026-05-11 12:11:53', '2026-05-11 12:11:53'),
(10, 'eu1@gmail.com', 'eu1@gmail.com', '$2y$10$GakzMFvTNc1mJVHoNZ/IxOOhMSQuUaWNJk9x/beu7jbo48tn3WJdq', '', 'user', '2026-05-22 11:29:36', '2026-05-22 11:29:36'),
(11, 'eu2@gmail.com', 'eu2@gmail.com', '$2y$10$gwrsGD001xeNt/E68o4lfuVTQeC.lBRzTHDLcEqK9sWJlbiv2CUK.', '', 'user', '2026-05-31 23:59:33', '2026-05-31 23:59:33');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_pedidos_resumo`
-- (See below for the actual view)
--
CREATE TABLE `vw_pedidos_resumo` (
`id` int(11)
,`status` enum('recebido','preparo','pronto','entregue','cancelado')
,`total` decimal(10,2)
,`obs_geral` text
,`created_at` timestamp
,`cliente` varchar(100)
,`telefone` varchar(20)
,`total_itens` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_promocoes_ativas`
-- (See below for the actual view)
--
CREATE TABLE `vw_promocoes_ativas` (
`promocao_id` int(11)
,`promocao` varchar(100)
,`tipo` enum('desconto_percentual','desconto_fixo','combo')
,`valor` decimal(10,2)
,`prato_id` int(11)
,`prato` varchar(100)
,`preco_original` decimal(10,2)
,`preco_promocional` decimal(20,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_reservas_painel`
-- (See below for the actual view)
--
CREATE TABLE `vw_reservas_painel` (
`id` int(11)
,`data` date
,`horario` time
,`num_pessoas` int(11)
,`status` enum('pendente','confirmada','cancelada')
,`observacao` text
,`cliente` varchar(100)
,`telefone` varchar(20)
);

-- --------------------------------------------------------

--
-- Structure for view `vw_pedidos_resumo`
--
DROP TABLE IF EXISTS `vw_pedidos_resumo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_pedidos_resumo`  AS SELECT `ped`.`id` AS `id`, `ped`.`status` AS `status`, `ped`.`total` AS `total`, `ped`.`obs_geral` AS `obs_geral`, `ped`.`created_at` AS `created_at`, `u`.`nome` AS `cliente`, `u`.`telefone` AS `telefone`, count(`ip`.`id`) AS `total_itens` FROM ((`pedidos` `ped` join `usuarios` `u` on(`u`.`id` = `ped`.`usuario_id`)) join `itens_pedido` `ip` on(`ip`.`pedido_id` = `ped`.`id`)) GROUP BY `ped`.`id`, `ped`.`status`, `ped`.`total`, `ped`.`obs_geral`, `ped`.`created_at`, `u`.`nome`, `u`.`telefone` ORDER BY `ped`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `vw_promocoes_ativas`
--
DROP TABLE IF EXISTS `vw_promocoes_ativas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_promocoes_ativas`  AS SELECT `pr`.`id` AS `promocao_id`, `pr`.`nome` AS `promocao`, `pr`.`tipo` AS `tipo`, `pr`.`valor` AS `valor`, `p`.`id` AS `prato_id`, `p`.`nome` AS `prato`, `p`.`preco` AS `preco_original`, CASE WHEN `pr`.`tipo` = 'desconto_percentual' THEN round(`p`.`preco` - `p`.`preco` * `pr`.`valor` / 100,2) WHEN `pr`.`tipo` = 'desconto_fixo' THEN round(`p`.`preco` - `pr`.`valor`,2) ELSE `p`.`preco` END AS `preco_promocional` FROM (`promocoes` `pr` join `pratos` `p` on(`p`.`id` = `pr`.`prato_id`)) WHERE `pr`.`ativa` = 1 AND curdate() between `pr`.`data_inicio` and `pr`.`data_fim` ;

-- --------------------------------------------------------

--
-- Structure for view `vw_reservas_painel`
--
DROP TABLE IF EXISTS `vw_reservas_painel`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_reservas_painel`  AS SELECT `r`.`id` AS `id`, `r`.`data` AS `data`, `r`.`horario` AS `horario`, `r`.`num_pessoas` AS `num_pessoas`, `r`.`status` AS `status`, `r`.`observacao` AS `observacao`, `u`.`nome` AS `cliente`, `u`.`telefone` AS `telefone` FROM (`reservas` `r` join `usuarios` `u` on(`u`.`id` = `r`.`usuario_id`)) ORDER BY `r`.`data` ASC, `r`.`horario` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carrinho`
--
ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_carrinho_prato` (`prato_id`),
  ADD KEY `idx_carrinho_usuario` (`usuario_id`);

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ingredientes`
--
ALTER TABLE `ingredientes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ip_pedido` (`pedido_id`),
  ADD KEY `fk_ip_prato` (`prato_id`);

--
-- Indexes for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedidos_usuario` (`usuario_id`),
  ADD KEY `idx_pedidos_status` (`status`);

--
-- Indexes for table `pratos`
--
ALTER TABLE `pratos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pratos_categoria` (`categoria_id`),
  ADD KEY `idx_pratos_disponivel` (`disponivel`);

--
-- Indexes for table `prato_ingrediente`
--
ALTER TABLE `prato_ingrediente`
  ADD PRIMARY KEY (`prato_id`,`ingrediente_id`),
  ADD KEY `fk_pi_ingrediente` (`ingrediente_id`);

--
-- Indexes for table `promocoes`
--
ALTER TABLE `promocoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_promo_prato` (`prato_id`),
  ADD KEY `idx_promocoes_datas` (`data_inicio`,`data_fim`,`ativa`);

--
-- Indexes for table `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reservas_data` (`data`,`horario`),
  ADD KEY `idx_reservas_status` (`status`),
  ADD KEY `idx_reservas_usuario` (`usuario_id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carrinho`
--
ALTER TABLE `carrinho`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ingredientes`
--
ALTER TABLE `ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `pratos`
--
ALTER TABLE `pratos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `promocoes`
--
ALTER TABLE `promocoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carrinho`
--
ALTER TABLE `carrinho`
  ADD CONSTRAINT `fk_carrinho_prato` FOREIGN KEY (`prato_id`) REFERENCES `pratos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_carrinho_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `fk_ip_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ip_prato` FOREIGN KEY (`prato_id`) REFERENCES `pratos` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `pratos`
--
ALTER TABLE `pratos`
  ADD CONSTRAINT `fk_pratos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `prato_ingrediente`
--
ALTER TABLE `prato_ingrediente`
  ADD CONSTRAINT `fk_pi_ingrediente` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pi_prato` FOREIGN KEY (`prato_id`) REFERENCES `pratos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `promocoes`
--
ALTER TABLE `promocoes`
  ADD CONSTRAINT `fk_promo_prato` FOREIGN KEY (`prato_id`) REFERENCES `pratos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `fk_reservas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
