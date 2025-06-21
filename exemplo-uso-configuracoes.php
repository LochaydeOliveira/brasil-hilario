<?php
/**
 * Exemplo de como usar as configurações do site
 * Este arquivo mostra como substituir as constantes hardcoded por valores dinâmicos
 */

// Inclui as classes necessárias
require_once 'config/config.php';
require_once 'includes/db.php';
require_once 'includes/SiteConfig.php';

// Inicializa as configurações do site
$siteConfig = SiteConfig::getInstance();

// Exemplo de como usar no header.php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php echo $siteConfig->generateMetaTags('Página Inicial'); ?>
    
    <!-- CSS customizado baseado nas configurações -->
    <style>
        <?php echo $siteConfig->generateCustomCSS(); ?>
    </style>
    
    <!-- CSS principal -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Favicon dinâmico -->
    <link rel="icon" type="image/x-icon" href="<?php echo $siteConfig->getFaviconUrl(); ?>">
    
    <!-- Códigos de integração do head -->
    <?php echo $siteConfig->generateHeadCodes(); ?>
</head>
<body>
    <!-- Header com logo dinâmico -->
    <header class="site-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <a href="<?php echo $siteConfig->getSiteUrl(); ?>" class="site-title">
                        <img src="<?php echo $siteConfig->getLogoUrl(); ?>" alt="<?php echo $siteConfig->getSiteTitle(); ?>" class="logo-img">
                    </a>
                </div>
                <div class="col-md-6">
                    <p class="lead"><?php echo $siteConfig->getSiteDescription(); ?></p>
                </div>
            </div>
        </div>
    </header>

    <!-- Conteúdo principal -->
    <main class="container">
        <h1>Bem-vindo ao <?php echo $siteConfig->getSiteTitle(); ?></h1>
        <p><?php echo $siteConfig->getSiteDescription(); ?></p>
        
        <!-- Exemplo de posts com paginação dinâmica -->
        <?php
        $postsPerPage = $siteConfig->getPostsPerPage();
        echo "<p>Mostrando {$postsPerPage} posts por página</p>";
        ?>
        
        <!-- Exemplo de newsletter condicional -->
        <?php if ($siteConfig->isNewsletterActive()): ?>
        <div class="newsletter-section">
            <h3><?php echo $siteConfig->getNewsletterTitle(); ?></h3>
            <p><?php echo $siteConfig->getNewsletterDescription(); ?></p>
            <!-- Formulário de newsletter aqui -->
        </div>
        <?php endif; ?>
        
        <!-- Exemplo de comentários condicionais -->
        <?php if ($siteConfig->isCommentsActive()): ?>
        <div class="comments-section">
            <h3>Comentários</h3>
            <!-- Seção de comentários aqui -->
        </div>
        <?php endif; ?>
    </main>

    <!-- Footer com links de redes sociais -->
    <footer class="site-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo $siteConfig->getSiteTitle(); ?></h5>
                    <p><?php echo $siteConfig->getSiteDescription(); ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Redes Sociais</h5>
                    <div class="social-links">
                        <?php
                        $socialLinks = $siteConfig->getSocialLinks();
                        foreach ($socialLinks as $platform => $url):
                            if (!empty($url)):
                        ?>
                        <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" class="social-icon">
                            <i class="fab fa-<?php echo strtolower($platform); ?>"></i>
                        </a>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Links de páginas dinâmicos -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="footer-links">
                        <?php
                        $pageConfigs = $siteConfig->getPageConfigs();
                        foreach ($pageConfigs as $key => $value):
                            if (strpos($key, '_titulo') !== false):
                                $urlKey = str_replace('_titulo', '_url', $key);
                                $url = $pageConfigs[$urlKey] ?? '';
                                if (!empty($url)):
                        ?>
                        <a href="<?php echo $siteConfig->getSiteUrl(); ?>/<?php echo $url; ?>">
                            <?php echo htmlspecialchars($value); ?>
                        </a>
                        <?php 
                                endif;
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Códigos de integração do body -->
    <?php echo $siteConfig->generateBodyCodes(); ?>
</body>
</html>

<?php
/**
 * Exemplo de como usar em outros arquivos PHP
 */

// Exemplo 1: Obtendo uma configuração específica
$siteTitle = $siteConfig->getSiteTitle();
$primaryColor = $siteConfig->getPrimaryColor();

// Exemplo 2: Verificando se uma funcionalidade está ativa
if ($siteConfig->isCommentsActive()) {
    // Mostrar seção de comentários
    echo "Comentários ativos";
} else {
    // Ocultar seção de comentários
    echo "Comentários desativados";
}

// Exemplo 3: Obtendo todas as configurações de um grupo
$seoConfigs = $siteConfig->getGroup('seo');
foreach ($seoConfigs as $key => $value) {
    echo "SEO {$key}: {$value}\n";
}

// Exemplo 4: Usando em consultas de banco de dados
$postsPerPage = $siteConfig->getPostsPerPage();
$offset = ($currentPage - 1) * $postsPerPage;

// Exemplo de query com LIMIT dinâmico
$query = "SELECT * FROM posts WHERE publicado = 1 ORDER BY data_publicacao DESC LIMIT {$postsPerPage} OFFSET {$offset}";

// Exemplo 5: Gerando meta tags para uma página específica
$metaTags = $siteConfig->generateMetaTags(
    'Título da Página', 
    'Descrição específica da página', 
    'palavras, chave, específicas'
);

// Exemplo 6: Aplicando cores customizadas via CSS
$customCSS = $siteConfig->generateCustomCSS();
echo "<style>{$customCSS}</style>";

/**
 * Exemplo de como migrar do sistema atual para o novo
 * 
 * ANTES (usando constantes):
 * echo BLOG_TITLE;
 * echo BLOG_DESCRIPTION;
 * echo BLOG_URL;
 * 
 * DEPOIS (usando configurações dinâmicas):
 * echo $siteConfig->getSiteTitle();
 * echo $siteConfig->getSiteDescription();
 * echo $siteConfig->getSiteUrl();
 * 
 * VANTAGENS:
 * - Configurações podem ser alteradas sem editar código
 * - Interface amigável no painel admin
 * - Valores são salvos no banco de dados
 * - Suporte a diferentes tipos de dados (string, integer, boolean, json)
 * - Organização por grupos
 * - Fallbacks para valores padrão
 */
?> 