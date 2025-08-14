<?php

class CacheManager {
    private $cacheDir;
    private $defaultTtl;
    private $logger;
    
    public function __construct($cacheDir = null, $defaultTtl = 3600) {
        $this->cacheDir = $cacheDir ?? __DIR__ . '/../cache';
        $this->defaultTtl = $defaultTtl;
        $this->logger = new Logger();
        
        // Criar diretório de cache se não existir
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    /**
     * Gerar chave de cache baseada em parâmetros
     */
    private function generateKey($key, $params = []) {
        $hash = md5($key . serialize($params));
        return $hash . '.cache';
    }
    
    /**
     * Obter caminho completo do arquivo de cache
     */
    private function getCachePath($key, $params = []) {
        $filename = $this->generateKey($key, $params);
        return $this->cacheDir . '/' . $filename;
    }
    
    /**
     * Verificar se cache existe e é válido
     */
    public function has($key, $params = []) {
        $cachePath = $this->getCachePath($key, $params);
        
        if (!file_exists($cachePath)) {
            return false;
        }
        
        $cacheData = $this->readCacheFile($cachePath);
        if (!$cacheData) {
            return false;
        }
        
        // Verificar se expirou
        if (time() > $cacheData['expires']) {
            $this->delete($key, $params);
            return false;
        }
        
        return true;
    }
    
    /**
     * Obter dados do cache
     */
    public function get($key, $params = []) {
        if (!$this->has($key, $params)) {
            return null;
        }
        
        $cachePath = $this->getCachePath($key, $params);
        $cacheData = $this->readCacheFile($cachePath);
        
        $this->logger->debug('Cache hit', ['key' => $key, 'params' => $params]);
        
        return $cacheData['data'];
    }
    
    /**
     * Salvar dados no cache
     */
    public function set($key, $data, $ttl = null, $params = []) {
        $ttl = $ttl ?? $this->defaultTtl;
        $cachePath = $this->getCachePath($key, $params);
        
        $cacheData = [
            'data' => $data,
            'created' => time(),
            'expires' => time() + $ttl,
            'ttl' => $ttl
        ];
        
        $result = file_put_contents($cachePath, serialize($cacheData), LOCK_EX);
        
        if ($result !== false) {
            $this->logger->debug('Cache set', [
                'key' => $key, 
                'params' => $params, 
                'ttl' => $ttl,
                'size' => strlen(serialize($data))
            ]);
            return true;
        }
        
        $this->logger->error('Failed to write cache', ['key' => $key, 'path' => $cachePath]);
        return false;
    }
    
    /**
     * Deletar cache
     */
    public function delete($key, $params = []) {
        $cachePath = $this->getCachePath($key, $params);
        
        if (file_exists($cachePath)) {
            $result = unlink($cachePath);
            if ($result) {
                $this->logger->debug('Cache deleted', ['key' => $key, 'params' => $params]);
            }
            return $result;
        }
        
        return true;
    }
    
    /**
     * Limpar todo o cache
     */
    public function clear() {
        $files = glob($this->cacheDir . '/*.cache');
        $deleted = 0;
        
        foreach ($files as $file) {
            if (unlink($file)) {
                $deleted++;
            }
        }
        
        $this->logger->info('Cache cleared', ['deleted_files' => $deleted]);
        return $deleted;
    }
    
    /**
     * Limpar cache expirado
     */
    public function clearExpired() {
        $files = glob($this->cacheDir . '/*.cache');
        $deleted = 0;
        
        foreach ($files as $file) {
            $cacheData = $this->readCacheFile($file);
            if ($cacheData && time() > $cacheData['expires']) {
                if (unlink($file)) {
                    $deleted++;
                }
            }
        }
        
        if ($deleted > 0) {
            $this->logger->info('Expired cache cleared', ['deleted_files' => $deleted]);
        }
        
        return $deleted;
    }
    
    /**
     * Ler arquivo de cache
     */
    private function readCacheFile($path) {
        if (!file_exists($path)) {
            return null;
        }
        
        $content = file_get_contents($path);
        if ($content === false) {
            return null;
        }
        
        $data = unserialize($content);
        if ($data === false) {
            return null;
        }
        
        return $data;
    }
    
    /**
     * Obter estatísticas do cache
     */
    public function getStats() {
        $files = glob($this->cacheDir . '/*.cache');
        $totalSize = 0;
        $expiredCount = 0;
        $validCount = 0;
        
        foreach ($files as $file) {
            $size = filesize($file);
            $totalSize += $size;
            
            $cacheData = $this->readCacheFile($file);
            if ($cacheData) {
                if (time() > $cacheData['expires']) {
                    $expiredCount++;
                } else {
                    $validCount++;
                }
            }
        }
        
        return [
            'total_files' => count($files),
            'valid_files' => $validCount,
            'expired_files' => $expiredCount,
            'total_size' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2)
        ];
    }
    
    /**
     * Cache para consultas de posts
     */
    public function cachePosts($page, $limit, $callback) {
        $key = "posts_page_{$page}_limit_{$limit}";
        $params = ['page' => $page, 'limit' => $limit];
        
        if ($this->has($key, $params)) {
            return $this->get($key, $params);
        }
        
        $data = $callback();
        $this->set($key, $data, 1800, $params); // 30 minutos
        
        return $data;
    }
    
    /**
     * Cache para configurações visuais
     */
    public function cacheVisualConfig($callback) {
        $key = "visual_config";
        
        if ($this->has($key)) {
            return $this->get($key);
        }
        
        $data = $callback();
        $this->set($key, $data, 3600); // 1 hora
        
        return $data;
    }
    
    /**
     * Cache para anúncios
     */
    public function cacheAnuncios($localizacao, $postId, $callback) {
        $key = "anuncios_{$localizacao}";
        $params = ['localizacao' => $localizacao, 'post_id' => $postId];
        
        if ($this->has($key, $params)) {
            return $this->get($key, $params);
        }
        
        $data = $callback();
        $this->set($key, $data, 900, $params); // 15 minutos
        
        return $data;
    }
} 