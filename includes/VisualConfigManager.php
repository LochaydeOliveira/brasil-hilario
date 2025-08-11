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
        $css = "/* CSS Gerado Dinamicamente - Sistema Completo de Configurações Visuais */\n\n";
        
        // =====================================================
        // VARIÁVEIS CSS ROOT
        // =====================================================
            $css .= ":root {\n";
        $css .= "  /* Cores principais do site */\n";
        $css .= "  --cor_primaria: #0b8103;\n";
        $css .= "  --cor_secundaria: #000000;\n";
        $css .= "  --cor_sucesso: #28a745;\n";
        $css .= "  --cor_perigo: #dc3545;\n";
        $css .= "  --cor_aviso: #ffc107;\n";
        $css .= "  --cor_info: #17a2b8;\n";
        $css .= "}\n\n";
        
        // =====================================================
        // HEADER - CONFIGURAÇÕES COMPLETAS
        // =====================================================
        $css .= "/* =====================================================\n";
        $css .= "   HEADER - CONFIGURAÇÕES COMPLETAS\n";
        $css .= "   ===================================================== */\n";
        
        // Background do header
        $corHeaderBg = $this->getConfigValue('header', 'header', 'cor_background', '#f8f9f4');
        $css .= "body .header {\n";
        $css .= "  background-color: {$corHeaderBg} !important;\n";
        $css .= "}\n\n";
        
        // Título do site
        $fonteTitulo = $this->getConfigValue('header', 'header', 'fonte_titulo', '"Merriweather", serif');
        $tamanhoTituloDesktop = $this->getConfigValue('header', 'header', 'tamanho_titulo_desktop', '28px');
        $tamanhoTituloMobile = $this->getConfigValue('header', 'header', 'tamanho_titulo_mobile', '24px');
        $pesoTitulo = $this->getConfigValue('header', 'header', 'peso_titulo', '700');
        $corTitulo = $this->getConfigValue('header', 'header', 'cor_titulo', '#333333');
        
        $css .= "body .site-title {\n";
        $css .= "  font-family: {$fonteTitulo} !important;\n";
        $css .= "  font-size: {$tamanhoTituloDesktop} !important;\n";
        $css .= "  font-weight: {$pesoTitulo} !important;\n";
        $css .= "  color: {$corTitulo} !important;\n";
        $css .= "}\n\n";
        
        $css .= "@media (max-width: 768px) {\n";
        $css .= "  body .site-title {\n";
        $css .= "    font-size: {$tamanhoTituloMobile} !important;\n";
        $css .= "  }\n";
        $css .= "}\n\n";
        
        // Logo
        $fonteLogo = $this->getConfigValue('header', 'header', 'fonte_logo', '"Merriweather", serif');
        $tamanhoLogoDesktop = $this->getConfigValue('header', 'header', 'tamanho_logo_desktop', '24px');
        $tamanhoLogoMobile = $this->getConfigValue('header', 'header', 'tamanho_logo_mobile', '20px');
        $pesoLogo = $this->getConfigValue('header', 'header', 'peso_logo', '700');
        $corLogo = $this->getConfigValue('header', 'header', 'cor_logo', '#0b8103');
        
        $css .= "body .site-logo {\n";
        $css .= "  font-family: {$fonteLogo} !important;\n";
        $css .= "  font-size: {$tamanhoLogoDesktop} !important;\n";
        $css .= "  font-weight: {$pesoLogo} !important;\n";
        $css .= "  color: {$corLogo} !important;\n";
            $css .= "}\n\n";
        
        $css .= "@media (max-width: 768px) {\n";
        $css .= "  body .site-logo {\n";
        $css .= "    font-size: {$tamanhoLogoMobile} !important;\n";
        $css .= "  }\n";
            $css .= "}\n\n";
            
        // =====================================================
        // NAVBAR - CONFIGURAÇÕES COMPLETAS
        // =====================================================
        $css .= "/* =====================================================\n";
        $css .= "   NAVBAR - CONFIGURAÇÕES COMPLETAS\n";
        $css .= "   ===================================================== */\n";
        
        // Background da navbar
        $corNavbarBg = $this->getConfigValue('navbar', 'navbar', 'cor_background', '#ffffff');
        $css .= "body .navbar {\n";
        $css .= "  background-color: {$corNavbarBg} !important;\n";
            $css .= "}\n\n";
            
        // Links da navbar
        $fonteLinks = $this->getConfigValue('navbar', 'navbar', 'fonte_links', '"Inter", sans-serif');
        $tamanhoLinksDesktop = $this->getConfigValue('navbar', 'navbar', 'tamanho_links_desktop', '14px');
        $tamanhoLinksMobile = $this->getConfigValue('navbar', 'navbar', 'tamanho_links_mobile', '12px');
        $pesoLinks = $this->getConfigValue('navbar', 'navbar', 'peso_links', '500');
        $corLinks = $this->getConfigValue('navbar', 'navbar', 'cor_links', '#5c5c5c');
        $corLinksHover = $this->getConfigValue('navbar', 'navbar', 'cor_links_hover', '#0b8103');
        
        $css .= "body .navbar .nav-link {\n";
        $css .= "  font-family: {$fonteLinks} !important;\n";
        $css .= "  font-size: {$tamanhoLinksDesktop} !important;\n";
        $css .= "  font-weight: {$pesoLinks} !important;\n";
        $css .= "  color: {$corLinks} !important;\n";
            $css .= "}\n\n";
        
        $css .= "body .navbar .nav-link:hover {\n";
        $css .= "  color: {$corLinksHover} !important;\n";
            $css .= "}\n\n";
            
        $css .= "@media (max-width: 768px) {\n";
        $css .= "  body .navbar .nav-link {\n";
        $css .= "    font-size: {$tamanhoLinksMobile} !important;\n";
        $css .= "  }\n";
            $css .= "}\n\n";
        
        // =====================================================
        // MAIN - CONFIGURAÇÕES COMPLETAS
        // =====================================================
        $css .= "/* =====================================================\n";
        $css .= "   MAIN - CONFIGURAÇÕES COMPLETAS\n";
        $css .= "   ===================================================== */\n";
        
        // Títulos dos posts
        $fonteTitulosPosts = $this->getConfigValue('main', 'titulos_posts', 'fonte', '"Merriweather", serif');
        $tamanhoTitulosDesktop = $this->getConfigValue('main', 'titulos_posts', 'tamanho_desktop', '28px');
        $tamanhoTitulosMobile = $this->getConfigValue('main', 'titulos_posts', 'tamanho_mobile', '24px');
        $pesoTitulosPosts = $this->getConfigValue('main', 'titulos_posts', 'peso', '700');
        $corTitulosPosts = $this->getConfigValue('main', 'titulos_posts', 'cor', '#000000');
        
        $css .= "body .post-title {\n";
        $css .= "  font-family: {$fonteTitulosPosts} !important;\n";
        $css .= "  font-size: {$tamanhoTitulosDesktop} !important;\n";
        $css .= "  font-weight: {$pesoTitulosPosts} !important;\n";
        $css .= "  color: {$corTitulosPosts} !important;\n";
            $css .= "}\n\n";
            
        $css .= "@media (max-width: 768px) {\n";
        $css .= "  body .post-title {\n";
        $css .= "    font-size: {$tamanhoTitulosMobile} !important;\n";
        $css .= "  }\n";
            $css .= "}\n\n";
            
        // Parágrafos dos posts
        $fonteParagrafos = $this->getConfigValue('main', 'paragrafos_posts', 'fonte', '"Inter", sans-serif');
        $tamanhoParagrafosDesktop = $this->getConfigValue('main', 'paragrafos_posts', 'tamanho_desktop', '16px');
        $tamanhoParagrafosMobile = $this->getConfigValue('main', 'paragrafos_posts', 'tamanho_mobile', '14px');
        $pesoParagrafos = $this->getConfigValue('main', 'paragrafos_posts', 'peso', '400');
        $corParagrafos = $this->getConfigValue('main', 'paragrafos_posts', 'cor', '#333333');
        
        $css .= "body .post-content p {\n";
        $css .= "  font-family: {$fonteParagrafos} !important;\n";
        $css .= "  font-size: {$tamanhoParagrafosDesktop} !important;\n";
        $css .= "  font-weight: {$pesoParagrafos} !important;\n";
        $css .= "  color: {$corParagrafos} !important;\n";
            $css .= "}\n\n";
        
        $css .= "@media (max-width: 768px) {\n";
        $css .= "  body .post-content p {\n";
        $css .= "    font-size: {$tamanhoParagrafosMobile} !important;\n";
        $css .= "  }\n";
            $css .= "}\n\n";
        
        // =====================================================
        // SIDEBAR - CONFIGURAÇÕES COMPLETAS
        // =====================================================
        $css .= "/* =====================================================\n";
        $css .= "   SIDEBAR - CONFIGURAÇÕES COMPLETAS\n";
        $css .= "   ===================================================== */\n";
        
        // Background da sidebar
        $corSidebarBg = $this->getConfigValue('sidebar', 'sidebar', 'cor_background', '#f8f9fa');
        $css .= "body .sidebar {\n";
        $css .= "  background-color: {$corSidebarBg} !important;\n";
            $css .= "}\n\n";
        
        // Títulos das seções da sidebar
        $fonteTitulosSecoes = $this->getConfigValue('sidebar', 'titulos_secoes', 'fonte', '"Merriweather", serif');
        $tamanhoTitulosSecoesDesktop = $this->getConfigValue('sidebar', 'titulos_secoes', 'tamanho_desktop', '18px');
        $tamanhoTitulosSecoesMobile = $this->getConfigValue('sidebar', 'titulos_secoes', 'tamanho_mobile', '16px');
        $pesoTitulosSecoes = $this->getConfigValue('sidebar', 'titulos_secoes', 'peso', '700');
        $corTitulosSecoes = $this->getConfigValue('sidebar', 'titulos_secoes', 'cor', '#000000');
        
        $css .= "body .sidebar .widget-title {\n";
        $css .= "  font-family: {$fonteTitulosSecoes} !important;\n";
        $css .= "  font-size: {$tamanhoTitulosSecoesDesktop} !important;\n";
        $css .= "  font-weight: {$pesoTitulosSecoes} !important;\n";
        $css .= "  color: {$corTitulosSecoes} !important;\n";
                $css .= "}\n\n";
                
                $css .= "@media (max-width: 768px) {\n";
        $css .= "  body .sidebar .widget-title {\n";
        $css .= "    font-size: {$tamanhoTitulosSecoesMobile} !important;\n";
                $css .= "  }\n";
                $css .= "}\n\n";
        
        // =====================================================
        // LEIA TAMBÉM - CONFIGURAÇÕES COMPLETAS
        // =====================================================
        $css .= "/* =====================================================\n";
        $css .= "   LEIA TAMBÉM - CONFIGURAÇÕES COMPLETAS\n";
        $css .= "   ===================================================== */\n";
        
        // Background da seção
        $corLeiaTambemBg = $this->getConfigValue('leia_tambem', 'leia_tambem', 'cor_background', '#ffffff');
        $css .= "body .related-posts-block {\n";
        $css .= "  background-color: {$corLeiaTambemBg} !important;\n";
        $css .= "}\n\n";
        
        // Título da seção
        $fonteTituloLeiaTambem = $this->getConfigValue('leia_tambem', 'titulo_secao', 'fonte', '"Merriweather", serif');
        $tamanhoTituloLeiaTambemDesktop = $this->getConfigValue('leia_tambem', 'titulo_secao', 'tamanho_desktop', '22px');
        $tamanhoTituloLeiaTambemMobile = $this->getConfigValue('leia_tambem', 'titulo_secao', 'tamanho_mobile', '20px');
        $pesoTituloLeiaTambem = $this->getConfigValue('leia_tambem', 'titulo_secao', 'peso', '700');
        $corTituloLeiaTambem = $this->getConfigValue('leia_tambem', 'titulo_secao', 'cor', '#000000');
        
        $css .= "body .related-posts-title {\n";
        $css .= "  font-family: {$fonteTituloLeiaTambem} !important;\n";
        $css .= "  font-size: {$tamanhoTituloLeiaTambemDesktop} !important;\n";
        $css .= "  font-weight: {$pesoTituloLeiaTambem} !important;\n";
        $css .= "  color: {$corTituloLeiaTambem} !important;\n";
                $css .= "}\n\n";
                
                $css .= "@media (max-width: 768px) {\n";
        $css .= "  body .related-posts-title {\n";
        $css .= "    font-size: {$tamanhoTituloLeiaTambemMobile} !important;\n";
                $css .= "  }\n";
                $css .= "}\n\n";
        
        // Títulos dos posts relacionados
        $fonteTitulosPostsLeia = $this->getConfigValue('leia_tambem', 'titulos_posts', 'fonte', '"Merriweather", serif');
        $tamanhoTitulosPostsLeiaDesktop = $this->getConfigValue('leia_tambem', 'titulos_posts', 'tamanho_desktop', '16px');
        $tamanhoTitulosPostsLeiaMobile = $this->getConfigValue('leia_tambem', 'titulos_posts', 'tamanho_mobile', '14px');
        $pesoTitulosPostsLeia = $this->getConfigValue('leia_tambem', 'titulos_posts', 'peso', '600');
        $corTitulosPostsLeia = $this->getConfigValue('leia_tambem', 'titulos_posts', 'cor', '#333333');
        
        $css .= "body .related-post-title {\n";
        $css .= "  font-family: {$fonteTitulosPostsLeia} !important;\n";
        $css .= "  font-size: {$tamanhoTitulosPostsLeiaDesktop} !important;\n";
        $css .= "  font-weight: {$pesoTitulosPostsLeia} !important;\n";
        $css .= "  color: {$corTitulosPostsLeia} !important;\n";
                $css .= "}\n\n";
                
                $css .= "@media (max-width: 768px) {\n";
        $css .= "  body .related-post-title {\n";
        $css .= "    font-size: {$tamanhoTitulosPostsLeiaMobile} !important;\n";
                $css .= "  }\n";
                $css .= "}\n\n";
        
        // =====================================================
        // ÚLTIMAS DO PORTAL - CONFIGURAÇÕES COMPLETAS
        // =====================================================
        $css .= "/* =====================================================\n";
        $css .= "   ÚLTIMAS DO PORTAL - CONFIGURAÇÕES COMPLETAS\n";
        $css .= "   ===================================================== */\n";
        
        // Background da seção
        $corUltimasPortalBg = $this->getConfigValue('ultimas_portal', 'ultimas_portal', 'cor_background', '#ffffff');
        $css .= "body .latest-posts-block {\n";
        $css .= "  background-color: {$corUltimasPortalBg} !important;\n";
        $css .= "}\n\n";
        
        // Título da seção
        $fonteTituloUltimasPortal = $this->getConfigValue('ultimas_portal', 'titulo_secao', 'fonte', '"Merriweather", serif');
        $tamanhoTituloUltimasPortalDesktop = $this->getConfigValue('ultimas_portal', 'titulo_secao', 'tamanho_desktop', '22px');
        $tamanhoTituloUltimasPortalMobile = $this->getConfigValue('ultimas_portal', 'titulo_secao', 'tamanho_mobile', '20px');
        $pesoTituloUltimasPortal = $this->getConfigValue('ultimas_portal', 'titulo_secao', 'peso', '700');
        $corTituloUltimasPortal = $this->getConfigValue('ultimas_portal', 'titulo_secao', 'cor', '#000000');
        
        $css .= "body .latest-posts-title {\n";
        $css .= "  font-family: {$fonteTituloUltimasPortal} !important;\n";
        $css .= "  font-size: {$tamanhoTituloUltimasPortalDesktop} !important;\n";
        $css .= "  font-weight: {$pesoTituloUltimasPortal} !important;\n";
        $css .= "  color: {$corTituloUltimasPortal} !important;\n";
                $css .= "}\n\n";
                
                $css .= "@media (max-width: 768px) {\n";
        $css .= "  body .latest-posts-title {\n";
        $css .= "    font-size: {$tamanhoTituloUltimasPortalMobile} !important;\n";
                $css .= "  }\n";
                $css .= "}\n\n";
        
        // Tag da categoria
        $corTagCategoriaBg = $this->getConfigValue('ultimas_portal', 'tag_categoria', 'cor_background', '#0b8103');
        $fonteTagCategoria = $this->getConfigValue('ultimas_portal', 'tag_categoria', 'fonte', '"Inter", sans-serif');
        $tamanhoTagCategoriaDesktop = $this->getConfigValue('ultimas_portal', 'tag_categoria', 'tamanho_desktop', '10px');
        $tamanhoTagCategoriaMobile = $this->getConfigValue('ultimas_portal', 'tag_categoria', 'tamanho_mobile', '8px');
        $pesoTagCategoria = $this->getConfigValue('ultimas_portal', 'tag_categoria', 'peso', '500');
        $corTagCategoriaTexto = $this->getConfigValue('ultimas_portal', 'tag_categoria', 'cor_texto', '#ffffff');
        $bordaTagCategoria = $this->getConfigValue('ultimas_portal', 'tag_categoria', 'borda_arredondada', '15px');
        
        $css .= "body .category-tag {\n";
        $css .= "  background-color: {$corTagCategoriaBg} !important;\n";
        $css .= "  font-family: {$fonteTagCategoria} !important;\n";
        $css .= "  font-size: {$tamanhoTagCategoriaDesktop} !important;\n";
        $css .= "  font-weight: {$pesoTagCategoria} !important;\n";
        $css .= "  color: {$corTagCategoriaTexto} !important;\n";
        $css .= "  border-radius: {$bordaTagCategoria} !important;\n";
                $css .= "}\n\n";
                
                $css .= "@media (max-width: 768px) {\n";
        $css .= "  body .category-tag {\n";
        $css .= "    font-size: {$tamanhoTagCategoriaMobile} !important;\n";
                $css .= "  }\n";
                $css .= "}\n\n";
        
        // =====================================================
        // FOOTER - CONFIGURAÇÕES COMPLETAS
        // =====================================================
        $css .= "/* =====================================================\n";
        $css .= "   FOOTER - CONFIGURAÇÕES COMPLETAS\n";
        $css .= "   ===================================================== */\n";
        
        // Background do footer
        $corFooterBg = $this->getConfigValue('footer', 'footer', 'cor_background', '#f8f9fa');
        $css .= "body .footer {\n";
        $css .= "  background-color: {$corFooterBg} !important;\n";
        $css .= "}\n\n";
        
        // Títulos das seções do footer
        $fonteTitulosFooter = $this->getConfigValue('footer', 'titulos_secoes', 'fonte', '"Inter", sans-serif');
        $tamanhoTitulosFooterDesktop = $this->getConfigValue('footer', 'titulos_secoes', 'tamanho_desktop', '18px');
        $tamanhoTitulosFooterMobile = $this->getConfigValue('footer', 'titulos_secoes', 'tamanho_mobile', '16px');
        $pesoTitulosFooter = $this->getConfigValue('footer', 'titulos_secoes', 'peso', '700');
        $corTitulosFooter = $this->getConfigValue('footer', 'titulos_secoes', 'cor', '#000000');
        
        $css .= "body .footer .section-title {\n";
        $css .= "  font-family: {$fonteTitulosFooter} !important;\n";
        $css .= "  font-size: {$tamanhoTitulosFooterDesktop} !important;\n";
        $css .= "  font-weight: {$pesoTitulosFooter} !important;\n";
        $css .= "  color: {$corTitulosFooter} !important;\n";
                $css .= "}\n\n";
                
                $css .= "@media (max-width: 768px) {\n";
        $css .= "  body .footer .section-title {\n";
        $css .= "    font-size: {$tamanhoTitulosFooterMobile} !important;\n";
                $css .= "  }\n";
                $css .= "}\n\n";
        
        // =====================================================
        // CARDS - CONFIGURAÇÕES COMPLETAS
        // =====================================================
        $css .= "/* =====================================================\n";
        $css .= "   CARDS - CONFIGURAÇÕES COMPLETAS\n";
        $css .= "   ===================================================== */\n";
        
        // Background dos cards
        $corCardsBg = $this->getConfigValue('cards', 'cards', 'cor_background', '#ffffff');
        $corCardsBorda = $this->getConfigValue('cards', 'cards', 'cor_borda', '#dee2e6');
        $css .= "body .card {\n";
        $css .= "  background-color: {$corCardsBg} !important;\n";
        $css .= "  border-color: {$corCardsBorda} !important;\n";
        $css .= "}\n\n";
        
        // Títulos dos cards
        $fonteTitulosCards = $this->getConfigValue('cards', 'titulos_cards', 'fonte', '"Merriweather", serif');
        $tamanhoTitulosCardsDesktop = $this->getConfigValue('cards', 'titulos_cards', 'tamanho_desktop', '20px');
        $tamanhoTitulosCardsMobile = $this->getConfigValue('cards', 'titulos_cards', 'tamanho_mobile', '18px');
        $pesoTitulosCards = $this->getConfigValue('cards', 'titulos_cards', 'peso', '700');
        $corTitulosCards = $this->getConfigValue('cards', 'titulos_cards', 'cor', '#000000');
        
        $css .= "body .card .card-title {\n";
        $css .= "  font-family: {$fonteTitulosCards} !important;\n";
        $css .= "  font-size: {$tamanhoTitulosCardsDesktop} !important;\n";
        $css .= "  font-weight: {$pesoTitulosCards} !important;\n";
        $css .= "  color: {$corTitulosCards} !important;\n";
                $css .= "}\n\n";
                
                $css .= "@media (max-width: 768px) {\n";
        $css .= "  body .card .card-title {\n";
        $css .= "    font-size: {$tamanhoTitulosCardsMobile} !important;\n";
                $css .= "  }\n";
                $css .= "}\n\n";
        
        // =====================================================
        // BOTÕES - CONFIGURAÇÕES COMPLETAS
        // =====================================================
        $css .= "/* =====================================================\n";
        $css .= "   BOTÕES - CONFIGURAÇÕES COMPLETAS\n";
        $css .= "   ===================================================== */\n";
        
        // Botões primários
        $corBotaoPrimarioBg = $this->getConfigValue('botoes', 'botao_primario', 'cor_background', '#0b8103');
        $corBotaoPrimarioTexto = $this->getConfigValue('botoes', 'botao_primario', 'cor_texto', '#ffffff');
        $fonteBotaoPrimario = $this->getConfigValue('botoes', 'botao_primario', 'fonte', '"Inter", sans-serif');
        $tamanhoBotaoPrimarioDesktop = $this->getConfigValue('botoes', 'botao_primario', 'tamanho_desktop', '14px');
        $tamanhoBotaoPrimarioMobile = $this->getConfigValue('botoes', 'botao_primario', 'tamanho_mobile', '12px');
        $pesoBotaoPrimario = $this->getConfigValue('botoes', 'botao_primario', 'peso', '500');
        
        $css .= "body .btn-primary {\n";
        $css .= "  background-color: {$corBotaoPrimarioBg} !important;\n";
        $css .= "  color: {$corBotaoPrimarioTexto} !important;\n";
        $css .= "  font-family: {$fonteBotaoPrimario} !important;\n";
        $css .= "  font-size: {$tamanhoBotaoPrimarioDesktop} !important;\n";
        $css .= "  font-weight: {$pesoBotaoPrimario} !important;\n";
        $css .= "}\n\n";
        
        $css .= "@media (max-width: 768px) {\n";
        $css .= "  body .btn-primary {\n";
        $css .= "    font-size: {$tamanhoBotaoPrimarioMobile} !important;\n";
        $css .= "  }\n";
        $css .= "}\n\n";
        
        // =====================================================
        // BADGES - CONFIGURAÇÕES COMPLETAS
        // =====================================================
        $css .= "/* =====================================================\n";
        $css .= "   BADGES - CONFIGURAÇÕES COMPLETAS\n";
        $css .= "   ===================================================== */\n";
        
        // Badges de categoria
        $corBadgeCategoriaBg = $this->getConfigValue('badges', 'badge_categoria', 'cor_background', '#0b8103');
        $corBadgeCategoriaTexto = $this->getConfigValue('badges', 'badge_categoria', 'cor_texto', '#ffffff');
        $fonteBadgeCategoria = $this->getConfigValue('badges', 'badge_categoria', 'fonte', '"Inter", sans-serif');
        $tamanhoBadgeCategoriaDesktop = $this->getConfigValue('badges', 'badge_categoria', 'tamanho_desktop', '10px');
        $tamanhoBadgeCategoriaMobile = $this->getConfigValue('badges', 'badge_categoria', 'tamanho_mobile', '8px');
        $pesoBadgeCategoria = $this->getConfigValue('badges', 'badge_categoria', 'peso', '500');
        $bordaBadgeCategoria = $this->getConfigValue('badges', 'badge_categoria', 'borda_arredondada', '15px');
        
        $css .= "body .badge-category {\n";
        $css .= "  background-color: {$corBadgeCategoriaBg} !important;\n";
        $css .= "  color: {$corBadgeCategoriaTexto} !important;\n";
        $css .= "  font-family: {$fonteBadgeCategoria} !important;\n";
        $css .= "  font-size: {$tamanhoBadgeCategoriaDesktop} !important;\n";
        $css .= "  font-weight: {$pesoBadgeCategoria} !important;\n";
        $css .= "  border-radius: {$bordaBadgeCategoria} !important;\n";
        $css .= "}\n\n";
        
        $css .= "@media (max-width: 768px) {\n";
        $css .= "  body .badge-category {\n";
        $css .= "    font-size: {$tamanhoBadgeCategoriaMobile} !important;\n";
        $css .= "  }\n";
        $css .= "}\n\n";
        
        // =====================================================
        // RESPONSIVIDADE - CONFIGURAÇÕES COMPLETAS
        // =====================================================
        $css .= "/* =====================================================\n";
        $css .= "   RESPONSIVIDADE - CONFIGURAÇÕES COMPLETAS\n";
        $css .= "   ===================================================== */\n";
        
        $mobilePadding = $this->getConfigValue('responsividade', 'espacamentos', 'mobile_padding', '15px');
        $desktopPadding = $this->getConfigValue('responsividade', 'espacamentos', 'desktop_padding', '30px');
        $mobileMargin = $this->getConfigValue('responsividade', 'espacamentos', 'mobile_margin', '10px');
        $desktopMargin = $this->getConfigValue('responsividade', 'espacamentos', 'desktop_margin', '20px');
        
        $css .= "@media (max-width: 768px) {\n";
        $css .= "  body .container,\n";
        $css .= "  body .container-fluid {\n";
        $css .= "    padding: {$mobilePadding} !important;\n";
        $css .= "  }\n\n";
        $css .= "  body .row {\n";
        $css .= "    margin: {$mobileMargin} !important;\n";
        $css .= "  }\n";
        $css .= "}\n\n";
        
        $css .= "@media (min-width: 769px) {\n";
        $css .= "  body .container,\n";
        $css .= "  body .container-fluid {\n";
        $css .= "    padding: {$desktopPadding} !important;\n";
        $css .= "  }\n\n";
        $css .= "  body .row {\n";
        $css .= "    margin: {$desktopMargin} !important;\n";
        $css .= "  }\n";
        $css .= "}\n\n";
        
        // =====================================================
        // PRIORIDADE MÁXIMA - !important EM TODOS OS ELEMENTOS
        // =====================================================
        $css .= "/* =====================================================\n";
        $css .= "   PRIORIDADE MÁXIMA - !important EM TODOS OS ELEMENTOS\n";
        $css .= "   ===================================================== */\n\n";
        
        $css .= "/* Este CSS tem prioridade máxima sobre todos os outros estilos */\n";
        $css .= "/* Todas as configurações são aplicadas com !important */\n";
        $css .= "/* O prefixo 'body' garante especificidade máxima */\n";
        $css .= "/* Responsividade completa para mobile e desktop */\n";
        
        return $css;
    }
    
    // Método auxiliar para obter valores de configuração
    private function getConfigValue($categoria, $elemento, $propriedade, $padrao = '') {
        try {
            $stmt = $this->pdo->prepare("
                SELECT valor FROM configuracoes_visuais 
                WHERE categoria = ? AND elemento = ? AND propriedade = ? AND ativo = 1
            ");
            $stmt->execute([$categoria, $elemento, $propriedade]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['valor'] : $padrao;
        } catch (Exception $e) {
            error_log("Erro ao obter configuração: {$categoria}.{$elemento}.{$propriedade} - " . $e->getMessage());
            return $padrao;
        }
    }
    
    public function saveCSS($filepath = null) {
            if (!$filepath) {
                $filepath = __DIR__ . '/../assets/css/dynamic.css';
            }
            
        $css = $this->generateCSS();
        
        try {
            $resultado = file_put_contents($filepath, $css);
            return $resultado !== false;
        } catch (Exception $e) {
            error_log("Erro ao salvar CSS: " . $e->getMessage());
            return false;
        }
    }
} 