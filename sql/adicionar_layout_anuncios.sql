-- Adicionar campo de layout para anúncios
ALTER TABLE anuncios ADD COLUMN layout ENUM('carrossel', 'grade') DEFAULT 'carrossel' AFTER localizacao;

-- Atualizar anúncios existentes para usar carrossel como padrão
UPDATE anuncios SET layout = 'carrossel' WHERE layout IS NULL; 