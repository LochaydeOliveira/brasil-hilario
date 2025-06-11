-- Adicionar campo editor_type na tabela posts
ALTER TABLE posts ADD COLUMN editor_type ENUM('tinymce', 'markdown') NOT NULL DEFAULT 'tinymce' AFTER conteudo;

-- Atualizar estrutura da tabela posts
ALTER TABLE posts
ADD COLUMN IF NOT EXISTS criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS autor_id INT,
ADD COLUMN IF NOT EXISTS visualizacoes INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS editor_type VARCHAR(20) DEFAULT 'tinymce';

-- Verificar e adicionar colunas se não existirem
SET @dbname = DATABASE();
SET @tablename = "posts";
SET @columnname = "publicado";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 1",
  "ALTER TABLE posts ADD COLUMN publicado TINYINT(1) DEFAULT 0"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Atualizar posts existentes
UPDATE posts SET publicado = 1 WHERE status = 'publicado';
UPDATE posts SET publicado = 0 WHERE status = 'rascunho';

-- Remover coluna status antiga se existir
SET @columnname = "status";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  "ALTER TABLE posts DROP COLUMN status",
  "SELECT 1"
));
PREPARE dropIfExists FROM @preparedStatement;
EXECUTE dropIfExists;
DEALLOCATE PREPARE dropIfExists; 