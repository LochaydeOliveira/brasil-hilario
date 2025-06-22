-- Script para criar e popular a tabela de configurações do site
-- Execute este script no seu banco de dados

-- Criar tabela configuracoes se não existir
CREATE TABLE IF NOT EXISTS configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT,
    tipo ENUM('string', 'integer', 'boolean', 'float', 'array', 'json') DEFAULT 'string',
    grupo VARCHAR(50) DEFAULT 'geral',
    descricao TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_grupo (grupo),
    INDEX idx_chave (chave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Limpar dados existentes (opcional - remova se quiser manter dados existentes)
-- DELETE FROM configuracoes;

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