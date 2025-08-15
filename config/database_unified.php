<?php
/**
 * CONEXÃO UNIFICADA E OTIMIZADA DO BANCO DE DADOS
 * Brasil Hilário - Sistema de Anúncios Nativos
 * 
 * Este arquivo centraliza todas as conexões e configurações do banco de dados
 * para evitar duplicações e melhorar a performance.
 */

// Configurações do Banco de Dados
define('DB_HOST_LOCAL', 'localhost');
define('DB_HOST_IP', '192.185.222.27');
define('DB_NAME', 'paymen58_brasil_hilario');
define('DB_USER', 'paymen58');
define('DB_PASS', 'u4q7+B6ly)obP_gxN9sNe');

// Configurações de Performance
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');

// Configurações de Timeout
define('DB_TIMEOUT', 30);
define('DB_RETRY_ATTEMPTS', 3);

// Configurações de Cache
define('DB_CACHE_ENABLED', true);
define('DB_CACHE_TIME', 3600); // 1 hora

/**
 * Classe para gerenciar conexões com o banco de dados
 */
class DatabaseManager {
    private static $instance = null;
    private $pdo = null;
    private $connection_time = null;
    private $last_query_time = null;
    
    /**
     * Construtor privado (Singleton)
     */
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Obter instância única
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obter conexão PDO
     */
    public function getConnection() {
        // Verificar se a conexão ainda está ativa
        if ($this->pdo && $this->isConnectionActive()) {
            return $this->pdo;
        }
        
        // Reconectar se necessário
        $this->connect();
        return $this->pdo;
    }
    
    /**
     * Estabelecer conexão com o banco
     */
    private function connect() {
        $dsnLocal = "mysql:host=" . DB_HOST_LOCAL . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $dsnIP = "mysql:host=" . DB_HOST_IP . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => true, // Conexões persistentes
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
            PDO::ATTR_TIMEOUT => DB_TIMEOUT,
        ];
        
        $attempts = 0;
        $lastException = null;
        
        while ($attempts < DB_RETRY_ATTEMPTS) {
            try {
                // Tentar localhost primeiro
                $this->pdo = new PDO($dsnLocal, DB_USER, DB_PASS, $options);
                $this->pdo->query('SELECT 1');
                $this->connection_time = time();
                
                // Configurar variáveis de sessão para performance
                $this->configureSession();
                
                error_log("✅ Conexão com banco estabelecida via localhost");
                return;
                
            } catch (PDOException $eLocal) {
                $lastException = $eLocal;
                
                try {
                    // Tentar IP se localhost falhar
                    $this->pdo = new PDO($dsnIP, DB_USER, DB_PASS, $options);
                    $this->pdo->query('SELECT 1');
                    $this->connection_time = time();
                    
                    // Configurar variáveis de sessão para performance
                    $this->configureSession();
                    
                    error_log("✅ Conexão com banco estabelecida via IP");
                    return;
                    
                } catch (PDOException $eIP) {
                    $lastException = $eIP;
                    $attempts++;
                    
                    if ($attempts < DB_RETRY_ATTEMPTS) {
                        error_log("⚠️ Tentativa $attempts falhou, tentando novamente em 2 segundos...");
                        sleep(2);
                    }
                }
            }
        }
        
        // Todas as tentativas falharam
        error_log("❌ Erro na conexão com o banco de dados (localhost): " . $eLocal->getMessage());
        error_log("❌ Erro na conexão com o banco de dados (IP): " . $eIP->getMessage());
        
