-- Adicionar campo editor_type na tabela posts
ALTER TABLE posts ADD COLUMN editor_type ENUM('tinymce', 'markdown') NOT NULL DEFAULT 'tinymce' AFTER conteudo; 