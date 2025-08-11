-- Sistema Completo de Configurações Visuais
-- Criado para o projeto Brasil Hilário
-- Controle total sobre fontes, cores, tamanhos e pesos de todas as seções

-- =====================================================
-- CONFIGURAÇÕES PARA HEADER
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
-- Background
('header', 'header', 'cor_background', '#f8f9f4', 'cor', 1),
-- Título do site
('header', 'header', 'fonte_titulo', '"Merriweather", serif', 'fonte', 1),
('header', 'header', 'tamanho_titulo_desktop', '28px', 'texto', 1),
('header', 'header', 'tamanho_titulo_mobile', '24px', 'texto', 1),
('header', 'header', 'peso_titulo', '700', 'texto', 1),
('header', 'header', 'cor_titulo', '#333333', 'cor', 1),
-- Logo
('header', 'header', 'fonte_logo', '"Merriweather", serif', 'fonte', 1),
('header', 'header', 'tamanho_logo_desktop', '24px', 'texto', 1),
('header', 'header', 'tamanho_logo_mobile', '20px', 'texto', 1),
('header', 'header', 'peso_logo', '700', 'texto', 1),
('header', 'header', 'cor_logo', '#0b8103', 'cor', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- CONFIGURAÇÕES PARA NAVBAR (CATEGORIAS)
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
-- Background
('navbar', 'navbar', 'cor_background', '#ffffff', 'cor', 1),
-- Links das categorias
('navbar', 'navbar', 'fonte_links', '"Inter", sans-serif', 'fonte', 1),
('navbar', 'navbar', 'tamanho_links_desktop', '14px', 'texto', 1),
('navbar', 'navbar', 'tamanho_links_mobile', '12px', 'texto', 1),
('navbar', 'navbar', 'peso_links', '500', 'texto', 1),
('navbar', 'navbar', 'cor_links', '#5c5c5c', 'cor', 1),
('navbar', 'navbar', 'cor_links_hover', '#0b8103', 'cor', 1),
-- Setas de navegação
('navbar', 'navbar', 'cor_setas', '#0b8103', 'cor', 1),
('navbar', 'navbar', 'tamanho_setas', '16px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- CONFIGURAÇÕES PARA MAIN (SEÇÃO DE POSTS)
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
-- TÍTULOS DOS POSTS
('main', 'titulos_posts', 'fonte', '"Merriweather", serif', 'fonte', 1),
('main', 'titulos_posts', 'tamanho_desktop', '28px', 'texto', 1),
('main', 'titulos_posts', 'tamanho_mobile', '24px', 'texto', 1),
('main', 'titulos_posts', 'peso', '700', 'texto', 1),
('main', 'titulos_posts', 'cor', '#000000', 'cor', 1),
-- PARÁGRAFOS DOS POSTS
('main', 'paragrafos_posts', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('main', 'paragrafos_posts', 'tamanho_desktop', '16px', 'texto', 1),
('main', 'paragrafos_posts', 'tamanho_mobile', '14px', 'texto', 1),
('main', 'paragrafos_posts', 'peso', '400', 'texto', 1),
('main', 'paragrafos_posts', 'cor', '#333333', 'cor', 1),
-- META INFORMAÇÕES
('main', 'meta_posts', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('main', 'meta_posts', 'tamanho_desktop', '12px', 'texto', 1),
('main', 'meta_posts', 'tamanho_mobile', '10px', 'texto', 1),
('main', 'meta_posts', 'peso', '400', 'texto', 1),
('main', 'meta_posts', 'cor', '#6c757d', 'cor', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- CONFIGURAÇÕES PARA SIDEBAR
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
-- Background geral da sidebar
('sidebar', 'sidebar', 'cor_background', '#f8f9fa', 'cor', 1),
-- TÍTULOS DAS SEÇÕES (Mais Recentes, Categorias, Mais Lidos)
('sidebar', 'titulos_secoes', 'fonte', '"Merriweather", serif', 'fonte', 1),
('sidebar', 'titulos_secoes', 'tamanho_desktop', '18px', 'texto', 1),
('sidebar', 'titulos_secoes', 'tamanho_mobile', '16px', 'texto', 1),
('sidebar', 'titulos_secoes', 'peso', '700', 'texto', 1),
('sidebar', 'titulos_secoes', 'cor', '#000000', 'cor', 1),
-- TÍTULOS DOS CARDS NAS SEÇÕES
('sidebar', 'titulos_cards', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('sidebar', 'titulos_cards', 'tamanho_desktop', '14px', 'texto', 1),
('sidebar', 'titulos_cards', 'tamanho_mobile', '12px', 'texto', 1),
('sidebar', 'titulos_cards', 'peso', '600', 'texto', 1),
('sidebar', 'titulos_cards', 'cor', '#333333', 'cor', 1),
-- Links da sidebar
('sidebar', 'links_sidebar', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('sidebar', 'links_sidebar', 'tamanho_desktop', '14px', 'texto', 1),
('sidebar', 'links_sidebar', 'tamanho_mobile', '12px', 'texto', 1),
('sidebar', 'links_sidebar', 'peso', '500', 'texto', 1),
('sidebar', 'links_sidebar', 'cor', '#0b8103', 'cor', 1),
('sidebar', 'links_sidebar', 'cor_hover', '#0a6b02', 'cor', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- CONFIGURAÇÕES PARA SEÇÃO "LEIA TAMBÉM"
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
-- Background da seção
('leia_tambem', 'leia_tambem', 'cor_background', '#ffffff', 'cor', 1),
-- TÍTULO da seção
('leia_tambem', 'titulo_secao', 'fonte', '"Merriweather", serif', 'fonte', 1),
('leia_tambem', 'titulo_secao', 'tamanho_desktop', '22px', 'texto', 1),
('leia_tambem', 'titulo_secao', 'tamanho_mobile', '20px', 'texto', 1),
('leia_tambem', 'titulo_secao', 'peso', '700', 'texto', 1),
('leia_tambem', 'titulo_secao', 'cor', '#000000', 'cor', 1),
-- TÍTULOS dos posts relacionados
('leia_tambem', 'titulos_posts', 'fonte', '"Merriweather", serif', 'fonte', 1),
('leia_tambem', 'titulos_posts', 'tamanho_desktop', '16px', 'texto', 1),
('leia_tambem', 'titulos_posts', 'tamanho_mobile', '14px', 'texto', 1),
('leia_tambem', 'titulos_posts', 'peso', '600', 'texto', 1),
('leia_tambem', 'titulos_posts', 'cor', '#333333', 'cor', 1),
-- Meta informações
('leia_tambem', 'meta_info', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('leia_tambem', 'meta_info', 'tamanho_desktop', '12px', 'texto', 1),
('leia_tambem', 'meta_info', 'tamanho_mobile', '10px', 'texto', 1),
('leia_tambem', 'meta_info', 'peso', '400', 'texto', 1),
('leia_tambem', 'meta_info', 'cor', '#6c757d', 'cor', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- CONFIGURAÇÕES PARA SEÇÃO "ÚLTIMAS DO PORTAL"
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
-- Background da seção
('ultimas_portal', 'ultimas_portal', 'cor_background', '#ffffff', 'cor', 1),
-- TÍTULO da seção
('ultimas_portal', 'titulo_secao', 'fonte', '"Merriweather", serif', 'fonte', 1),
('ultimas_portal', 'titulo_secao', 'tamanho_desktop', '22px', 'texto', 1),
('ultimas_portal', 'titulo_secao', 'tamanho_mobile', '20px', 'texto', 1),
('ultimas_portal', 'titulo_secao', 'peso', '700', 'texto', 1),
('ultimas_portal', 'titulo_secao', 'cor', '#000000', 'cor', 1),
-- TÍTULOS dos posts
('ultimas_portal', 'titulos_posts', 'fonte', '"Merriweather", serif', 'fonte', 1),
('ultimas_portal', 'titulos_posts', 'tamanho_desktop', '16px', 'texto', 1),
('ultimas_portal', 'titulos_posts', 'tamanho_mobile', '14px', 'texto', 1),
('ultimas_portal', 'titulos_posts', 'peso', '600', 'texto', 1),
('ultimas_portal', 'titulos_posts', 'cor', '#333333', 'cor', 1),
-- TAG DA CATEGORIA
('ultimas_portal', 'tag_categoria', 'cor_background', '#0b8103', 'cor', 1),
('ultimas_portal', 'tag_categoria', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('ultimas_portal', 'tag_categoria', 'tamanho_desktop', '10px', 'texto', 1),
('ultimas_portal', 'tag_categoria', 'tamanho_mobile', '8px', 'texto', 1),
('ultimas_portal', 'tag_categoria', 'peso', '500', 'texto', 1),
('ultimas_portal', 'tag_categoria', 'cor_texto', '#ffffff', 'cor', 1),
('ultimas_portal', 'tag_categoria', 'borda_arredondada', '15px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- CONFIGURAÇÕES PARA FOOTER
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
-- Background do footer
('footer', 'footer', 'cor_background', '#f8f9fa', 'cor', 1),
-- TÍTULOS das seções do footer
('footer', 'titulos_secoes', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('footer', 'titulos_secoes', 'tamanho_desktop', '18px', 'texto', 1),
('footer', 'titulos_secoes', 'tamanho_mobile', '16px', 'texto', 1),
('footer', 'titulos_secoes', 'peso', '700', 'texto', 1),
('footer', 'titulos_secoes', 'cor', '#000000', 'cor', 1),
-- OUTROS TEXTOS do footer
('footer', 'outros_textos', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('footer', 'outros_textos', 'tamanho_desktop', '14px', 'texto', 1),
('footer', 'outros_textos', 'tamanho_mobile', '12px', 'texto', 1),
('footer', 'outros_textos', 'peso', '400', 'texto', 1),
('footer', 'outros_textos', 'cor', '#6c757d', 'cor', 1),
-- Links do footer
('footer', 'links_footer', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('footer', 'links_footer', 'tamanho_desktop', '14px', 'texto', 1),
('footer', 'links_footer', 'tamanho_mobile', '12px', 'texto', 1),
('footer', 'links_footer', 'peso', '400', 'texto', 1),
('footer', 'links_footer', 'cor', '#0b8103', 'cor', 1),
('footer', 'links_footer', 'cor_hover', '#0a6b02', 'cor', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- CONFIGURAÇÕES PARA CARDS E POSTS
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
-- Background dos cards
('cards', 'cards', 'cor_background', '#ffffff', 'cor', 1),
('cards', 'cards', 'cor_borda', '#dee2e6', 'cor', 1),
-- TÍTULOS dos cards
('cards', 'titulos_cards', 'fonte', '"Merriweather", serif', 'fonte', 1),
('cards', 'titulos_cards', 'tamanho_desktop', '20px', 'texto', 1),
('cards', 'titulos_cards', 'tamanho_mobile', '18px', 'texto', 1),
('cards', 'titulos_cards', 'peso', '700', 'texto', 1),
('cards', 'titulos_cards', 'cor', '#000000', 'cor', 1),
-- Texto dos cards
('cards', 'texto_cards', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('cards', 'texto_cards', 'tamanho_desktop', '14px', 'texto', 1),
('cards', 'texto_cards', 'tamanho_mobile', '12px', 'texto', 1),
('cards', 'texto_cards', 'peso', '400', 'texto', 1),
('cards', 'texto_cards', 'cor', '#333333', 'cor', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- CONFIGURAÇÕES PARA BOTÕES
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
-- Botões primários
('botoes', 'botao_primario', 'cor_background', '#0b8103', 'cor', 1),
('botoes', 'botao_primario', 'cor_texto', '#ffffff', 'cor', 1),
('botoes', 'botao_primario', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('botoes', 'botao_primario', 'tamanho_desktop', '14px', 'texto', 1),
('botoes', 'botao_primario', 'tamanho_mobile', '12px', 'texto', 1),
('botoes', 'botao_primario', 'peso', '500', 'texto', 1),
-- Botões secundários
('botoes', 'botao_secundario', 'cor_background', '#6c757d', 'cor', 1),
('botoes', 'botao_secundario', 'cor_texto', '#ffffff', 'cor', 1),
('botoes', 'botao_secundario', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('botoes', 'botao_secundario', 'tamanho_desktop', '14px', 'texto', 1),
('botoes', 'botao_secundario', 'tamanho_mobile', '12px', 'texto', 1),
('botoes', 'botao_secundario', 'peso', '500', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- CONFIGURAÇÕES PARA BADGES E TAGS
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
-- Badges de categoria
('badges', 'badge_categoria', 'cor_background', '#0b8103', 'cor', 1),
('badges', 'badge_categoria', 'cor_texto', '#ffffff', 'cor', 1),
('badges', 'badge_categoria', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('badges', 'badge_categoria', 'tamanho_desktop', '10px', 'texto', 1),
('badges', 'badge_categoria', 'tamanho_mobile', '8px', 'texto', 1),
('badges', 'badge_categoria', 'peso', '500', 'texto', 1),
('badges', 'badge_categoria', 'borda_arredondada', '15px', 'texto', 1),
-- Badges de destaque
('badges', 'badge_destaque', 'cor_background', '#ffc107', 'cor', 1),
('badges', 'badge_destaque', 'cor_texto', '#000000', 'cor', 1),
('badges', 'badge_destaque', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('badges', 'badge_destaque', 'tamanho_desktop', '10px', 'texto', 1),
('badges', 'badge_destaque', 'tamanho_mobile', '8px', 'texto', 1),
('badges', 'badge_destaque', 'peso', '600', 'texto', 1),
('badges', 'badge_destaque', 'borda_arredondada', '15px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- CONFIGURAÇÕES PARA PAGINAÇÃO
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
-- Background da paginação
('paginacao', 'paginacao', 'cor_background', '#ffffff', 'cor', 1),
-- Links da paginação
('paginacao', 'links_paginacao', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('paginacao', 'links_paginacao', 'tamanho_desktop', '14px', 'texto', 1),
('paginacao', 'links_paginacao', 'tamanho_mobile', '12px', 'texto', 1),
('paginacao', 'links_paginacao', 'peso', '400', 'texto', 1),
('paginacao', 'links_paginacao', 'cor', '#007bff', 'cor', 1),
('paginacao', 'links_paginacao', 'cor_hover', '#0056b3', 'cor', 1),
-- Página ativa
('paginacao', 'pagina_ativa', 'cor_background', '#007bff', 'cor', 1),
('paginacao', 'pagina_ativa', 'cor_texto', '#ffffff', 'cor', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- CONFIGURAÇÕES PARA FORMULÁRIOS
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
-- Campos de input
('formularios', 'input_texto', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('formularios', 'input_texto', 'tamanho_desktop', '14px', 'texto', 1),
('formularios', 'input_texto', 'tamanho_mobile', '12px', 'texto', 1),
('formularios', 'input_texto', 'peso', '400', 'texto', 1),
('formularios', 'input_texto', 'cor_texto', '#333333', 'cor', 1),
('formularios', 'input_texto', 'cor_borda', '#ced4da', 'cor', 1),
('formularios', 'input_texto', 'cor_foco', '#0b8103', 'cor', 1),
-- Labels dos formulários
('formularios', 'labels', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('formularios', 'labels', 'tamanho_desktop', '14px', 'texto', 1),
('formularios', 'labels', 'tamanho_mobile', '12px', 'texto', 1),
('formularios', 'labels', 'peso', '500', 'texto', 1),
('formularios', 'labels', 'cor', '#495057', 'cor', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- CONFIGURAÇÕES PARA ANÚNCIOS
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
-- Background dos anúncios
('anuncios', 'anuncios', 'cor_background', '#f8f9fa', 'cor', 1),
-- TÍTULOS dos anúncios
('anuncios', 'titulos_anuncios', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('anuncios', 'titulos_anuncios', 'tamanho_desktop', '14px', 'texto', 1),
('anuncios', 'titulos_anuncios', 'tamanho_mobile', '12px', 'texto', 1),
('anuncios', 'titulos_anuncios', 'peso', '600', 'texto', 1),
('anuncios', 'titulos_anuncios', 'cor', '#333333', 'cor', 1),
-- Badge "Patrocinado"
('anuncios', 'badge_patrocinado', 'cor_background', '#ff6b35', 'cor', 1),
('anuncios', 'badge_patrocinado', 'cor_texto', '#ffffff', 'cor', 1),
('anuncios', 'badge_patrocinado', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('anuncios', 'badge_patrocinado', 'tamanho_desktop', '10px', 'texto', 1),
('anuncios', 'badge_patrocinado', 'tamanho_mobile', '8px', 'texto', 1),
('anuncios', 'badge_patrocinado', 'peso', '600', 'texto', 1),
('anuncios', 'badge_patrocinado', 'borda_arredondada', '10px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- =====================================================
-- CONFIGURAÇÕES PARA RESPONSIVIDADE
-- =====================================================
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
-- Breakpoints
('responsividade', 'breakpoints', 'mobile', '768px', 'texto', 1),
('responsividade', 'breakpoints', 'tablet', '992px', 'texto', 1),
('responsividade', 'breakpoints', 'desktop', '1200px', 'texto', 1),
-- Espaçamentos responsivos
('responsividade', 'espacamentos', 'mobile_padding', '15px', 'texto', 1),
('responsividade', 'espacamentos', 'desktop_padding', '30px', 'texto', 1),
('responsividade', 'espacamentos', 'mobile_margin', '10px', 'texto', 1),
('responsividade', 'espacamentos', 'desktop_margin', '20px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW(); 