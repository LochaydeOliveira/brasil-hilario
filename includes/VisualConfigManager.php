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
    
    // Novos métodos para gerenciar fonte geral vs personalizada
    public function getFonteGeral() {
        return $this->getFonte('site', 'fonte_geral', 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif');
    }
    
    public function setFonteGeral($valor) {
        return $this->setFonte('site', 'fonte_geral', $valor);
    }
    
    public function usarFonteGeral() {
        $stmt = $this->pdo->prepare("
            SELECT valor FROM configuracoes_visuais 
            WHERE categoria = 'fontes' AND elemento = 'site' AND propriedade = 'usar_fonte_geral' AND ativo = 1
        ");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (bool)$row['valor'] : true;
    }
    
    public function setUsarFonteGeral($valor) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo) 
                VALUES ('fontes', 'site', 'usar_fonte_geral', ?, 'boolean')
                ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW()
            ");
            return $stmt->execute([$valor ? '1' : '0']);
        } catch (Exception $e) {
            error_log("Exceção ao salvar usar_fonte_geral: " . $e->getMessage());
            return false;
        }
    }
    
    public function personalizarFontes() {
        $stmt = $this->pdo->prepare("
            SELECT valor FROM configuracoes_visuais 
            WHERE categoria = 'fontes' AND elemento = 'site' AND propriedade = 'personalizar_fontes' AND ativo = 1
        ");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (bool)$row['valor'] : false;
    }
    
    public function setPersonalizarFontes($valor) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo) 
                VALUES ('fontes', 'site', 'personalizar_fontes', ?, 'boolean')
                ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW()
            ");
            return $stmt->execute([$valor ? '1' : '0']);
        } catch (Exception $e) {
            error_log("Exceção ao salvar personalizar_fontes: " . $e->getMessage());
            return false;
        }
    }
    
    // Métodos para gerenciar peso das fontes
    public function getPesoFonte($elemento, $padrao = '400') {
        $stmt = $this->pdo->prepare("
            SELECT valor FROM configuracoes_visuais 
            WHERE categoria = 'fontes' AND elemento = 'site' AND propriedade = ? AND ativo = 1
        ");
        $stmt->execute(['peso_' . $elemento]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['valor'] : $padrao;
    }
    
    public function setPesoFonte($elemento, $valor) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo) 
                VALUES ('fontes', 'site', ?, ?, 'texto')
                ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW()
            ");
            return $stmt->execute(['peso_' . $elemento, $valor]);
        } catch (Exception $e) {
            error_log("Exceção ao salvar peso fonte: " . $e->getMessage());
            return false;
        }
    }
    
    // Métodos para gerenciar tamanhos responsivos
    public function getTamanhoFonte($elemento, $dispositivo = 'desktop', $padrao = '16px') {
        $stmt = $this->pdo->prepare("
            SELECT valor FROM configuracoes_visuais 
            WHERE categoria = 'fontes' AND elemento = ? AND propriedade = ? AND ativo = 1
        ");
        $stmt->execute([$elemento, 'tamanho_' . $dispositivo]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['valor'] : $padrao;
    }
    
    public function setTamanhoFonte($elemento, $dispositivo, $valor) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo) 
                VALUES ('fontes', ?, ?, ?, 'texto')
                ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW()
            ");
            return $stmt->execute([$elemento, 'tamanho_' . $dispositivo, $valor]);
        } catch (Exception $e) {
            error_log("Exceção ao salvar tamanho fonte: " . $e->getMessage());
            return false;
        }
    }
    
    // Métodos para seções específicas do blog
    public function getFonteSecao($secao, $padrao = 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif') {
        return $this->getFonte($secao, 'fonte', $padrao);
    }
    
    public function setFonteSecao($secao, $valor) {
        return $this->setFonte($secao, 'fonte', $valor);
    }
    
    public function getPesoSecao($secao, $tipo = 'titulo', $padrao = '600') {
        $stmt = $this->pdo->prepare("
            SELECT valor FROM configuracoes_visuais 
            WHERE categoria = 'fontes' AND elemento = ? AND propriedade = ? AND ativo = 1
        ");
        $stmt->execute([$secao, 'peso_' . $tipo]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['valor'] : $padrao;
    }
    
    public function setPesoSecao($secao, $tipo, $valor) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo) 
                VALUES ('fontes', ?, ?, ?, 'texto')
                ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW()
            ");
            return $stmt->execute([$secao, 'peso_' . $tipo, $valor]);
        } catch (Exception $e) {
            error_log("Exceção ao salvar peso seção: " . $e->getMessage());
            return false;
        }
    }
    
    public function getTamanhoSecao($secao, $tipo = 'titulo', $dispositivo = 'desktop', $padrao = '22px') {
        $stmt = $this->pdo->prepare("
            SELECT valor FROM configuracoes_visuais 
            WHERE categoria = 'fontes' AND elemento = ? AND propriedade = ? AND ativo = 1
        ");
        $stmt->execute([$secao, 'tamanho_' . $tipo . '_' . $dispositivo]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['valor'] : $padrao;
    }
    
    public function setTamanhoSecao($secao, $tipo, $dispositivo, $valor) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo) 
                VALUES ('fontes', ?, ?, ?, 'texto')
                ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW()
            ");
            return $stmt->execute([$secao, 'tamanho_' . $tipo . '_' . $dispositivo, $valor]);
        } catch (Exception $e) {
            error_log("Exceção ao salvar tamanho seção: " . $e->getMessage());
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
            if (isset($card['cor_texto'])) $css .= "  color: {$card['cor_texto']};\n";
            $css .= "}\n\n";
        }
        
        // NOVA LÓGICA DE FONTES
        $usarFonteGeral = $this->usarFonteGeral();
        $personalizarFontes = $this->personalizarFontes();
        
        if ($usarFonteGeral && !$personalizarFontes) {
            // Modo: Fonte Geral
            $fonteGeral = $this->getFonteGeral();
            $css .= "/* Fonte Geral do Site */\n";
            $css .= "body, h1, h2, h3, h4, h5, h6, p, div, .navbar, .sidebar, .card, .btn {\n";
            $css .= "  font-family: {$fonteGeral};\n";
            $css .= "}\n\n";
        } else {
            // Modo: Fontes Personalizadas
            $css .= "/* Fontes Personalizadas */\n";
            
            // Títulos
            if (isset($configs['fontes']['titulos']['fonte'])) {
                $peso = $this->getPesoFonte('titulos', '700');
                $tamanhoDesktop = $this->getTamanhoFonte('titulos', 'desktop', '28px');
                $tamanhoMobile = $this->getTamanhoFonte('titulos', 'mobile', '24px');
                
                $css .= "h1, h2, h3, h4, h5, h6 {\n";
                $css .= "  font-family: {$configs['fontes']['titulos']['fonte']};\n";
                $css .= "  font-weight: {$peso};\n";
                $css .= "  font-size: {$tamanhoDesktop};\n";
                $css .= "}\n\n";
                
                $css .= "@media (max-width: 768px) {\n";
                $css .= "  h1, h2, h3, h4, h5, h6 {\n";
                $css .= "    font-size: {$tamanhoMobile};\n";
                $css .= "  }\n";
                $css .= "}\n\n";
            }
            
            // Parágrafos
            if (isset($configs['fontes']['paragrafos']['fonte'])) {
                $peso = $this->getPesoFonte('paragrafos', '400');
                $tamanhoDesktop = $this->getTamanhoFonte('paragrafos', 'desktop', '16px');
                $tamanhoMobile = $this->getTamanhoFonte('paragrafos', 'mobile', '14px');
                
                $css .= "p {\n";
                $css .= "  font-family: {$configs['fontes']['paragrafos']['fonte']};\n";
                $css .= "  font-weight: {$peso};\n";
                $css .= "  font-size: {$tamanhoDesktop};\n";
                $css .= "}\n\n";
                
                $css .= "@media (max-width: 768px) {\n";
                $css .= "  p {\n";
                $css .= "    font-size: {$tamanhoMobile};\n";
                $css .= "  }\n";
                $css .= "}\n\n";
            }
            
            // Navegação
            if (isset($configs['fontes']['navegacao']['fonte'])) {
                $peso = $this->getPesoFonte('navegacao', '500');
                $tamanhoDesktop = $this->getTamanhoFonte('navegacao', 'desktop', '14px');
                $tamanhoMobile = $this->getTamanhoFonte('navegacao', 'mobile', '12px');
                
                $css .= ".navbar, .navbar-nav .nav-link {\n";
                $css .= "  font-family: {$configs['fontes']['navegacao']['fonte']};\n";
                $css .= "  font-weight: {$peso};\n";
                $css .= "  font-size: {$tamanhoDesktop};\n";
                $css .= "}\n\n";
                
                $css .= "@media (max-width: 768px) {\n";
                $css .= "  .navbar, .navbar-nav .nav-link {\n";
                $css .= "    font-size: {$tamanhoMobile};\n";
                $css .= "  }\n";
                $css .= "}\n\n";
            }
            
            // Sidebar
            if (isset($configs['fontes']['sidebar']['fonte'])) {
                $peso = $this->getPesoFonte('sidebar', '400');
                $tamanhoDesktop = $this->getTamanhoFonte('sidebar', 'desktop', '14px');
                $tamanhoMobile = $this->getTamanhoFonte('sidebar', 'mobile', '12px');
                
                $css .= ".sidebar, .sidebar * {\n";
                $css .= "  font-family: {$configs['fontes']['sidebar']['fonte']};\n";
                $css .= "  font-weight: {$peso};\n";
                $css .= "  font-size: {$tamanhoDesktop};\n";
                $css .= "}\n\n";
                
                $css .= "@media (max-width: 768px) {\n";
                $css .= "  .sidebar, .sidebar * {\n";
                $css .= "    font-size: {$tamanhoMobile};\n";
                $css .= "  }\n";
                $css .= "}\n\n";
            }
            
            // Cards
            if (isset($configs['fontes']['cards']['fonte'])) {
                $peso = $this->getPesoFonte('cards', '400');
                $tamanhoDesktop = $this->getTamanhoFonte('cards', 'desktop', '14px');
                $tamanhoMobile = $this->getTamanhoFonte('cards', 'mobile', '12px');
                
                $css .= ".card, .card * {\n";
                $css .= "  font-family: {$configs['fontes']['cards']['fonte']};\n";
                $css .= "  font-weight: {$peso};\n";
                $css .= "  font-size: {$tamanhoDesktop};\n";
                $css .= "}\n\n";
                
                $css .= "@media (max-width: 768px) {\n";
                $css .= "  .card, .card * {\n";
                $css .= "    font-size: {$tamanhoMobile};\n";
                $css .= "  }\n";
                $css .= "}\n\n";
            }
            
            // Botões
            if (isset($configs['fontes']['botoes']['fonte'])) {
                $peso = $this->getPesoFonte('botoes', '500');
                $tamanhoDesktop = $this->getTamanhoFonte('botoes', 'desktop', '14px');
                $tamanhoMobile = $this->getTamanhoFonte('botoes', 'mobile', '12px');
                
                $css .= ".btn {\n";
                $css .= "  font-family: {$configs['fontes']['botoes']['fonte']};\n";
                $css .= "  font-weight: {$peso};\n";
                $css .= "  font-size: {$tamanhoDesktop};\n";
                $css .= "}\n\n";
                
                $css .= "@media (max-width: 768px) {\n";
                $css .= "  .btn {\n";
                $css .= "    font-size: {$tamanhoMobile};\n";
                $css .= "  }\n";
                $css .= "}\n\n";
            }
            
            // Meta textos
            if (isset($configs['fontes']['meta_textos']['fonte'])) {
                $peso = $this->getPesoFonte('meta_textos', '400');
                $tamanhoDesktop = $this->getTamanhoFonte('meta_textos', 'desktop', '12px');
                $tamanhoMobile = $this->getTamanhoFonte('meta_textos', 'mobile', '10px');
                
                $css .= ".text-muted, .meta-text, small {\n";
                $css .= "  font-family: {$configs['fontes']['meta_textos']['fonte']};\n";
                $css .= "  font-weight: {$peso};\n";
                $css .= "  font-size: {$tamanhoDesktop};\n";
                $css .= "}\n\n";
                
                $css .= "@media (max-width: 768px) {\n";
                $css .= "  .text-muted, .meta-text, small {\n";
                $css .= "    font-size: {$tamanhoMobile};\n";
                $css .= "  }\n";
                $css .= "}\n\n";
            }
        }
        
        // CSS para seções específicas do blog (independente do modo de fonte)
        $css .= "/* Seções Específicas do Blog */\n";
        
        // Seção "Leia Também"
        $fonteLeiaTambem = $this->getFonteSecao('leia_tambem');
        $pesoTituloLeiaTambem = $this->getPesoSecao('leia_tambem', 'titulo', '600');
        $tamanhoTituloLeiaTambemDesktop = $this->getTamanhoSecao('leia_tambem', 'titulo', 'desktop', '22px');
        $tamanhoTituloLeiaTambemMobile = $this->getTamanhoSecao('leia_tambem', 'titulo', 'mobile', '20px');
        $pesoTextoLeiaTambem = $this->getPesoSecao('leia_tambem', 'texto', '400');
        $tamanhoTextoLeiaTambemDesktop = $this->getTamanhoSecao('leia_tambem', 'texto', 'desktop', '14px');
        $tamanhoTextoLeiaTambemMobile = $this->getTamanhoSecao('leia_tambem', 'texto', 'mobile', '12px');
        
        $css .= ".related-posts-title {\n";
        $css .= "  font-family: {$fonteLeiaTambem};\n";
        $css .= "  font-weight: {$pesoTituloLeiaTambem};\n";
        $css .= "  font-size: {$tamanhoTituloLeiaTambemDesktop};\n";
        $css .= "}\n\n";
        
        $css .= ".related-post-title {\n";
        $css .= "  font-family: {$fonteLeiaTambem};\n";
        $css .= "  font-weight: {$pesoTextoLeiaTambem};\n";
        $css .= "  font-size: {$tamanhoTextoLeiaTambemDesktop};\n";
        $css .= "}\n\n";
        
        $css .= "@media (max-width: 768px) {\n";
        $css .= "  .related-posts-title {\n";
        $css .= "    font-size: {$tamanhoTituloLeiaTambemMobile};\n";
        $css .= "  }\n";
        $css .= "  .related-post-title {\n";
        $css .= "    font-size: {$tamanhoTextoLeiaTambemMobile};\n";
        $css .= "  }\n";
        $css .= "}\n\n";
        
        // Seção "Últimas do Portal" (usa as mesmas classes CSS)
        $fonteUltimasPortal = $this->getFonteSecao('ultimas_portal');
        $pesoTituloUltimasPortal = $this->getPesoSecao('ultimas_portal', 'titulo', '600');
        $tamanhoTituloUltimasPortalDesktop = $this->getTamanhoSecao('ultimas_portal', 'titulo', 'desktop', '22px');
        $tamanhoTituloUltimasPortalMobile = $this->getTamanhoSecao('ultimas_portal', 'titulo', 'mobile', '20px');
        $pesoTextoUltimasPortal = $this->getPesoSecao('ultimas_portal', 'texto', '400');
        $tamanhoTextoUltimasPortalDesktop = $this->getTamanhoSecao('ultimas_portal', 'texto', 'desktop', '14px');
        $tamanhoTextoUltimasPortalMobile = $this->getTamanhoSecao('ultimas_portal', 'texto', 'mobile', '12px');
        
        // Como ambas as seções usam as mesmas classes CSS, vamos sobrescrever com as configurações específicas
        // quando necessário. Por padrão, elas herdam as configurações da seção "Leia Também"
        
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