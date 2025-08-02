<?php

class GruposAnunciosManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Buscar grupos de anúncios por localização
     */
    public function getGruposPorLocalizacao($localizacao) {
        $sql = "SELECT g.*, COUNT(gi.anuncio_id) as total_anuncios
                FROM grupos_anuncios g 
                LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
                WHERE g.localizacao = ? AND g.ativo = 1
                GROUP BY g.id 
                ORDER BY g.criado_em DESC";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$localizacao]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar grupos de anúncios: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Buscar anúncios de um grupo específico
     */
    public function getAnunciosDoGrupo($grupoId) {
        $sql = "SELECT a.*, gi.ordem
                FROM anuncios a 
                JOIN grupos_anuncios_items gi ON a.id = gi.anuncio_id
                WHERE gi.grupo_id = ? AND a.ativo = 1
                ORDER BY gi.ordem ASC";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$grupoId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar anúncios do grupo: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Criar novo grupo de anúncios
     */
    public function criarGrupo($dados) {
        $sql = "INSERT INTO grupos_anuncios (nome, localizacao, layout) VALUES (?, ?, ?)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $dados['nome'],
                $dados['localizacao'],
                $dados['layout'] ?? 'carrossel'
            ]);
            
            $grupoId = $this->pdo->lastInsertId();
            
            // Associar anúncios ao grupo
            if (!empty($dados['anuncios'])) {
                $this->associarAnunciosAoGrupo($grupoId, $dados['anuncios']);
            }
            
            return $grupoId;
        } catch (Exception $e) {
            error_log("Erro ao criar grupo de anúncios: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Associar anúncios ao grupo
     */
    private function associarAnunciosAoGrupo($grupoId, $anunciosIds) {
        $sql = "INSERT INTO grupos_anuncios_items (grupo_id, anuncio_id, ordem) VALUES (?, ?, ?)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($anunciosIds as $ordem => $anuncioId) {
                $stmt->execute([$grupoId, $anuncioId, $ordem]);
            }
            return true;
        } catch (Exception $e) {
            error_log("Erro ao associar anúncios ao grupo: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Buscar todos os grupos com estatísticas
     */
    public function getAllGruposComStats() {
        $sql = "SELECT g.*, COUNT(gi.anuncio_id) as total_anuncios
                FROM grupos_anuncios g 
                LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
                GROUP BY g.id 
                ORDER BY g.criado_em DESC";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar grupos com stats: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Buscar grupo por ID
     */
    public function getGrupo($id) {
        $sql = "SELECT g.*
                FROM grupos_anuncios g 
                WHERE g.id = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $grupo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$grupo) {
                return false;
            }
            
            return $grupo;
        } catch (Exception $e) {
            error_log("Erro ao buscar grupo: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Atualizar grupo
     */
    public function atualizarGrupo($id, $dados) {
        $sql = "UPDATE grupos_anuncios SET 
                nome = ?, localizacao = ?, layout = ?, ativo = ?
                WHERE id = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $dados['nome'],
                $dados['localizacao'],
                $dados['layout'] ?? 'carrossel',
                $dados['ativo'] ?? true,
                $id
            ]);
            
            // Atualizar associações com anúncios
            if (isset($dados['anuncios'])) {
                $this->removerAssociacoesGrupo($id);
                if (!empty($dados['anuncios'])) {
                    $this->associarAnunciosAoGrupo($id, $dados['anuncios']);
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Erro ao atualizar grupo: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remover associações do grupo
     */
    private function removerAssociacoesGrupo($grupoId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM grupos_anuncios_items WHERE grupo_id = ?");
            return $stmt->execute([$grupoId]);
        } catch (Exception $e) {
            error_log("Erro ao remover associações do grupo: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Excluir grupo
     */
    public function excluirGrupo($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM grupos_anuncios WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log("Erro ao excluir grupo: " . $e->getMessage());
            return false;
        }
    }
}
?> 