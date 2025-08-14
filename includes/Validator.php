<?php

class Validator {
    private $errors = [];
    private $data = [];
    private $logger;
    
    public function __construct() {
        $this->logger = new Logger();
    }
    
    /**
     * Definir dados para validação
     */
    public function setData($data) {
        $this->data = $data;
        $this->errors = [];
        return $this;
    }
    
    /**
     * Validar campo obrigatório
     */
    public function required($field, $message = null) {
        $value = $this->getValue($field);
        
        if (empty($value) && $value !== '0') {
            $this->addError($field, $message ?? "O campo {$field} é obrigatório");
        }
        
        return $this;
    }
    
    /**
     * Validar email
     */
    public function email($field, $message = null) {
        $value = $this->getValue($field);
        
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $message ?? "O campo {$field} deve ser um email válido");
        }
        
        return $this;
    }
    
    /**
     * Validar URL
     */
    public function url($field, $message = null) {
        $value = $this->getValue($field);
        
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, $message ?? "O campo {$field} deve ser uma URL válida");
        }
        
        return $this;
    }
    
    /**
     * Validar tamanho mínimo
     */
    public function minLength($field, $min, $message = null) {
        $value = $this->getValue($field);
        
        if (!empty($value) && strlen($value) < $min) {
            $this->addError($field, $message ?? "O campo {$field} deve ter pelo menos {$min} caracteres");
        }
        
        return $this;
    }
    
    /**
     * Validar tamanho máximo
     */
    public function maxLength($field, $max, $message = null) {
        $value = $this->getValue($field);
        
        if (!empty($value) && strlen($value) > $max) {
            $this->addError($field, $message ?? "O campo {$field} deve ter no máximo {$max} caracteres");
        }
        
        return $this;
    }
    
    /**
     * Validar número mínimo
     */
    public function min($field, $min, $message = null) {
        $value = $this->getValue($field);
        
        if (!empty($value) && is_numeric($value) && $value < $min) {
            $this->addError($field, $message ?? "O campo {$field} deve ser maior ou igual a {$min}");
        }
        
        return $this;
    }
    
    /**
     * Validar número máximo
     */
    public function max($field, $max, $message = null) {
        $value = $this->getValue($field);
        
        if (!empty($value) && is_numeric($value) && $value > $max) {
            $this->addError($field, $message ?? "O campo {$field} deve ser menor ou igual a {$max}");
        }
        
        return $this;
    }
    
    /**
     * Validar valores permitidos
     */
    public function in($field, $allowedValues, $message = null) {
        $value = $this->getValue($field);
        
        if (!empty($value) && !in_array($value, $allowedValues)) {
            $allowed = implode(', ', $allowedValues);
            $this->addError($field, $message ?? "O campo {$field} deve ser um dos seguintes valores: {$allowed}");
        }
        
        return $this;
    }
    
    /**
     * Validar formato de data
     */
    public function date($field, $format = 'Y-m-d', $message = null) {
        $value = $this->getValue($field);
        
        if (!empty($value)) {
            $date = DateTime::createFromFormat($format, $value);
            if (!$date || $date->format($format) !== $value) {
                $this->addError($field, $message ?? "O campo {$field} deve ser uma data válida no formato {$format}");
            }
        }
        
        return $this;
    }
    
    /**
     * Validar arquivo de imagem
     */
    public function image($field, $message = null) {
        $file = $this->getValue($field);
        
        if (!empty($file) && is_array($file)) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            
            if (!in_array($file['type'], $allowedTypes)) {
                $this->addError($field, $message ?? "O arquivo deve ser uma imagem válida (JPG, PNG, GIF, WEBP)");
            }
            
            // Verificar tamanho máximo (5MB)
            if ($file['size'] > 5242880) {
                $this->addError($field, $message ?? "O arquivo deve ter no máximo 5MB");
            }
        }
        
        return $this;
    }
    
    /**
     * Validar slug (URL amigável)
     */
    public function slug($field, $message = null) {
        $value = $this->getValue($field);
        
        if (!empty($value) && !preg_match('/^[a-z0-9-]+$/', $value)) {
            $this->addError($field, $message ?? "O campo {$field} deve conter apenas letras minúsculas, números e hífens");
        }
        
        return $this;
    }
    
    /**
     * Validar HTML seguro
     */
    public function safeHtml($field, $message = null) {
        $value = $this->getValue($field);
        
        if (!empty($value)) {
            // Lista de tags permitidas
            $allowedTags = '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><blockquote><code><pre>';
            
            $cleaned = strip_tags($value, $allowedTags);
            
            if ($cleaned !== $value) {
                $this->addError($field, $message ?? "O campo {$field} contém HTML não permitido");
            }
        }
        
        return $this;
    }
    
    /**
     * Validar CSRF token
     */
    public function csrf($field, $message = null) {
        $value = $this->getValue($field);
        $sessionToken = $_SESSION['csrf_token'] ?? null;
        
        if (empty($value) || $value !== $sessionToken) {
            $this->addError($field, $message ?? "Token de segurança inválido");
            $this->logger->warning('CSRF token validation failed', [
                'provided_token' => $value,
                'session_token' => $sessionToken
            ]);
        }
        
        return $this;
    }
    
    /**
     * Validar reCAPTCHA
     */
    public function recaptcha($field, $secretKey, $message = null) {
        $value = $this->getValue($field);
        
        if (!empty($value)) {
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $data = [
                'secret' => $secretKey,
                'response' => $value,
                'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
            ];
            
            $options = [
                'http' => [
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data)
                ]
            ];
            
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            $response = json_decode($result, true);
            
            if (!$response['success']) {
                $this->addError($field, $message ?? "Verificação reCAPTCHA falhou");
                $this->logger->warning('reCAPTCHA validation failed', [
                    'response' => $response
                ]);
            }
        }
        
        return $this;
    }
    
    /**
     * Validar dados de anúncio
     */
    public function validateAnuncio($data) {
        return $this->setData($data)
            ->required('titulo')
            ->maxLength('titulo', 255)
            ->required('imagem')
            ->url('imagem')
            ->required('link_compra')
            ->url('link_compra')
            ->in('localizacao', ['sidebar', 'conteudo'])
            ->maxLength('cta_texto', 100);
    }
    
    /**
     * Validar dados de post
     */
    public function validatePost($data) {
        return $this->setData($data)
            ->required('titulo')
            ->maxLength('titulo', 255)
            ->required('conteudo')
            ->minLength('conteudo', 50)
            ->safeHtml('conteudo')
            ->required('slug')
            ->slug('slug')
            ->required('categoria_id')
            ->min('categoria_id', 1);
    }
    
    /**
     * Validar dados de usuário
     */
    public function validateUsuario($data) {
        return $this->setData($data)
            ->required('nome')
            ->maxLength('nome', 100)
            ->required('email')
            ->email('email')
            ->required('senha')
            ->minLength('senha', 6);
    }
    
    /**
     * Obter valor do campo
     */
    private function getValue($field) {
        return $this->data[$field] ?? null;
    }
    
    /**
     * Adicionar erro
     */
    private function addError($field, $message) {
        $this->errors[$field][] = $message;
        
        $this->logger->warning('Validation error', [
            'field' => $field,
            'message' => $message,
            'value' => $this->getValue($field)
        ]);
    }
    
    /**
     * Verificar se há erros
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Obter todos os erros
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Obter erros de um campo específico
     */
    public function getFieldErrors($field) {
        return $this->errors[$field] ?? [];
    }
    
    /**
     * Obter primeiro erro de um campo
     */
    public function getFirstError($field) {
        $errors = $this->getFieldErrors($field);
        return !empty($errors) ? $errors[0] : null;
    }
    
    /**
     * Limpar erros
     */
    public function clearErrors() {
        $this->errors = [];
        return $this;
    }
    
    /**
     * Obter dados validados
     */
    public function getValidatedData() {
        return $this->data;
    }
} 