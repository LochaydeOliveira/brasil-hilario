-- Adicionar configurações de fontes para anúncios e outras seções
-- Criado para o projeto Brasil Hilário

-- Configurações de fontes principais do site
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'site', 'fonte_primaria', '"Merriweather", serif', 'fonte', 1),
('fontes', 'site', 'fonte_secundaria', '"Inter", sans-serif', 'fonte', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- Configurações de fontes para anúncios
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'anuncios', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'anuncios', 'peso', '600', 'texto', 1),
('fontes', 'anuncios', 'tamanho_desktop', '14px', 'texto', 1),
('fontes', 'anuncios', 'tamanho_mobile', '12px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- Configurações de fontes para títulos de conteúdo
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'titulo_conteudo', 'fonte', '"Merriweather", serif', 'fonte', 1),
('fontes', 'titulo_conteudo', 'peso', '700', 'texto', 1),
('fontes', 'titulo_conteudo_h1', 'tamanho_desktop', '32px', 'texto', 1),
('fontes', 'titulo_conteudo_h1', 'tamanho_mobile', '28px', 'texto', 1),
('fontes', 'titulo_conteudo_h2', 'tamanho_desktop', '28px', 'texto', 1),
('fontes', 'titulo_conteudo_h2', 'tamanho_mobile', '24px', 'texto', 1),
('fontes', 'titulo_conteudo_h3', 'tamanho_desktop', '24px', 'texto', 1),
('fontes', 'titulo_conteudo_h3', 'tamanho_mobile', '20px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- Configurações de fontes para navegação
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'navegacao', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'navegacao', 'peso', '500', 'texto', 1),
('fontes', 'navegacao', 'tamanho_desktop', '14px', 'texto', 1),
('fontes', 'navegacao', 'tamanho_mobile', '12px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- Configurações de fontes para sidebar
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'sidebar', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'sidebar', 'peso', '400', 'texto', 1),
('fontes', 'sidebar', 'tamanho_desktop', '14px', 'texto', 1),
('fontes', 'sidebar', 'tamanho_mobile', '12px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- Configurações de fontes para cards
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'cards', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'cards', 'peso', '400', 'texto', 1),
('fontes', 'cards', 'tamanho_desktop', '14px', 'texto', 1),
('fontes', 'cards', 'tamanho_mobile', '12px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- Configurações de fontes para botões
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'botoes', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'botoes', 'peso', '500', 'texto', 1),
('fontes', 'botoes', 'tamanho_desktop', '14px', 'texto', 1),
('fontes', 'botoes', 'tamanho_mobile', '12px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- Configurações de fontes para meta textos
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'meta_textos', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'meta_textos', 'peso', '400', 'texto', 1),
('fontes', 'meta_textos', 'tamanho_desktop', '12px', 'texto', 1),
('fontes', 'meta_textos', 'tamanho_mobile', '10px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- Configurações de fontes para títulos
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'titulos', 'fonte', '"Merriweather", serif', 'fonte', 1),
('fontes', 'titulos', 'peso', '700', 'texto', 1),
('fontes', 'titulos', 'tamanho_desktop', '28px', 'texto', 1),
('fontes', 'titulos', 'tamanho_mobile', '24px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- Configurações de fontes para parágrafos
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'paragrafos', 'fonte', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'paragrafos', 'peso', '400', 'texto', 1),
('fontes', 'paragrafos', 'tamanho_desktop', '16px', 'texto', 1),
('fontes', 'paragrafos', 'tamanho_mobile', '14px', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW();

-- Configurações de fonte geral
INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) VALUES
('fontes', 'site', 'fonte_geral', '"Inter", sans-serif', 'fonte', 1),
('fontes', 'site', 'usar_fonte_geral', '0', 'texto', 1),
('fontes', 'site', 'personalizar_fontes', '1', 'texto', 1)
ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW(); 