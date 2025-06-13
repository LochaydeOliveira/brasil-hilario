-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 13/06/2025 às 04:37
-- Versão do servidor: 5.7.23-23
-- Versão do PHP: 8.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `INFORMATION_SCHEMA`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `COLUMNS`
--

CREATE TEMPORARY TABLE `COLUMNS` (
  `TABLE_CATALOG` varchar(512) NOT NULL DEFAULT '',
  `TABLE_SCHEMA` varchar(64) NOT NULL DEFAULT '',
  `TABLE_NAME` varchar(64) NOT NULL DEFAULT '',
  `COLUMN_NAME` varchar(64) NOT NULL DEFAULT '',
  `ORDINAL_POSITION` bigint(21) UNSIGNED NOT NULL DEFAULT '0',
  `COLUMN_DEFAULT` longtext,
  `IS_NULLABLE` varchar(3) NOT NULL DEFAULT '',
  `DATA_TYPE` varchar(64) NOT NULL DEFAULT '',
  `CHARACTER_MAXIMUM_LENGTH` bigint(21) UNSIGNED DEFAULT NULL,
  `CHARACTER_OCTET_LENGTH` bigint(21) UNSIGNED DEFAULT NULL,
  `NUMERIC_PRECISION` bigint(21) UNSIGNED DEFAULT NULL,
  `NUMERIC_SCALE` bigint(21) UNSIGNED DEFAULT NULL,
  `DATETIME_PRECISION` bigint(21) UNSIGNED DEFAULT NULL,
  `CHARACTER_SET_NAME` varchar(32) DEFAULT NULL,
  `COLLATION_NAME` varchar(32) DEFAULT NULL,
  `COLUMN_TYPE` longtext NOT NULL,
  `COLUMN_KEY` varchar(3) NOT NULL DEFAULT '',
  `EXTRA` varchar(30) NOT NULL DEFAULT '',
  `PRIVILEGES` varchar(80) NOT NULL DEFAULT '',
  `COLUMN_COMMENT` varchar(1024) NOT NULL DEFAULT '',
  `GENERATION_EXPRESSION` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `COLUMNS`
--

INSERT INTO `COLUMNS` (`TABLE_NAME`, `COLUMN_NAME`, `COLUMN_TYPE`, `IS_NULLABLE`, `COLUMN_KEY`, `COLUMN_DEFAULT`, `EXTRA`) VALUES
('adsense_metrics', 'id', 'int(11)', 'NO', 'PRI', NULL, 'auto_increment'),
('adsense_metrics', 'post_id', 'int(11)', 'NO', 'MUL', NULL, ''),
('adsense_metrics', 'impressoes', 'int(11)', 'NO', '', '0', ''),
('adsense_metrics', 'cliques', 'int(11)', 'NO', '', '0', ''),
('adsense_metrics', 'receita', 'decimal(10,2)', 'NO', '', '0.00', ''),
('adsense_metrics', 'data', 'date', 'NO', '', NULL, ''),
('categorias', 'id', 'int(11)', 'NO', 'PRI', NULL, 'auto_increment'),
('categorias', 'nome', 'varchar(100)', 'NO', '', NULL, ''),
('categorias', 'slug', 'varchar(100)', 'NO', 'UNI', NULL, ''),
('categorias', 'criada_em', 'datetime', 'YES', '', 'CURRENT_TIMESTAMP', ''),
('comentarios', 'id', 'int(11)', 'NO', 'PRI', NULL, 'auto_increment'),
('comentarios', 'post_id', 'int(11)', 'NO', 'MUL', NULL, ''),
('comentarios', 'nome', 'varchar(100)', 'NO', '', NULL, ''),
('comentarios', 'email', 'varchar(100)', 'NO', '', NULL, ''),
('comentarios', 'comentario', 'text', 'NO', '', NULL, ''),
('comentarios', 'aprovado', 'tinyint(1)', 'NO', '', '0', ''),
('comentarios', 'criado_em', 'datetime', 'YES', '', 'CURRENT_TIMESTAMP', ''),
('configuracoes', 'id', 'int(11)', 'NO', 'PRI', NULL, 'auto_increment'),
('configuracoes', 'chave', 'varchar(50)', 'NO', 'UNI', NULL, ''),
('configuracoes', 'valor', 'text', 'NO', '', NULL, ''),
('configuracoes', 'tipo', 'enum(\'string\',\'integer\',\'boolean\',\'json\')', 'NO', '', 'string', ''),
('configuracoes', 'grupo', 'varchar(50)', 'NO', '', 'geral', ''),
('configuracoes', 'atualizado_em', 'datetime', 'YES', '', 'CURRENT_TIMESTAMP', 'on update CURRENT_TIMESTAMP'),
('mensagens', 'id', 'int(11)', 'NO', 'PRI', NULL, 'auto_increment'),
('mensagens', 'nome', 'varchar(100)', 'NO', '', NULL, ''),
('mensagens', 'email', 'varchar(100)', 'NO', '', NULL, ''),
('mensagens', 'assunto', 'varchar(200)', 'NO', '', NULL, ''),
('mensagens', 'mensagem', 'text', 'NO', '', NULL, ''),
('mensagens', 'data_envio', 'datetime', 'NO', '', NULL, ''),
('mensagens', 'lida', 'tinyint(1)', 'NO', '', '0', ''),
('metricas_performance', 'id', 'int(11)', 'NO', 'PRI', NULL, 'auto_increment'),
('metricas_performance', 'post_id', 'int(11)', 'NO', 'MUL', NULL, ''),
('metricas_performance', 'tempo_carregamento', 'float', 'YES', '', NULL, ''),
('metricas_performance', 'tamanho_pagina', 'int(11)', 'YES', '', NULL, ''),
('metricas_performance', 'taxa_rejeicao', 'float', 'YES', '', NULL, ''),
('metricas_performance', 'tempo_medio_sessao', 'int(11)', 'YES', '', NULL, ''),
('metricas_performance', 'data', 'date', 'NO', '', NULL, ''),
('newsletter', 'id', 'int(11)', 'NO', 'PRI', NULL, 'auto_increment'),
('newsletter', 'email', 'varchar(100)', 'NO', 'UNI', NULL, ''),
('newsletter', 'status', 'enum(\'ativo\',\'inativo\')', 'NO', '', 'ativo', ''),
('newsletter', 'token_confirmacao', 'varchar(100)', 'YES', '', NULL, ''),
('newsletter', 'confirmado', 'tinyint(1)', 'NO', '', '0', ''),
('newsletter', 'criado_em', 'datetime', 'YES', '', 'CURRENT_TIMESTAMP', ''),
('posts', 'id', 'int(11)', 'NO', 'PRI', NULL, 'auto_increment'),
('posts', 'titulo', 'varchar(255)', 'NO', '', NULL, ''),
('posts', 'resumo', 'text', 'YES', '', NULL, ''),
('posts', 'slug', 'varchar(255)', 'NO', 'UNI', NULL, ''),
('posts', 'conteudo', 'longtext', 'NO', '', NULL, ''),
('posts', 'editor_type', 'enum(\'tinymce\',\'markdown\')', 'NO', '', 'tinymce', ''),
('posts', 'data_publicacao', 'datetime', 'NO', '', 'CURRENT_TIMESTAMP', ''),
('posts', 'publicado', 'tinyint(1)', 'NO', '', '1', ''),
('posts', 'imagem_destacada', 'varchar(255)', 'YES', '', NULL, ''),
('posts', 'visualizacoes', 'int(11)', 'NO', '', '0', ''),
('posts', 'categoria_id', 'int(11)', 'NO', 'MUL', NULL, ''),
('posts', 'autor_id', 'int(11)', 'YES', '', NULL, ''),
('posts', 'criado_em', 'datetime', 'YES', '', 'CURRENT_TIMESTAMP', ''),
('posts', 'atualizado_em', 'datetime', 'YES', '', 'CURRENT_TIMESTAMP', 'on update CURRENT_TIMESTAMP'),
('posts_tags', 'post_id', 'int(11)', 'NO', 'PRI', NULL, ''),
('posts_tags', 'tag_id', 'int(11)', 'NO', 'PRI', NULL, ''),
('post_tags', 'post_id', 'int(11)', 'NO', 'PRI', NULL, ''),
('post_tags', 'tag_id', 'int(11)', 'NO', 'PRI', NULL, ''),
('tags', 'id', 'int(11)', 'NO', 'PRI', NULL, 'auto_increment'),
('tags', 'nome', 'varchar(50)', 'NO', '', NULL, ''),
('tags', 'slug', 'varchar(50)', 'NO', 'UNI', NULL, ''),
('tags', 'criada_em', 'datetime', 'YES', '', 'CURRENT_TIMESTAMP', ''),
('usuarios', 'id', 'int(11)', 'NO', 'PRI', NULL, 'auto_increment'),
('usuarios', 'nome', 'varchar(100)', 'YES', '', NULL, ''),
('usuarios', 'email', 'varchar(100)', 'YES', 'UNI', NULL, ''),
('usuarios', 'senha', 'varchar(255)', 'YES', '', NULL, ''),
('usuarios', 'tipo', 'enum(\'admin\',\'editor\',\'autor\')', 'NO', '', 'autor', ''),
('usuarios', 'avatar', 'varchar(255)', 'YES', '', NULL, ''),
('usuarios', 'biografia', 'text', 'YES', '', NULL, ''),
('usuarios', 'ultimo_login', 'datetime', 'YES', '', NULL, ''),
('usuarios', 'status', 'enum(\'ativo\',\'inativo\',\'bloqueado\')', 'NO', '', 'ativo', ''),
('usuarios', 'criado_em', 'datetime', 'YES', '', 'CURRENT_TIMESTAMP', '');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
