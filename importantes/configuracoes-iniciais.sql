-- Script para inserir configurações iniciais do site
-- Execute este script no seu banco de dados para popular a tabela configuracoes

-- Configurações Gerais
INSERT INTO configuracoes (chave, valor, tipo, grupo) VALUES
('site_titulo', 'Brasil Hilário', 'string', 'geral'),
('site_descricao', 'O melhor do humor brasileiro', 'string', 'geral'),
('site_url', 'https://brasilhilario.com.br', 'string', 'geral'),
('admin_email', 'admin@brasilhilario.com.br', 'string', 'geral'),
('posts_por_pagina', '10', 'integer', 'geral'),
('comentarios_ativos', '1', 'boolean', 'geral');

-- Configurações de Aparência
INSERT INTO configuracoes (chave, valor, tipo, grupo) VALUES
('cor_primaria', '#0b8103', 'string', 'aparencia'),
('cor_secundaria', '#b30606', 'string', 'aparencia'),
('logo_url', 'assets/images/logo-brasil-hilario-quadrada-svg.svg', 'string', 'aparencia'),
('favicon_url', 'assets/images/favicon.ico', 'string', 'aparencia');

-- Configurações de SEO
INSERT INTO configuracoes (chave, valor, tipo, grupo) VALUES
('meta_keywords', 'humor, brasileiro, piadas, memes, comédia', 'string', 'seo'),
('og_image_padrao', 'assets/images/og-image-default.jpg', 'string', 'seo'),
('google_analytics_id', '', 'string', 'seo'),
('google_search_console', '', 'string', 'seo');

-- Configurações de Redes Sociais
INSERT INTO configuracoes (chave, valor, tipo, grupo) VALUES
('facebook_url', '', 'string', 'redes_sociais'),
('instagram_url', '', 'string', 'redes_sociais'),
('twitter_url', '', 'string', 'redes_sociais'),
('youtube_url', '', 'string', 'redes_sociais'),
('tiktok_url', '', 'string', 'redes_sociais');

-- Configurações de Integração
INSERT INTO configuracoes (chave, valor, tipo, grupo) VALUES
('codigo_head', '', 'string', 'integracao'),
('codigo_body', '', 'string', 'integracao'),
('adsense_client_id', '', 'string', 'integracao'),
('adsense_slot_header', '', 'string', 'integracao'),
('adsense_slot_sidebar', '', 'string', 'integracao'),
('adsense_slot_content', '', 'string', 'integracao');

-- Configurações de Páginas
INSERT INTO configuracoes (chave, valor, tipo, grupo) VALUES
('pagina_sobre_titulo', 'Sobre Nós', 'string', 'paginas'),
('pagina_sobre_url', 'sobre', 'string', 'paginas'),
('pagina_contato_titulo', 'Contato', 'string', 'paginas'),
('pagina_contato_url', 'contato', 'string', 'paginas'),
('pagina_privacidade_titulo', 'Política de Privacidade', 'string', 'paginas'),
('pagina_privacidade_url', 'privacidade', 'string', 'paginas'),
('pagina_termos_titulo', 'Termos de Uso', 'string', 'paginas'),
('pagina_termos_url', 'termos', 'string', 'paginas');

-- Configurações de Newsletter
INSERT INTO configuracoes (chave, valor, tipo, grupo) VALUES
('newsletter_ativa', '1', 'boolean', 'newsletter'),
('newsletter_titulo', 'Inscreva-se na Newsletter', 'string', 'newsletter'),
('newsletter_descricao', 'Receba as melhores piadas e memes diretamente no seu email!', 'string', 'newsletter');

-- Configurações de Performance
INSERT INTO configuracoes (chave, valor, tipo, grupo) VALUES
('cache_ativa', '1', 'boolean', 'performance'),
('cache_tempo', '3600', 'integer', 'performance'),
('compressao_ativa', '1', 'boolean', 'performance'),
('lazy_loading', '1', 'boolean', 'performance'); 