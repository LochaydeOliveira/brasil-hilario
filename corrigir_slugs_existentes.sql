-- Script para corrigir slugs existentes no banco de dados
-- Execute este script no seu banco de dados

-- Corrigir slugs de tags problemáticos
UPDATE tags SET slug = 'etica-medica' WHERE slug = 'tica-m-dica';
UPDATE tags SET slug = 'conduta-de-medico' WHERE slug = 'conduta-de-m-dico';
UPDATE tags SET slug = 'repercussoes-legais' WHERE slug = 'repercuss-es-legais';
UPDATE tags SET slug = 'liberdade-de-expressao-responsabilidade' WHERE slug = 'liberdade-de-express-o-responsabilidade';

-- Verificar se há outros slugs problemáticos
-- (Execute esta consulta para ver todas as tags após a correção)
SELECT id, nome, slug, 
       CONCAT('https://www.brasilhilario.com.br/tag/', slug) as url_completa 
FROM tags 
WHERE slug LIKE '%m-d%' OR slug LIKE '%-%' 
ORDER BY nome;

-- Verificar slugs de posts problemáticos
SELECT id, titulo, slug, 
       CONCAT('https://www.brasilhilario.com.br/post/', slug) as url_completa 
FROM posts 
WHERE slug LIKE '%m-d%' OR slug LIKE '%-%' 
ORDER BY titulo;
