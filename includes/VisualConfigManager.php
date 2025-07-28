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
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo) 
                VALUES ('cores', ?, ?, ?, 'cor')
                ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW()
            ");
            $resultado = $stmt->execute([$elemento, $propriedade, $valor]);
            
            if (!$resultado) {
                error_log("Erro ao salvar cor: {$elemento}.{$propriedade} = {$valor}");
            }
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Exceção ao salvar cor: " . $e->getMessage());
            return false;
        }
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
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo) 
                VALUES ('fontes', ?, ?, ?, 'fonte')
                ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW()
            ");
            $resultado = $stmt->execute([$elemento, $propriedade, $valor]);
            
            if (!$resultado) {
                error_log("Erro ao salvar fonte: {$elemento}.{$propriedade} = {$valor}");
            }
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Exceção ao salvar fonte: " . $e->getMessage());
            return false;
        }
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
        
        // Cores da paginação
        if (isset($configs['cores']['paginacao'])) {
            $paginacao = $configs['cores']['paginacao'];
            $css .= ".pagination {\n";
            if (isset($paginacao['cor_fundo'])) $css .= "  background-color: {$paginacao['cor_fundo']} !important;\n";
            $css .= "}\n\n";
            
            $css .= ".page-link {\n";
            if (isset($paginacao['cor_link'])) $css .= "  color: {$paginacao['cor_link']} !important;\n";
            if (isset($paginacao['cor_fundo'])) $css .= "  background-color: {$paginacao['cor_fundo']} !important;\n";
            $css .= "  border-color: {$paginacao['cor_link']} !important;\n";
            $css .= "}\n\n";
            
            $css .= ".page-link:hover {\n";
            if (isset($paginacao['cor_link'])) $css .= "  color: #ffffff !important;\n";
            if (isset($paginacao['cor_link'])) $css .= "  background-color: {$paginacao['cor_link']} !important;\n";
            $css .= "  border-color: {$paginacao['cor_link']} !important;\n";
            $css .= "}\n\n";
            
            $css .= ".page-item.active .page-link {\n";
            if (isset($paginacao['cor_ativa'])) $css .= "  background-color: {$paginacao['cor_ativa']} !important;\n";
            if (isset($paginacao['cor_texto'])) $css .= "  color: {$paginacao['cor_texto']} !important;\n";
            $css .= "  border-color: {$paginacao['cor_ativa']} !important;\n";
            $css .= "}\n\n";
            
            $css .= ".page-item.disabled .page-link {\n";
            if (isset($paginacao['cor_link'])) $css .= "  color: {$paginacao['cor_link']} !important;\n";
            $css .= "  opacity: 0.6;\n";
            $css .= "}\n\n";
        }
        
        // Cores dos botões
        if (isset($configs['cores']['botao'])) {
            $botao = $configs['cores']['botao'];
            $css .= ".btn-primary {\n";
            if (isset($botao['cor_primario'])) $css .= "  background-color: {$botao['cor_primario']};\n";
            $css .= "}\n\n";
            
            $css .= ".btn-secondary {\n";
            if (isset($botao['cor_secundario'])) $css .= "  background-color: {$botao['cor_secundario']};\n";
            $css .= "}\n\n";
            
            $css .= ".btn-success {\n";
            if (isset($botao['cor_sucesso'])) $css .= "  background-color: {$botao['cor_sucesso']};\n";
            $css .= "}\n\n";
        }
        
        // Cores dos cards
        if (isset($configs['cores']['card'])) {
            $card = $configs['cores']['card'];
            $css .= ".card {\n";
            if (isset($card['cor_fundo'])) $css .= "  background-color: {$card['cor_fundo']};\n";
            if (isset($card['cor_borda'])) $css .= "  border-color: {$card['cor_borda']};\n";
            $css .= "}\n\n";
            
            $css .= ".card-body {\n";
            if (isset($card['cor_texto'])) $css .= "  color: {$card['cor_texto']};\n";
            $css .= "}\n\n";
        }
        
        // Fontes
        if (isset($configs['fontes'])) {
            // Fonte principal do site
            if (isset($configs['fontes']['site']['fonte'])) {
                $css .= "body {\n";
                $css .= "  font-family: {$configs['fontes']['site']['fonte']};\n";
                $css .= "}\n\n";
            }
            
            // Fonte dos títulos
            if (isset($configs['fontes']['titulo']['fonte'])) {
                $css .= "h1, h2, h3, h4, h5, h6 {\n";
                $css .= "  font-family: {$configs['fontes']['titulo']['fonte']};\n";
                $css .= "}\n\n";
            }
            
            // Fonte dos parágrafos
            if (isset($configs['fontes']['paragrafo']['fonte'])) {
                $css .= "p {\n";
                $css .= "  font-family: {$configs['fontes']['paragrafo']['fonte']};\n";
                $css .= "}\n\n";
            }
            
            // Tamanhos de fonte
            if (isset($configs['fontes']['titulo']['tamanho'])) {
                $css .= "h1, h2, h3, h4, h5, h6 {\n";
                $css .= "  font-size: {$configs['fontes']['titulo']['tamanho']};\n";
                $css .= "}\n\n";
            }
            
            if (isset($configs['fontes']['paragrafo']['tamanho'])) {
                $css .= "p {\n";
                $css .= "  font-size: {$configs['fontes']['paragrafo']['tamanho']};\n";
                $css .= "}\n\n";
            }
        }
        
        return $css;
    }
    
    // Salvar CSS em arquivo
    public function saveCSS($filepath = null) {
        try {
            if (!$filepath) {
                $filepath = __DIR__ . '/../assets/css/dynamic.css';
            }
            
            // Garantir que o diretório existe
            $dir = dirname($filepath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            $css = $this->generateCSS();
            $resultado = file_put_contents($filepath, $css);
            
            if ($resultado === false) {
                error_log("Erro ao salvar CSS em: {$filepath}");
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Exceção ao salvar CSS: " . $e->getMessage());
            return false;
        }
    }
} 