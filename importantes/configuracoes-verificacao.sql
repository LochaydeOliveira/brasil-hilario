-- Script de verificação das configurações
-- Execute este script para verificar se os dados foram inseridos corretamente

-- Verificar se a tabela existe
SELECT 'Tabela configuracoes existe' as status, COUNT(*) as total 
FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'configuracoes';

-- Verificar total de configurações
SELECT 'Total de configurações' as status, COUNT(*) as total FROM configuracoes;

-- Listar todas as configurações por grupo
SELECT grupo, COUNT(*) as total FROM configuracoes GROUP BY grupo ORDER BY grupo;

-- Verificar configurações gerais
SELECT chave, valor, tipo FROM configuracoes WHERE grupo = 'geral' ORDER BY chave;

-- Verificar configurações de SEO
SELECT chave, valor, tipo FROM configuracoes WHERE grupo = 'seo' ORDER BY chave;

-- Verificar configurações de redes sociais
SELECT chave, valor, tipo FROM configuracoes WHERE grupo = 'redes_sociais' ORDER BY chave;

-- Verificar configurações de integração
SELECT chave, valor, tipo FROM configuracoes WHERE grupo = 'integracao' ORDER BY chave;

-- Verificar configurações de páginas
SELECT chave, valor, tipo FROM configuracoes WHERE grupo = 'paginas' ORDER BY chave; 