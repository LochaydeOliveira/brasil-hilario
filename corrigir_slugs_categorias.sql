-- Script para corrigir slugs das categorias com acentos
-- Execute este script no seu banco de dados

-- Atualizar categoria "Notícia" (ID 22)
UPDATE categorias SET slug = 'noticia' WHERE id = 22;

-- Atualizar categoria "Entretenimento" (ID 21) - tem maiúscula no slug
UPDATE categorias SET slug = 'entretenimento' WHERE id = 21;

-- Verificar se há outras categorias com problemas
-- (Execute esta consulta para ver todas as categorias após a correção)
SELECT id, nome, slug, CONCAT('https://www.brasilhilario.com.br/categoria/', slug) as url_completa 
FROM categorias 
ORDER BY nome;
