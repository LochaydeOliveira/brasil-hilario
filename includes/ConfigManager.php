<?php
require_once __DIR__ . '/db.php';

class ConfigManager {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database;
    }
    
    public function get($chave, $padrao = null) {
        $chave = $this->conn->real_escape_string($chave);
        $sql = "SELECT valor, tipo FROM configuracoes WHERE chave = '$chave'";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $this->convertValue($row['valor'], $row['tipo']);
        }
        
        return $padrao;
    }
    
    public function set($chave, $valor, $tipo = 'string', $grupo = 'geral') {
        $chave = $this->conn->real_escape_string($chave);
        $valor = $this->conn->real_escape_string($valor);
        $tipo = $this->conn->real_escape_string($tipo);
        $grupo = $this->conn->real_escape_string($grupo);
        
        $check_sql = "SELECT id FROM configuracoes WHERE chave = '$chave'";
        $check_result = $this->conn->query($check_sql);
        
        if ($check_result && $check_result->num_rows > 0) {
            $sql = "UPDATE configuracoes SET valor = '$valor', tipo = '$tipo', grupo = '$grupo', atualizado_em = NOW() WHERE chave = '$chave'";
        } else {
            $sql = "INSERT INTO configuracoes (chave, valor, tipo, grupo, criado_em, atualizado_em) VALUES ('$chave', '$valor', '$tipo', '$grupo', NOW(), NOW())";
        }
        
        return $this->conn->query($sql);
    }
    
    public function getGroup($grupo) {
        $grupo = $this->conn->real_escape_string($grupo);
        $sql = "SELECT chave, valor, tipo FROM configuracoes WHERE grupo = '$grupo' ORDER BY chave";
        $result = $this->conn->query($sql);
        
        $configs = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $configs[$row['chave']] = [
                    'valor' => $this->convertValue($row['valor'], $row['tipo']),
                    'tipo' => $row['tipo']
                ];
            }
        }
        
        return $configs;
    }
    
    public function getAll() {
        $sql = "SELECT chave, valor, tipo, grupo FROM configuracoes ORDER BY grupo, chave";
        $result = $this->conn->query($sql);
        
        $configs = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $configs[$row['chave']] = [
                    'valor' => $this->convertValue($row['valor'], $row['tipo']),
                    'tipo' => $row['tipo'],
                    'grupo' => $row['grupo']
                ];
            }
        }
        
        return $configs;
    }
    
    public function delete($chave) {
        $chave = $this->conn->real_escape_string($chave);
        $sql = "DELETE FROM configuracoes WHERE chave = '$chave'";
        return $this->conn->query($sql);
    }
    
    public function exists($chave) {
        $chave = $this->conn->real_escape_string($chave);
        $sql = "SELECT id FROM configuracoes WHERE chave = '$chave'";
        $result = $this->conn->query($sql);
        return $result && $result->num_rows > 0;
    }
    
    private function convertValue($valor, $tipo) {
        switch ($tipo) {
            case 'boolean':
                return filter_var($valor, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $valor;
            case 'float':
                return (float) $valor;
            case 'array':
                return json_decode($valor, true) ?: [];
            case 'json':
                return json_decode($valor, true) ?: [];
            default:
                return $valor;
        }
    }
} 