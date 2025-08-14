<?php

class NewsletterManager {
    private $pdo;
    private $logger;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->logger = new Logger();
    }
    
    /**
     * Criar tabela de newsletter se não existir
     */
    public function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            nome VARCHAR(100),
            status ENUM('ativo', 'inativo', 'cancelado') DEFAULT 'ativo',
            token_confirmacao VARCHAR(255),
            token_cancelamento VARCHAR(255),
            data_inscricao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_confirmacao TIMESTAMP NULL,
            data_cancelamento TIMESTAMP NULL,
            ultimo_envio TIMESTAMP NULL,
            total_envios INT DEFAULT 0,
            ip_inscricao VARCHAR(45),
            user_agent TEXT,
            INDEX idx_email (email),
            INDEX idx_status (status),
            INDEX idx_token_confirmacao (token_confirmacao),
            INDEX idx_token_cancelamento (token_cancelamento)
        )";
        
        try {
            $this->pdo->exec($sql);
            $this->logger->info('Tabela newsletter criada/verificada');
            return true;
        } catch (Exception $e) {
            $this->logger->error('Erro ao criar tabela newsletter', ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Inscrever email na newsletter
     */
    public function subscribe($email, $nome = null) {
        try {
            // Validar email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'error' => 'Email inválido'];
            }
            
            // Verificar se já existe
            $stmt = $this->pdo->prepare("SELECT id, status FROM newsletter_subscribers WHERE email = ?");
            $stmt->execute([$email]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                if ($existing['status'] === 'ativo') {
                    return ['success' => false, 'error' => 'Este email já está inscrito na newsletter'];
                } else {
                    // Reativar inscrição
                    $token = $this->generateToken();
                    $stmt = $this->pdo->prepare("
                        UPDATE newsletter_subscribers 
                        SET status = 'ativo', token_confirmacao = ?, data_inscricao = NOW(), 
                            ip_inscricao = ?, user_agent = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$token, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '', $existing['id']]);
                    
                    $this->logger->info('Newsletter reativada', ['email' => $email]);
                    return ['success' => true, 'message' => 'Inscrição reativada! Verifique seu email para confirmar.'];
                }
            }
            
            // Nova inscrição
            $token = $this->generateToken();
            $stmt = $this->pdo->prepare("
                INSERT INTO newsletter_subscribers (email, nome, token_confirmacao, ip_inscricao, user_agent) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$email, $nome, $token, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
            
            // Enviar email de confirmação
            $this->sendConfirmationEmail($email, $nome, $token);
            
            $this->logger->info('Nova inscrição na newsletter', ['email' => $email]);
            
            return ['success' => true, 'message' => 'Inscrição realizada! Verifique seu email para confirmar.'];
            
        } catch (Exception $e) {
            $this->logger->error('Erro ao inscrever na newsletter', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'error' => 'Erro ao processar inscrição'];
        }
    }
    
    /**
     * Confirmar inscrição
     */
    public function confirmSubscription($token) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE newsletter_subscribers 
                SET status = 'ativo', data_confirmacao = NOW(), token_confirmacao = NULL 
                WHERE token_confirmacao = ? AND status = 'inativo'
            ");
            $stmt->execute([$token]);
            
            if ($stmt->rowCount() > 0) {
                $this->logger->info('Newsletter confirmada', ['token' => $token]);
                return ['success' => true, 'message' => 'Inscrição confirmada com sucesso!'];
            } else {
                return ['success' => false, 'error' => 'Token inválido ou já confirmado'];
            }
            
        } catch (Exception $e) {
            $this->logger->error('Erro ao confirmar newsletter', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'error' => 'Erro ao confirmar inscrição'];
        }
    }
    
    /**
     * Cancelar inscrição
     */
    public function unsubscribe($email, $token = null) {
        try {
            if ($token) {
                // Cancelar por token
                $stmt = $this->pdo->prepare("
                    UPDATE newsletter_subscribers 
                    SET status = 'cancelado', data_cancelamento = NOW(), token_cancelamento = NULL 
                    WHERE token_cancelamento = ? AND status = 'ativo'
                ");
                $stmt->execute([$token]);
            } else {
                // Cancelar por email
                $stmt = $this->pdo->prepare("
                    UPDATE newsletter_subscribers 
                    SET status = 'cancelado', data_cancelamento = NOW() 
                    WHERE email = ? AND status = 'ativo'
                ");
                $stmt->execute([$email]);
            }
            
            if ($stmt->rowCount() > 0) {
                $this->logger->info('Newsletter cancelada', ['email' => $email, 'token' => $token]);
                return ['success' => true, 'message' => 'Inscrição cancelada com sucesso!'];
            } else {
                return ['success' => false, 'error' => 'Email não encontrado ou já cancelado'];
            }
            
        } catch (Exception $e) {
            $this->logger->error('Erro ao cancelar newsletter', [
                'email' => $email,
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'error' => 'Erro ao cancelar inscrição'];
        }
    }
    
    /**
     * Enviar newsletter
     */
    public function sendNewsletter($subject, $content, $postId = null) {
        try {
            // Buscar inscritos ativos
            $stmt = $this->pdo->prepare("
                SELECT id, email, nome, total_envios 
                FROM newsletter_subscribers 
                WHERE status = 'ativo'
            ");
            $stmt->execute();
            $subscribers = $stmt->fetchAll();
            
            $sent = 0;
            $errors = 0;
            
            foreach ($subscribers as $subscriber) {
                $result = $this->sendEmail($subscriber['email'], $subscriber['nome'], $subject, $content, $postId);
                
                if ($result) {
                    // Atualizar estatísticas
                    $stmt = $this->pdo->prepare("
                        UPDATE newsletter_subscribers 
                        SET ultimo_envio = NOW(), total_envios = total_envios + 1 
                        WHERE id = ?
                    ");
                    $stmt->execute([$subscriber['id']]);
                    $sent++;
                } else {
                    $errors++;
                }
            }
            
            $this->logger->info('Newsletter enviada', [
                'subject' => $subject,
                'sent' => $sent,
                'errors' => $errors,
                'post_id' => $postId
            ]);
            
            return [
                'success' => true,
                'sent' => $sent,
                'errors' => $errors,
                'total' => count($subscribers)
            ];
            
        } catch (Exception $e) {
            $this->logger->error('Erro ao enviar newsletter', [
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Enviar email de confirmação
     */
    private function sendConfirmationEmail($email, $nome, $token) {
        $subject = 'Confirme sua inscrição na newsletter do Brasil Hilário';
        $confirmUrl = BLOG_URL . '/confirmar-newsletter?token=' . $token;
        $cancelUrl = BLOG_URL . '/cancelar-newsletter?token=' . $token;
        
        $content = "
            <h2>Olá {$nome}!</h2>
            <p>Obrigado por se inscrever na newsletter do Brasil Hilário!</p>
            <p>Para confirmar sua inscrição, clique no link abaixo:</p>
            <p><a href='{$confirmUrl}' style='background: #0b8103; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Confirmar Inscrição</a></p>
            <p>Se você não solicitou esta inscrição, pode cancelar clicando <a href='{$cancelUrl}'>aqui</a>.</p>
            <p>Atenciosamente,<br>Equipe Brasil Hilário</p>
        ";
        
        return $this->sendEmail($email, $nome, $subject, $content);
    }
    
    /**
     * Enviar email
     */
    private function sendEmail($email, $nome, $subject, $content, $postId = null) {
        try {
            $headers = [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=UTF-8',
                'From: Brasil Hilário <noreply@brasilhilario.com.br>',
                'Reply-To: noreply@brasilhilario.com.br',
                'X-Mailer: PHP/' . phpversion()
            ];
            
            // Template HTML
            $htmlContent = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>{$subject}</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: #0b8103; color: white; padding: 20px; text-align: center; }
                        .content { padding: 20px; background: #f9f9f9; }
                        .footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; }
                        .btn { display: inline-block; background: #0b8103; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
                        .btn:hover { background: #0a6b02; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>Brasil Hilário</h1>
                            <p>O melhor conteúdo de humor do Brasil</p>
                        </div>
                        <div class='content'>
                            {$content}
                        </div>
                        <div class='footer'>
                            <p>© 2025 Brasil Hilário. Todos os direitos reservados.</p>
                            <p><a href='" . BLOG_URL . "/cancelar-newsletter?email=" . urlencode($email) . "' style='color: #ccc;'>Cancelar inscrição</a></p>
                        </div>
                    </div>
                </body>
                </html>
            ";
            
            $result = mail($email, $subject, $htmlContent, implode("\r\n", $headers));
            
            if ($result) {
                $this->logger->info('Email enviado com sucesso', [
                    'email' => $email,
                    'subject' => $subject,
                    'post_id' => $postId
                ]);
            } else {
                $this->logger->error('Falha ao enviar email', [
                    'email' => $email,
                    'subject' => $subject
                ]);
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->logger->error('Erro ao enviar email', [
                'email' => $email,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Gerar token único
     */
    private function generateToken() {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Obter estatísticas da newsletter
     */
    public function getStats() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'ativo' THEN 1 ELSE 0 END) as ativos,
                    SUM(CASE WHEN status = 'inativo' THEN 1 ELSE 0 END) as inativos,
                    SUM(CASE WHEN status = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                    SUM(total_envios) as total_envios,
                    AVG(total_envios) as media_envios
                FROM newsletter_subscribers
            ");
            $stmt->execute();
            $stats = $stmt->fetch();
            
            // Inscrições por mês (últimos 6 meses)
            $stmt = $this->pdo->prepare("
                SELECT 
                    DATE_FORMAT(data_inscricao, '%Y-%m') as mes,
                    COUNT(*) as inscricoes
                FROM newsletter_subscribers
                WHERE data_inscricao >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(data_inscricao, '%Y-%m')
                ORDER BY mes DESC
            ");
            $stmt->execute();
            $monthlyStats = $stmt->fetchAll();
            
            return [
                'success' => true,
                'stats' => $stats,
                'monthly' => $monthlyStats
            ];
            
        } catch (Exception $e) {
            $this->logger->error('Erro ao obter estatísticas da newsletter', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Listar inscritos
     */
    public function listSubscribers($page = 1, $limit = 20, $status = null) {
        try {
            $offset = ($page - 1) * $limit;
            $where = '';
            $params = [];
            
            if ($status) {
                $where = 'WHERE status = ?';
                $params[] = $status;
            }
            
            $stmt = $this->pdo->prepare("
                SELECT * FROM newsletter_subscribers 
                {$where}
                ORDER BY data_inscricao DESC 
                LIMIT ? OFFSET ?
            ");
            
            $params[] = $limit;
            $params[] = $offset;
            $stmt->execute($params);
            $subscribers = $stmt->fetchAll();
            
            // Contar total
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as total FROM newsletter_subscribers {$where}
            ");
            $stmt->execute($status ? [$status] : []);
            $total = $stmt->fetch()['total'];
            
            return [
                'success' => true,
                'subscribers' => $subscribers,
                'total' => $total,
                'pages' => ceil($total / $limit),
                'current_page' => $page
            ];
            
        } catch (Exception $e) {
            $this->logger->error('Erro ao listar inscritos', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Exportar lista de emails
     */
    public function exportEmails($status = 'ativo') {
        try {
            $stmt = $this->pdo->prepare("
                SELECT email, nome, data_inscricao, total_envios 
                FROM newsletter_subscribers 
                WHERE status = ?
                ORDER BY data_inscricao DESC
            ");
            $stmt->execute([$status]);
            $subscribers = $stmt->fetchAll();
            
            $csv = "Email,Nome,Data de Inscrição,Total de Envios\n";
            foreach ($subscribers as $subscriber) {
                $csv .= "\"{$subscriber['email']}\",\"{$subscriber['nome']}\",\"{$subscriber['data_inscricao']}\",\"{$subscriber['total_envios']}\"\n";
            }
            
            return [
                'success' => true,
                'csv' => $csv,
                'count' => count($subscribers)
            ];
            
        } catch (Exception $e) {
            $this->logger->error('Erro ao exportar emails', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
} 