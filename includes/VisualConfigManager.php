<?php
require_once __DIR__ . '/db.php';

class VisualConfigManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Gerenciar cores
    public function getCor($elemento, $propriedade, $padrao = '#000000') {
        $stmt = $this->pdo->prepare("
            SELECT valor FROM configuracoes_visuais 
            WHERE categoria = 'cores' AND elemento = ? AND propriedade = ? AND ativo = 1
        ");
        $stmt->execute([$elemento, $propriedade]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['valor'] : $padrao;
    }
    
    public function setCor($elemento, $propriedade, $valor) {
        $stmt = $this->pdo->prepare("
            INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo) 
            VALUES ('cores', ?, ?, ?, 'cor')
            ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW()
        ");
        return $stmt->execute([$elemento, $propriedade, $valor]);
    }
    
    // Gerenciar fontes
    public function getFonte($elemento, $propriedade, $padrao = 'Arial, sans-serif') {
        $stmt = $this->pdo->prepare("
            SELECT valor FROM configuracoes_visuais 
            WHERE categoria = 'fontes' AND elemento = ? AND propriedade = ? AND ativo = 1
        ");
        $stmt->execute([$elemento, $propriedade]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['valor'] : $padrao;
    }
    
    public function setFonte($elemento, $propriedade, $valor) {
        $stmt = $this->pdo->prepare("
            INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo) 
            VALUES ('fontes', ?, ?, ?, 'fonte')
            ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW()
        ");
        return $stmt->execute([$elemento, $propriedade, $valor]);
    }
    
    // Obter todas as configurações visuais
    public function getAllConfigs() {
        $stmt = $this->pdo->query("
            SELECT categoria, elemento, propriedade, valor, tipo 
            FROM configuracoes_visuais 
            WHERE ativo = 1 
            ORDER BY categoria, elemento, propriedade
        ");
        
        $configs = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $configs[$row['categoria']][$row['elemento']][$row['propriedade']] = $row['valor'];
        }
        return $configs;
    }
    
    // Gerar CSS dinâmico
    public function generateCSS() {
        $configs = $this->getAllConfigs();
        $css = "/* CSS Gerado Dinamicamente */\n\n";
        
        // Cores principais
        if (isset($configs['cores']['site'])) {
            $cores = $configs['cores']['site'];
            $css .= ":root {\n";
            foreach ($cores as $prop => $valor) {
                $css .= "  --{$prop}: {$valor};\n";
            }
            $css .= "}\n\n";
        }
        
        // Cores do header
        if (isset($configs['cores']['header'])) {
            $header = $configs['cores']['header'];
            $css .= ".navbar {\n";
            if (isset($header['cor_fundo'])) $css .= "  background-color: {$header['cor_fundo']};\n";
            if (isset($header['cor_texto'])) $css .= "  color: {$header['cor_texto']};\n";
            $css .= "}\n\n";
            
            $css .= ".navbar-nav .nav-link {\n";
            if (isset($header['cor_link'])) $css .= "  color: {$header['cor_link']};\n";
            $css .= "}\n\n";
            
            $css .= ".navbar-nav .nav-link:hover {\n";
            if (isset($header['cor_link_hover'])) $css .= "  color: {$header['cor_link_hover']};\n";
            $css .= "}\n\n";
        }
        
        // Cores do footer
        if (isset($configs['cores']['footer'])) {
            $footer = $configs['cores']['footer'];
            $css .= "footer {\n";
            if (isset($footer['cor_fundo'])) $css .= "  background-color: {$footer['cor_fundo']};\n";
            if (isset($footer['cor_texto'])) $css .= "  color: {$footer['cor_texto']};\n";
            $css .= "}\n\n";
            
            $css .= "footer a {\n";
            if (isset($footer['cor_link'])) $css .= "  color: {$footer['cor_link']};\n";
            $css .= "}\n\n";
        }
        
        // Fontes
        if (isset($configs['fontes']['site'])) {
            $fontes = $configs['fontes']['site'];
            $css .= "body {\n";
            if (isset($fontes['fonte_principal'])) $css .= "  font-family: {$fontes['fonte_principal']};\n";
            $css .= "}\n\n";
            
            $css .= "h1, h2, h3, h4, h5, h6 {\n";
            if (isset($fontes['fonte_titulos'])) $css .= "  font-family: {$fontes['fonte_titulos']};\n";
            $css .= "}\n\n";
            
            $css .= "p, div {\n";
            if (isset($fontes['fonte_texto'])) $css .= "  font-family: {$fontes['fonte_texto']};\n";
            $css .= "}\n\n";
        }
        
        // Tamanhos de fonte
        if (isset($configs['fontes'])) {
            foreach ($configs['fontes'] as $elemento => $props) {
                if ($elemento !== 'site' && isset($props['tamanho'])) {
                    $css .= ".{$elemento} {\n";
                    $css .= "  font-size: {$props['tamanho']};\n";
                    $css .= "}\n\n";
                }
            }
        }
        
        return $css;
    }
    
    // Salvar CSS em arquivo
    public function saveCSS($filepath = null) {
        if (!$filepath) {
            $filepath = __DIR__ . '/../assets/css/dynamic.css';
        }
        
        $css = $this->generateCSS();
        return file_put_contents($filepath, $css);
    }
} 