        throw new Exception("Erro na conexão com o banco de dados após $attempts tentativas. Por favor, tente novamente mais tarde.");
    }
    
    /**
     * Configurar variáveis de sessão para melhor performance
     */
    private function configureSession() {
        $queries = [
            "SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'",
            "SET SESSION innodb_lock_wait_timeout = 50",
            "SET SESSION innodb_flush_log_at_trx_commit = 2"
        ];
        
        foreach ($queries as $query) {
            try {
                $this->pdo->exec($query);
            } catch (Exception $e) {
                // Ignorar erros de configuração (algumas podem não ser suportadas)
                error_log("⚠️ Erro ao configurar sessão: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Verificar se a conexão está ativa
     */
    private function isConnectionActive() {
        try {
            $this->pdo->query('SELECT 1');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Executar query com cache
     */
    public function query($sql, $params = [], $cache = false, $cache_time = null) {
        $start_time = microtime(true);
        
        try {
            $connection = $this->getConnection();
            
            // Verificar cache se habilitado
            if ($cache && DB_CACHE_ENABLED) {
                $cache_key = $this->generateCacheKey($sql, $params);
                $cached_result = $this->getCache($cache_key);
                
                if ($cached_result !== false) {
                    $this->last_query_time = microtime(true) - $start_time;
                    return $cached_result;
                }
            }
            
            // Executar query
            $stmt = $connection->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll();
            
            // Salvar no cache se habilitado
            if ($cache && DB_CACHE_ENABLED) {
                $cache_key = $this->generateCacheKey($sql, $params);
                $this->setCache($cache_key, $result, $cache_time ?: DB_CACHE_TIME);
            }
            
            $this->last_query_time = microtime(true) - $start_time;
            return $result;
            
        } catch (Exception $e) {
            error_log("❌ Erro na query: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Params: " . json_encode($params));
            throw $e;
        }
    }
    
    /**
     * Executar query única (fetch)
     */
    public function queryOne($sql, $params = [], $cache = false, $cache_time = null) {
        $result = $this->query($sql, $params, $cache, $cache_time);
        return $result ? $result[0] : null;
    }
    
    /**
     * Executar query de inserção/atualização
     */
    public function execute($sql, $params = []) {
        $start_time = microtime(true);
        
        try {
            $connection = $this->getConnection();
            $stmt = $connection->prepare($sql);
            $result = $stmt->execute($params);
            
            $this->last_query_time = microtime(true) - $start_time;
            return $result;
            
        } catch (Exception $e) {
            error_log("❌ Erro na execução: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Params: " . json_encode($params));
            throw $e;
        }
    }
    
    /**
     * Obter último ID inserido
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Iniciar transação
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Commit transação
     */
    public function commit() {
        return $this->pdo->commit();
    }
    
    /**
     * Rollback transação
     */
    public function rollback() {
        return $this->pdo->rollback();
    }
    
    /**
     * Gerar chave de cache
     */
    private function generateCacheKey($sql, $params) {
        return 'db_' . md5($sql . json_encode($params));
    }
    
    /**
     * Obter valor do cache
     */
    private function getCache($key) {
        try {
            $sql = "SELECT valor, expiracao FROM cache_anuncios WHERE chave = ? AND expiracao > NOW()";
            $result = $this->queryOne($sql, [$key]);
            
            if ($result) {
                return json_decode($result['valor'], true);
            }
            
            return false;
        } catch (Exception $e) {
            // Se a tabela de cache não existir, retornar false
            return false;
        }
    }
    
    /**
     * Salvar valor no cache
     */
    private function setCache($key, $value, $expiration = 3600) {
        try {
            $sql = "INSERT INTO cache_anuncios (chave, valor, expiracao) VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE valor = VALUES(valor), expiracao = VALUES(expiracao)";
            
            $this->execute($sql, [
                $key,
                json_encode($value),
                date('Y-m-d H:i:s', time() + $expiration)
            ]);
            
            return true;
        } catch (Exception $e) {
            // Se a tabela de cache não existir, ignorar
            return false;
        }
    }
    
    /**
     * Obter estatísticas de performance
     */
    public function getPerformanceStats() {
        return [
            'connection_time' => $this->connection_time,
            'last_query_time' => $this->last_query_time,
            'uptime' => $this->connection_time ? time() - $this->connection_time : 0
        ];
    }
    
    /**
     * Limpar cache
     */
    public function clearCache() {
        try {
            $this->execute("DELETE FROM cache_anuncios WHERE expiracao < NOW()");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

// Inicializar conexão global
try {
    $dbManager = DatabaseManager::getInstance();
    $pdo = $dbManager->getConnection();
    
    // Definir variável global para compatibilidade
    if (!isset($GLOBALS['pdo'])) {
        $GLOBALS['pdo'] = $pdo;
    }
    
} catch (Exception $e) {
    error_log("❌ Erro fatal na inicialização do banco: " . $e->getMessage());
    
    // Em produção, mostrar página de erro amigável
    if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
        http_response_code(503);
        include __DIR__ . '/../includes/error-database.php';
        exit;
    } else {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
}

// Função helper para obter conexão
function getDB() {
    return DatabaseManager::getInstance()->getConnection();
}

// Função helper para executar query
function dbQuery($sql, $params = [], $cache = false) {
    return DatabaseManager::getInstance()->query($sql, $params, $cache);
}

// Função helper para executar query única
function dbQueryOne($sql, $params = [], $cache = false) {
    return DatabaseManager::getInstance()->queryOne($sql, $params, $cache);
}

// Função helper para executar comando
function dbExecute($sql, $params = []) {
    return DatabaseManager::getInstance()->execute($sql, $params);
}
