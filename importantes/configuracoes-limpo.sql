-- Script limpo para inserir configurações
-- Execute este script se precisar recriar os dados

-- Limpar dados existentes
DELETE FROM configuracoes;

-- Configurações Gerais
INSERT INTO configuracoes (chave, valor, tipo, grupo) VALUES
('site_title', 'Brasil Hilário', 'string', 'geral'),
('site_description', 'O melhor do humor brasileiro', 'string', 'geral'),
('site_url', 'https://brasilhilario.com.br', 'string', 'geral'),
('admin_email', 'admin@brasilhilario.com.br', 'string', 'geral'),
('posts_per_page', '10', 'integer', 'geral'),
('comments_active', '1', 'boolean', 'geral'),
('primary_color', '#0b8103', 'string', 'geral'),
('secondary_color', '#b30606', 'string', 'geral'),
('logo_url', 'assets/images/logo-brasil-hilario-quadrada-svg.svg', 'string', 'geral'),
('favicon_url', 'assets/images/favicon.ico', 'string', 'geral');

-- Configurações de SEO
INSERT INTO configuracoes (chave, valor, tipo, grupo) VALUES
('meta_keywords', 'humor, brasileiro, piadas, memes, comédia', 'string', 'seo'),
('og_image_default', 'assets/images/og-image-default.jpg', 'string', 'seo'),
('google_analytics_id', '', 'string', 'seo');

-- Configurações de Redes Sociais
INSERT INTO configuracoes (chave, valor, tipo, grupo) VALUES
('facebook_url', '', 'string', 'redes_sociais'),
('instagram_url', '', 'string', 'redes_sociais'),
('twitter_url', '', 'string', 'redes_sociais'),
('youtube_url', '', 'string', 'redes_sociais'),
('tiktok_url', '', 'string', 'redes_sociais'),
('telegram_url', '', 'string', 'redes_sociais');

-- Configurações de Integração
INSERT INTO configuracoes (chave, valor, tipo, grupo) VALUES
('head_code', '', 'string', 'integracao'),
('body_code', '', 'string', 'integracao'),
('adsense_code', '', 'string', 'integracao');

-- Configurações de Páginas
INSERT INTO configuracoes (chave, valor, tipo, grupo) VALUES
('newsletter_active', '1', 'boolean', 'paginas'),
('newsletter_title', 'Inscreva-se na Newsletter', 'string', 'paginas'),
('newsletter_description', 'Receba as melhores piadas e memes diretamente no seu email!', 'string', 'paginas'),
('about_page_title', 'Sobre Nós', 'string', 'paginas'),
('contact_page_title', 'Entre em Contato', 'string', 'paginas'); 