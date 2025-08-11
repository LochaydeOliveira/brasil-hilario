-- Configurações Completas de Fontes para Controle Total
-- Criado para o projeto Brasil Hilário

-- =====================================================
-- FONTES PRINCIPAIS DO SITE
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'site', 'fonte_primaria', '"Merriweather", serif', 'fonte', 1),
('fontes', 'site', 'fonte_secundaria', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'site', 'fonte_geral', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'site', 'usar_fonte_geral', '0', 'texto', 1),
('fontes', 'site', 'personalizar_fontes', '1', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- FONTES PARA HEADER E NAVEGAÇÃO
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'header', 'fonte', '"Merriweather", serif', 'fonte', 1),
('fontes', 'header', 'peso', '700', 'texto', 1),
('fontes', 'header', 'tamanho_desktop', '28px', 'texto', 1),
('fontes', 'header', 'tamanho_mobile', '24px', 'texto', 1),

('fontes', 'navegacao', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'navegacao', 'peso', '500', 'texto', 1),
('fontes', 'navegacao', 'tamanho_desktop', '14px', 'texto', 1),
('fontes', 'navegacao', 'tamanho_mobile', '12px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- FONTES PARA SIDEBAR (CONTROLE TOTAL)
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'sidebar', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'sidebar', 'peso', '400', 'texto', 1),
('fontes', 'sidebar', 'tamanho_desktop', '14px', 'texto', 1),
('fontes', 'sidebar', 'tamanho_mobile', '12px', 'texto', 1),

('fontes', 'sidebar_titulo', 'fonte', '"Merriweather", serif', 'fonte', 1),
('fontes', 'sidebar_titulo', 'peso', '700', 'texto', 1),
('fontes', 'sidebar_titulo', 'tamanho_desktop', '18px', 'texto', 1),
('fontes', 'sidebar_titulo', 'tamanho_mobile', '16px', 'texto', 1),

('fontes', 'sidebar_links', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'sidebar_links', 'peso', '700', 'texto', 1),
('fontes', 'sidebar_links', 'tamanho_desktop', '14px', 'texto', 1),
('fontes', 'sidebar_links', 'tamanho_mobile', '12px', 'texto', 1),

('fontes', 'sidebar_widget', 'fonte', '"Merriweather", serif', 'fonte', 1),
('fontes', 'sidebar_widget', 'peso', '600', 'texto', 1),
('fontes', 'sidebar_widget', 'tamanho_desktop', '19px', 'texto', 1),
('fontes', 'sidebar_widget', 'tamanho_mobile', '17px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- FONTES PARA CONTEÚDO PRINCIPAL
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'conteudo', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'conteudo', 'peso', '400', 'texto', 1),
('fontes', 'conteudo', 'tamanho_desktop', '16px', 'texto', 1),
('fontes', 'conteudo', 'tamanho_mobile', '14px', 'texto', 1),

('fontes', 'titulo_conteudo', 'fonte', '"Merriweather", serif', 'fonte', 1),
('fontes', 'titulo_conteudo', 'peso', '700', 'texto', 1),
('fontes', 'titulo_conteudo_h1', 'tamanho_desktop', '32px', 'texto', 1),
('fontes', 'titulo_conteudo_h1', 'tamanho_mobile', '28px', 'texto', 1),
('fontes', 'titulo_conteudo_h2', 'tamanho_desktop', '28px', 'texto', 1),
('fontes', 'titulo_conteudo_h2', 'tamanho_mobile', '24px', 'texto', 1),
('fontes', 'titulo_conteudo_h3', 'tamanho_desktop', '24px', 'texto', 1),
('fontes', 'titulo_conteudo_h3', 'tamanho_mobile', '20px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- FONTES PARA CARDS E POSTS
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'cards', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'cards', 'peso', '400', 'texto', 1),
('fontes', 'cards', 'tamanho_desktop', '14px', 'texto', 1),
('fontes', 'cards', 'tamanho_mobile', '12px', 'texto', 1),

