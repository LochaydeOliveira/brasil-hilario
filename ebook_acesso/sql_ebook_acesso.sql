USE ebook_acesso;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ativo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- (Opcional) inserir o admin inicial
-- Substitua HASH_GERADO_PHP pelo retorno de: password_hash('SUA_SENHA_FORTE', PASSWORD_DEFAULT)
-- INSERT INTO usuarios (nome, email, senha, ativo)
-- VALUES ('Lochayde Oliveira', 'lochaydeguerreiro@hotmail.com', 'HASH_GERADO_PHP', 1);


