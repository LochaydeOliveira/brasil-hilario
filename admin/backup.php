<?php
require_once '../includes/db.php';
require_once '../includes/Logger.php';

class BackupManager {
    private $pdo;
    private $logger;
    private $backupDir;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->logger = new Logger();
        $this->backupDir = __DIR__ . '/../backups';
        
        // Criar diretório de backup se não existir
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }
    
    /**
     * Criar backup completo do banco de dados
     */
    public function createFullBackup() {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "backup_completo_{$timestamp}.sql";
            $filepath = $this->backupDir . '/' . $filename;
            
            // Obter configurações do banco
            $host = DB_HOST_LOCAL;
            $database = DB_NAME;
            $username = DB_USER;
            $password = DB_PASS;
            
            // Comando mysqldump
            $command = "mysqldump --host={$host} --user={$username} --password={$password} " .
                      "--single-transaction --routines --triggers --events " .
                      "--add-drop-database --create-options " .
                      "{$database} > {$filepath} 2>&1";
            
            $output = [];
            $returnCode = 0;
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($filepath)) {
                $fileSize = filesize($filepath);
                $this->logger->info('Backup completo criado com sucesso', [
                    'filename' => $filename,
                    'size' => $fileSize,
                    'size_mb' => round($fileSize / 1024 / 1024, 2)
                ]);
                
                // Comprimir o arquivo
                $this->compressFile($filepath);
                
                return [
                    'success' => true,
                    'filename' => $filename,
                    'size' => $fileSize,
                    'size_mb' => round($fileSize / 1024 / 1024, 2)
                ];
            } else {
                $this->logger->error('Erro ao criar backup completo', [
                    'command' => $command,
                    'output' => $output,
                    'return_code' => $returnCode
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Erro ao executar mysqldump',
                    'details' => implode("\n", $output)
                ];
            }
            
        } catch (Exception $e) {
            $this->logger->error('Exceção ao criar backup completo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Criar backup apenas dos dados (sem estrutura)
     */
    public function createDataBackup() {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "backup_dados_{$timestamp}.sql";
            $filepath = $this->backupDir . '/' . $filename;
            
            // Obter configurações do banco
            $host = DB_HOST_LOCAL;
            $database = DB_NAME;
            $username = DB_USER;
            $password = DB_PASS;
            
            // Comando mysqldump apenas para dados
            $command = "mysqldump --host={$host} --user={$username} --password={$password} " .
                      "--single-transaction --no-create-info --no-create-db " .
                      "--no-set-names --skip-add-drop-table " .
                      "{$database} > {$filepath} 2>&1";
            
            $output = [];
            $returnCode = 0;
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($filepath)) {
                $fileSize = filesize($filepath);
                $this->logger->info('Backup de dados criado com sucesso', [
                    'filename' => $filename,
                    'size' => $fileSize,
                    'size_mb' => round($fileSize / 1024 / 1024, 2)
                ]);
                
                // Comprimir o arquivo
                $this->compressFile($filepath);
                
                return [
                    'success' => true,
                    'filename' => $filename,
                    'size' => $fileSize,
                    'size_mb' => round($fileSize / 1024 / 1024, 2)
                ];
            } else {
                $this->logger->error('Erro ao criar backup de dados', [
                    'command' => $command,
                    'output' => $output,
                    'return_code' => $returnCode
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Erro ao executar mysqldump',
                    'details' => implode("\n", $output)
                ];
            }
            
        } catch (Exception $e) {
            $this->logger->error('Exceção ao criar backup de dados', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Criar backup de tabelas específicas
     */
    public function createTableBackup($tables) {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $tablesStr = implode('_', $tables);
            $filename = "backup_tabelas_{$tablesStr}_{$timestamp}.sql";
            $filepath = $this->backupDir . '/' . $filename;
            
            // Obter configurações do banco
            $host = DB_HOST_LOCAL;
            $database = DB_NAME;
            $username = DB_USER;
            $password = DB_PASS;
            
            // Comando mysqldump para tabelas específicas
            $tablesList = implode(' ', $tables);
            $command = "mysqldump --host={$host} --user={$username} --password={$password} " .
                      "--single-transaction --routines --triggers " .
                      "{$database} {$tablesList} > {$filepath} 2>&1";
            
            $output = [];
            $returnCode = 0;
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($filepath)) {
                $fileSize = filesize($filepath);
                $this->logger->info('Backup de tabelas criado com sucesso', [
                    'filename' => $filename,
                    'tables' => $tables,
                    'size' => $fileSize,
                    'size_mb' => round($fileSize / 1024 / 1024, 2)
                ]);
                
                // Comprimir o arquivo
                $this->compressFile($filepath);
                
                return [
                    'success' => true,
                    'filename' => $filename,
                    'tables' => $tables,
                    'size' => $fileSize,
                    'size_mb' => round($fileSize / 1024 / 1024, 2)
                ];
            } else {
                $this->logger->error('Erro ao criar backup de tabelas', [
                    'command' => $command,
                    'tables' => $tables,
                    'output' => $output,
                    'return_code' => $returnCode
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Erro ao executar mysqldump',
                    'details' => implode("\n", $output)
                ];
            }
            
        } catch (Exception $e) {
            $this->logger->error('Exceção ao criar backup de tabelas', [
                'error' => $e->getMessage(),
                'tables' => $tables,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Comprimir arquivo de backup
     */
    private function compressFile($filepath) {
        if (class_exists('ZipArchive')) {
            $zipPath = $filepath . '.zip';
            $zip = new ZipArchive();
            
            if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($filepath, basename($filepath));
                $zip->close();
                
                // Remover arquivo original
                unlink($filepath);
                
                $this->logger->info('Arquivo de backup comprimido', [
                    'original' => basename($filepath),
                    'compressed' => basename($zipPath),
                    'original_size' => filesize($filepath),
                    'compressed_size' => filesize($zipPath)
                ]);
            }
        }
    }
    
    /**
     * Listar backups disponíveis
     */
    public function listBackups() {
        $backups = [];
        $files = glob($this->backupDir . '/*.{sql,zip}', GLOB_BRACE);
        
        foreach ($files as $file) {
            $filename = basename($file);
            $fileSize = filesize($file);
            $fileTime = filemtime($file);
            
            $backups[] = [
                'filename' => $filename,
                'size' => $fileSize,
                'size_mb' => round($fileSize / 1024 / 1024, 2),
                'created_at' => date('Y-m-d H:i:s', $fileTime),
                'type' => pathinfo($file, PATHINFO_EXTENSION) === 'zip' ? 'compressed' : 'sql'
            ];
        }
        
        // Ordenar por data de criação (mais recente primeiro)
        usort($backups, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return $backups;
    }
    
    /**
     * Deletar backup
     */
    public function deleteBackup($filename) {
        $filepath = $this->backupDir . '/' . $filename;
        
        if (file_exists($filepath)) {
            $result = unlink($filepath);
            
            if ($result) {
                $this->logger->info('Backup deletado', ['filename' => $filename]);
                return ['success' => true];
            } else {
                $this->logger->error('Erro ao deletar backup', ['filename' => $filename]);
                return ['success' => false, 'error' => 'Erro ao deletar arquivo'];
            }
        } else {
            return ['success' => false, 'error' => 'Arquivo não encontrado'];
        }
    }
    
    /**
     * Limpar backups antigos (manter apenas os últimos 10)
     */
    public function cleanOldBackups($keepCount = 10) {
        $backups = $this->listBackups();
        
        if (count($backups) <= $keepCount) {
            return ['success' => true, 'deleted' => 0];
        }
        
        $toDelete = array_slice($backups, $keepCount);
        $deleted = 0;
        
        foreach ($toDelete as $backup) {
            $result = $this->deleteBackup($backup['filename']);
            if ($result['success']) {
                $deleted++;
            }
        }
        
        $this->logger->info('Backups antigos removidos', [
            'deleted_count' => $deleted,
            'kept_count' => $keepCount
        ]);
        
        return ['success' => true, 'deleted' => $deleted];
    }
    
    /**
     * Obter estatísticas de backup
     */
    public function getBackupStats() {
        $backups = $this->listBackups();
        $totalSize = 0;
        $sqlCount = 0;
        $zipCount = 0;
        
        foreach ($backups as $backup) {
            $totalSize += $backup['size'];
            if ($backup['type'] === 'compressed') {
                $zipCount++;
            } else {
                $sqlCount++;
            }
        }
        
        return [
            'total_backups' => count($backups),
            'sql_files' => $sqlCount,
            'compressed_files' => $zipCount,
            'total_size' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2)
        ];
    }
}

// Interface web para o sistema de backup
if (isset($_GET['action'])) {
    session_start();
    
    // Verificar se usuário está logado
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        die('Acesso negado');
    }
    
    $backupManager = new BackupManager($pdo);
    
    switch ($_GET['action']) {
        case 'create_full':
            $result = $backupManager->createFullBackup();
            break;
            
        case 'create_data':
            $result = $backupManager->createDataBackup();
            break;
            
        case 'create_tables':
            $tables = ['posts', 'anuncios', 'grupos_anuncios', 'configuracoes_visuais'];
            $result = $backupManager->createTableBackup($tables);
            break;
            
        case 'list':
            $result = $backupManager->listBackups();
            break;
            
        case 'delete':
            $filename = $_GET['filename'] ?? '';
            $result = $backupManager->deleteBackup($filename);
            break;
            
        case 'clean':
            $result = $backupManager->cleanOldBackups();
            break;
            
        case 'stats':
            $result = $backupManager->getBackupStats();
            break;
            
        default:
            $result = ['success' => false, 'error' => 'Ação inválida'];
    }
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Backup - Brasil Hilário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1><i class="fas fa-database"></i> Sistema de Backup</h1>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-plus"></i> Criar Backup</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary mb-2 w-100" onclick="createBackup('full')">
                            <i class="fas fa-database"></i> Backup Completo
                        </button>
                        <button class="btn btn-info mb-2 w-100" onclick="createBackup('data')">
                            <i class="fas fa-table"></i> Backup de Dados
                        </button>
                        <button class="btn btn-warning mb-2 w-100" onclick="createBackup('tables')">
                            <i class="fas fa-list"></i> Backup de Tabelas Específicas
                        </button>
                        <button class="btn btn-danger w-100" onclick="cleanBackups()">
                            <i class="fas fa-trash"></i> Limpar Backups Antigos
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar"></i> Estatísticas</h5>
                    </div>
                    <div class="card-body" id="stats">
                        <p class="text-muted">Carregando...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Backups Disponíveis</h5>
                    </div>
                    <div class="card-body">
                        <div id="backups-list">
                            <p class="text-muted">Carregando...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Carregar dados ao iniciar
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            loadBackups();
        });
        
        function createBackup(type) {
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Criando...';
            button.disabled = true;
            
            fetch(`?action=create_${type}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Backup criado com sucesso!');
                        loadStats();
                        loadBackups();
                    } else {
                        alert('Erro ao criar backup: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Erro ao criar backup: ' + error);
                })
                .finally(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
        }
        
        function loadStats() {
            fetch('?action=stats')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('stats').innerHTML = `
                        <p><strong>Total de Backups:</strong> ${data.total_backups}</p>
                        <p><strong>Arquivos SQL:</strong> ${data.sql_files}</p>
                        <p><strong>Arquivos Comprimidos:</strong> ${data.compressed_files}</p>
                        <p><strong>Tamanho Total:</strong> ${data.total_size_mb} MB</p>
                    `;
                })
                .catch(error => {
                    document.getElementById('stats').innerHTML = '<p class="text-danger">Erro ao carregar estatísticas</p>';
                });
        }
        
        function loadBackups() {
            fetch('?action=list')
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        document.getElementById('backups-list').innerHTML = '<p class="text-muted">Nenhum backup encontrado</p>';
                        return;
                    }
                    
                    let html = '<div class="table-responsive"><table class="table table-striped">';
                    html += '<thead><tr><th>Arquivo</th><th>Tamanho</th><th>Data</th><th>Tipo</th><th>Ações</th></tr></thead><tbody>';
                    
                    data.forEach(backup => {
                        html += `<tr>
                            <td>${backup.filename}</td>
                            <td>${backup.size_mb} MB</td>
                            <td>${backup.created_at}</td>
                            <td><span class="badge bg-${backup.type === 'compressed' ? 'success' : 'primary'}">${backup.type}</span></td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="deleteBackup('${backup.filename}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>`;
                    });
                    
                    html += '</tbody></table></div>';
                    document.getElementById('backups-list').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('backups-list').innerHTML = '<p class="text-danger">Erro ao carregar backups</p>';
                });
        }
        
        function deleteBackup(filename) {
            if (confirm('Tem certeza que deseja deletar este backup?')) {
                fetch(`?action=delete&filename=${encodeURIComponent(filename)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Backup deletado com sucesso!');
                            loadStats();
                            loadBackups();
                        } else {
                            alert('Erro ao deletar backup: ' + data.error);
                        }
                    })
                    .catch(error => {
                        alert('Erro ao deletar backup: ' + error);
                    });
            }
        }
        
        function cleanBackups() {
            if (confirm('Tem certeza que deseja limpar os backups antigos? Serão mantidos apenas os 10 mais recentes.')) {
                fetch('?action=clean')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`${data.deleted} backups antigos foram removidos!`);
                            loadStats();
                            loadBackups();
                        } else {
                            alert('Erro ao limpar backups: ' + data.error);
                        }
                    })
                    .catch(error => {
                        alert('Erro ao limpar backups: ' + error);
                    });
            }
        }
    </script>
</body>
</html> 