('fontes', 'card_titulo', 'fonte', '"Merriweather", serif', 'fonte', 1),
('fontes', 'card_titulo', 'peso', '700', 'texto', 1),
('fontes', 'card_titulo', 'tamanho_desktop', '20px', 'texto', 1),
('fontes', 'card_titulo', 'tamanho_mobile', '18px', 'texto', 1),

('fontes', 'card_header', 'fonte', '"Merriweather", serif', 'fonte', 1),
('fontes', 'card_header', 'peso', '700', 'texto', 1),
('fontes', 'card_header', 'tamanho_desktop', '22px', 'texto', 1),
('fontes', 'card_header', 'tamanho_mobile', '20px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- FONTES PARA BOTÕES E ELEMENTOS INTERATIVOS
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'botoes', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'botoes', 'peso', '500', 'texto', 1),
('fontes', 'botoes', 'tamanho_desktop', '14px', 'texto', 1),
('fontes', 'botoes', 'tamanho_mobile', '12px', 'texto', 1),

('fontes', 'badges', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'badges', 'peso', '500', 'texto', 1),
('fontes', 'badges', 'tamanho_desktop', '12px', 'texto', 1),
('fontes', 'badges', 'tamanho_mobile', '10px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- FONTES PARA ANÚNCIOS
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'anuncios', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'anuncios', 'peso', '600', 'texto', 1),
('fontes', 'anuncios', 'tamanho_desktop', '14px', 'texto', 1),
('fontes', 'anuncios', 'tamanho_mobile', '12px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- FONTES PARA FOOTER
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'footer', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'footer', 'peso', '400', 'texto', 1),
('fontes', 'footer', 'tamanho_desktop', '14px', 'texto', 1),
('fontes', 'footer', 'tamanho_mobile', '12px', 'texto', 1),

('fontes', 'footer_titulo', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'footer_titulo', 'peso', '700', 'texto', 1),
('fontes', 'footer_titulo', 'tamanho_desktop', '18px', 'texto', 1),
('fontes', 'footer_titulo', 'tamanho_mobile', '16px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- FONTES PARA META TEXTOS E ELEMENTOS PEQUENOS
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'meta_textos', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'meta_textos', 'peso', '400', 'texto', 1),
('fontes', 'meta_textos', 'tamanho_desktop', '12px', 'texto', 1),
('fontes', 'meta_textos', 'tamanho_mobile', '10px', 'texto', 1),

('fontes', 'breadcrumb', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'breadcrumb', 'peso', '400', 'texto', 1),
('fontes', 'breadcrumb', 'tamanho_desktop', '12px', 'texto', 1),
('fontes', 'breadcrumb', 'tamanho_mobile', '10px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- FONTES PARA SEÇÕES ESPECÍFICAS
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'leia_tambem', 'fonte', '"Merriweather", serif', 'fonte', 1),
('fontes', 'leia_tambem', 'peso_titulo', '700', 'texto', 1),
('fontes', 'leia_tambem', 'tamanho_titulo_desktop', '22px', 'texto', 1),
('fontes', 'leia_tambem', 'tamanho_titulo_mobile', '20px', 'texto', 1),
('fontes', 'leia_tambem', 'peso_texto', '600', 'texto', 1),
('fontes', 'leia_tambem', 'tamanho_texto_desktop', '14px', 'texto', 1),
('fontes', 'leia_tambem', 'tamanho_texto_mobile', '12px', 'texto', 1),

('fontes', 'ultimas_portal', 'fonte', '"Merriweather", serif', 'fonte', 1),
('fontes', 'ultimas_portal', 'peso_titulo', '700', 'texto', 1),
('fontes', 'ultimas_portal', 'tamanho_titulo_desktop', '22px', 'texto', 1),
('fontes', 'ultimas_portal', 'tamanho_titulo_mobile', '20px', 'texto', 1),
('fontes', 'ultimas_portal', 'peso_texto', '600', 'texto', 1),
('fontes', 'ultimas_portal', 'tamanho_texto_desktop', '14px', 'texto', 1),
('fontes', 'ultimas_portal', 'tamanho_texto_mobile', '12px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW(); 