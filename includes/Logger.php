<?php

class Logger {
    private $logFile;
    private $logLevel;
    
    const LEVEL_DEBUG = 0;
    const LEVEL_INFO = 1;
    const LEVEL_WARNING = 2;
    const LEVEL_ERROR = 3;
    const LEVEL_CRITICAL = 4;
    
    public function __construct($logFile = null, $logLevel = self::LEVEL_INFO) {
        $this->logFile = $logFile ?? __DIR__ . '/../logs/app.log';
        $this->logLevel = $logLevel;
        
        // Criar diretório de logs se não existir
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    public function debug($message, $context = []) {
        $this->log(self::LEVEL_DEBUG, 'DEBUG', $message, $context);
    }
    
    public function info($message, $context = []) {
        $this->log(self::LEVEL_INFO, 'INFO', $message, $context);
    }
    
    public function warning($message, $context = []) {
        $this->log(self::LEVEL_WARNING, 'WARNING', $message, $context);
    }
    
    public function error($message, $context = []) {
        $this->log(self::LEVEL_ERROR, 'ERROR', $message, $context);
    }
    
    public function critical($message, $context = []) {
        $this->log(self::LEVEL_CRITICAL, 'CRITICAL', $message, $context);
    }
    
    private function log($level, $levelName, $message, $context = []) {
        if ($level < $this->logLevel) {
            return;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $requestUri = $_SERVER['REQUEST_URI'] ?? 'unknown';
        
        $logEntry = [
            'timestamp' => $timestamp,
            'level' => $levelName,
            'message' => $message,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'request_uri' => $requestUri,
            'context' => $context
        ];
        
        $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n";
        
        file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
        
        // Para erros críticos, também enviar para error_log do PHP
        if ($level >= self::LEVEL_ERROR) {
            error_log("BRASIL_HILARIO [{$levelName}]: {$message}");
        }
    }
    
    public function logDatabaseQuery($sql, $params = [], $executionTime = null) {
        $context = [
            'sql' => $sql,
            'params' => $params,
            'execution_time' => $executionTime
        ];
        
        $this->debug('Database query executed', $context);
    }
    
    public function logUserAction($action, $userId = null, $details = []) {
        $context = [
            'action' => $action,
            'user_id' => $userId,
            'details' => $details
        ];
        
        $this->info('User action performed', $context);
    }
    
    public function logAnuncioClick($anuncioId, $postId, $tipoClique) {
        $context = [
            'anuncio_id' => $anuncioId,
            'post_id' => $postId,
            'tipo_clique' => $tipoClique
        ];
        
        $this->info('Anúncio clicked', $context);
    }
} 