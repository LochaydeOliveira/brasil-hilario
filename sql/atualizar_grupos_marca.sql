-- Adicionar coluna marca aos grupos de anúncios
ALTER TABLE grupos_anuncios 
ADD COLUMN marca ENUM('', 'shopee', 'amazon') DEFAULT '' AFTER layout;

-- Comentário sobre os valores:
-- '' = Vazio (infoproduto)
-- 'shopee' = Produtos da Shopee
-- 'amazon' = Produtos da Amazon 