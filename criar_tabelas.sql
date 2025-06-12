CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserir algumas categorias padrão
INSERT INTO categories (name, slug, description) VALUES
('Notícias', 'noticias', 'Notícias gerais'),
('Humor', 'humor', 'Conteúdo humorístico'),
('Política', 'politica', 'Notícias sobre política'),
('Esportes', 'esportes', 'Notícias esportivas'),
('Tecnologia', 'tecnologia', 'Notícias sobre tecnologia'); 