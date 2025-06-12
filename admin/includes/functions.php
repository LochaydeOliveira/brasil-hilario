<?php
/**
 * Arquivo: functions.php
 * Descrição: Funções auxiliares do sistema
 * Funcionalidades:
 * - Funções para manipulação de posts
 * - Funções para manipulação de categorias
 * - Funções para manipulação de usuários
 * - Funções para manipulação de mídia
 */

// Função para obter todos os posts
function getAllPosts($conn, $limit = null, $offset = 0) {
    $sql = "SELECT p.*, c.nome as categoria_nome, u.nome as autor_nome 
            FROM posts p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            LEFT JOIN usuarios u ON p.autor_id = u.id 
            ORDER BY p.data_criacao DESC";
    
    if ($limit) {
        $sql .= " LIMIT :limit OFFSET :offset";
    }
    
    $stmt = $conn->prepare($sql);
    
    if ($limit) {
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt->fetchAll();
}

// Função para obter um post específico
function getPost($conn, $id) {
    $sql = "SELECT p.*, c.nome as categoria_nome, u.nome as autor_nome 
            FROM posts p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            LEFT JOIN usuarios u ON p.autor_id = u.id 
            WHERE p.id = :id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch();
}

// Função para criar um novo post
function createPost($conn, $data) {
    $sql = "INSERT INTO posts (titulo, slug, conteudo, resumo, categoria_id, autor_id, status, data_criacao) 
            VALUES (:titulo, :slug, :conteudo, :resumo, :categoria_id, :autor_id, :status, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':titulo', $data['titulo']);
    $stmt->bindParam(':slug', $data['slug']);
    $stmt->bindParam(':conteudo', $data['conteudo']);
    $stmt->bindParam(':resumo', $data['resumo']);
    $stmt->bindParam(':categoria_id', $data['categoria_id'], PDO::PARAM_INT);
    $stmt->bindParam(':autor_id', $data['autor_id'], PDO::PARAM_INT);
    $stmt->bindParam(':status', $data['status']);
    
    return $stmt->execute();
}

// Função para atualizar um post
function updatePost($conn, $id, $data) {
    $sql = "UPDATE posts 
            SET titulo = :titulo, 
                slug = :slug, 
                conteudo = :conteudo, 
                resumo = :resumo, 
                categoria_id = :categoria_id, 
                status = :status, 
                data_atualizacao = NOW() 
            WHERE id = :id";
    
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':titulo', $data['titulo']);
    $stmt->bindParam(':slug', $data['slug']);
    $stmt->bindParam(':conteudo', $data['conteudo']);
    $stmt->bindParam(':resumo', $data['resumo']);
    $stmt->bindParam(':categoria_id', $data['categoria_id'], PDO::PARAM_INT);
    $stmt->bindParam(':status', $data['status']);
    
    return $stmt->execute();
}

// Função para excluir um post
function deletePost($conn, $id) {
    $sql = "DELETE FROM posts WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

// Função para obter todas as categorias
function getAllCategories($conn) {
    $sql = "SELECT * FROM categorias ORDER BY nome";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Função para obter uma categoria específica
function getCategory($conn, $id) {
    $sql = "SELECT * FROM categorias WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch();
}

// Função para criar uma nova categoria
function createCategory($conn, $data) {
    $sql = "INSERT INTO categorias (nome, slug, descricao) 
            VALUES (:nome, :slug, :descricao)";
    
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':nome', $data['nome']);
    $stmt->bindParam(':slug', $data['slug']);
    $stmt->bindParam(':descricao', $data['descricao']);
    
    return $stmt->execute();
}

// Função para atualizar uma categoria
function updateCategory($conn, $id, $data) {
    $sql = "UPDATE categorias 
            SET nome = :nome, 
                slug = :slug, 
                descricao = :descricao 
            WHERE id = :id";
    
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':nome', $data['nome']);
    $stmt->bindParam(':slug', $data['slug']);
    $stmt->bindParam(':descricao', $data['descricao']);
    
    return $stmt->execute();
}

// Função para excluir uma categoria
function deleteCategory($conn, $id) {
    $sql = "DELETE FROM categorias WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

// Função para obter todos os usuários
function getAllUsers($conn) {
    $sql = "SELECT * FROM usuarios ORDER BY nome";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Função para obter um usuário específico
function getUser($conn, $id) {
    $sql = "SELECT * FROM usuarios WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch();
}

// Função para criar um novo usuário
function createUser($conn, $data) {
    $sql = "INSERT INTO usuarios (nome, email, senha, tipo) 
            VALUES (:nome, :email, :senha, :tipo)";
    
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':nome', $data['nome']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':senha', $data['senha']);
    $stmt->bindParam(':tipo', $data['tipo']);
    
    return $stmt->execute();
}

// Função para atualizar um usuário
function updateUser($conn, $id, $data) {
    $sql = "UPDATE usuarios 
            SET nome = :nome, 
                email = :email, 
                tipo = :tipo 
            WHERE id = :id";
    
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':nome', $data['nome']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':tipo', $data['tipo']);
    
    return $stmt->execute();
}

// Função para excluir um usuário
function deleteUser($conn, $id) {
    $sql = "DELETE FROM usuarios WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

// Função para verificar se um slug já existe
function slugExists($conn, $slug, $table, $id = null) {
    $sql = "SELECT COUNT(*) FROM $table WHERE slug = :slug";
    
    if ($id) {
        $sql .= " AND id != :id";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':slug', $slug);
    
    if ($id) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

// Função para obter o total de registros em uma tabela
function getTotalRecords($conn, $table) {
    $sql = "SELECT COUNT(*) FROM $table";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
}

// Função para gerar paginação
function generatePagination($total, $per_page, $current_page) {
    $total_pages = ceil($total / $per_page);
    $pagination = [];
    
    if ($total_pages > 1) {
        $pagination['first'] = 1;
        $pagination['last'] = $total_pages;
        
        if ($current_page > 1) {
            $pagination['prev'] = $current_page - 1;
        }
        
        if ($current_page < $total_pages) {
            $pagination['next'] = $current_page + 1;
        }
        
        $pagination['pages'] = range(
            max(1, $current_page - 2),
            min($total_pages, $current_page + 2)
        );
    }
    
    return $pagination;
} 