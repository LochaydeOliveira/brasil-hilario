<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/ConfigManager.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$configManager = new ConfigManager($conn);

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grupo = $_POST['grupo'] ?? 'geral';
    
    foreach ($_POST as $chave => $valor) {
        if ($chave !== 'grupo' && $chave !== 'submit') {
            $configManager->set($chave, $valor, 'string', $grupo);
        }
    }
    
    $mensagem = 'Configurações atualizadas com sucesso!';
    $tipo_mensagem = 'success';
}

// Obter configurações por grupo
$grupos = ['geral', 'seo', 'redes_sociais', 'integracao', 'paginas'];
$configuracoes = [];

foreach ($grupos as $grupo) {
    $configuracoes[$grupo] = $configManager->getGroup($grupo);
}

// Função auxiliar para obter valor de configuração
function getConfig($configs, $grupo, $chave, $padrao = '') {
    if (isset($configs[$grupo][$chave])) {
        return $configs[$grupo][$chave]['valor'];
    }
    return $padrao;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - Painel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>
            
            <!-- Conteúdo Principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-cogs"></i> Configurações do Site
                    </h1>
                </div>
                
                <?php if (isset($mensagem)): ?>
                    <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert">
                        <?= $mensagem ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Abas de Configurações -->
                <ul class="nav nav-tabs" id="configTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="geral-tab" data-bs-toggle="tab" data-bs-target="#geral" type="button" role="tab">
                            <i class="fas fa-home"></i> Geral
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                            <i class="fas fa-search"></i> SEO
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="redes-tab" data-bs-toggle="tab" data-bs-target="#redes" type="button" role="tab">
                            <i class="fab fa-facebook"></i> Redes Sociais
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="integracao-tab" data-bs-toggle="tab" data-bs-target="#integracao" type="button" role="tab">
                            <i class="fas fa-code"></i> Integração
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="paginas-tab" data-bs-toggle="tab" data-bs-target="#paginas" type="button" role="tab">
                            <i class="fas fa-file-alt"></i> Páginas
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="configTabsContent">
                    <!-- Aba Geral -->
                    <div class="tab-pane fade show active" id="geral" role="tabpanel">
                        <form method="POST" class="mt-4">
                            <input type="hidden" name="grupo" value="geral">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_title" class="form-label">Título do Site</label>
                                        <input type="text" class="form-control" id="site_title" name="site_title" 
                                               value="<?= getConfig($configuracoes, 'geral', 'site_title', 'Brasil Hilário') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="admin_email" class="form-label">Email do Administrador</label>
                                        <input type="email" class="form-control" id="admin_email" name="admin_email" 
                                               value="<?= getConfig($configuracoes, 'geral', 'admin_email', 'admin@brasilhilario.com.br') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_url" class="form-label">URL do Site</label>
                                        <input type="url" class="form-control" id="site_url" name="site_url" 
                                               value="<?= getConfig($configuracoes, 'geral', 'site_url', 'https://brasilhilario.com.br') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="posts_per_page" class="form-label">Posts por Página</label>
                                        <input type="number" class="form-control" id="posts_per_page" name="posts_per_page" 
                                               value="<?= getConfig($configuracoes, 'geral', 'posts_per_page', '10') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="primary_color" class="form-label">Cor Primária</label>
                                        <input type="color" class="form-control" id="primary_color" name="primary_color" 
                                               value="<?= getConfig($configuracoes, 'geral', 'primary_color', '#0b8103') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="secondary_color" class="form-label">Cor Secundária</label>
                                        <input type="color" class="form-control" id="secondary_color" name="secondary_color" 
                                               value="<?= getConfig($configuracoes, 'geral', 'secondary_color', '#b30606') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="logo_url" class="form-label">URL do Logo</label>
                                        <input type="text" class="form-control" id="logo_url" name="logo_url" 
                                               value="<?= getConfig($configuracoes, 'geral', 'logo_url', 'assets/images/logo-brasil-hilario-quadrada-svg.svg') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="favicon_url" class="form-label">URL do Favicon</label>
                                        <input type="text" class="form-control" id="favicon_url" name="favicon_url" 
                                               value="<?= getConfig($configuracoes, 'geral', 'favicon_url', 'assets/images/favicon.ico') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="site_description" class="form-label">Descrição do Site</label>
                                <textarea class="form-control" id="site_description" name="site_description" rows="3"><?= getConfig($configuracoes, 'geral', 'site_description', 'O melhor do humor brasileiro') ?></textarea>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="comments_active" name="comments_active" value="1" 
                                       <?= getConfig($configuracoes, 'geral', 'comments_active', '1') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="comments_active">
                                    Comentários Ativos
                                </label>
                            </div>
                            
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Configurações Gerais
                            </button>
                        </form>
                    </div>
                    
                    <!-- Aba SEO -->
                    <div class="tab-pane fade" id="seo" role="tabpanel">
                        <form method="POST" class="mt-4">
                            <input type="hidden" name="grupo" value="seo">
                            
                            <div class="mb-3">
                                <label for="meta_keywords" class="form-label">Palavras-chave (Meta Keywords)</label>
                                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                       value="<?= getConfig($configuracoes, 'seo', 'meta_keywords', 'humor, brasileiro, piadas, memes, comédia') ?>">
                                <div class="form-text">Separe as palavras-chave por vírgula</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="og_image_default" class="form-label">Imagem Padrão para Redes Sociais</label>
                                <input type="text" class="form-control" id="og_image_default" name="og_image_default" 
                                       value="<?= getConfig($configuracoes, 'seo', 'og_image_default', 'assets/images/og-image-default.jpg') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="google_analytics_id" class="form-label">ID do Google Analytics</label>
                                <input type="text" class="form-control" id="google_analytics_id" name="google_analytics_id" 
                                       value="<?= getConfig($configuracoes, 'seo', 'google_analytics_id', '') ?>">
                                <div class="form-text">Ex: G-XXXXXXXXXX ou UA-XXXXXXXXX-X</div>
                            </div>
                            
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Configurações SEO
                            </button>
                        </form>
                    </div>
                    
                    <!-- Aba Redes Sociais -->
                    <div class="tab-pane fade" id="redes" role="tabpanel">
                        <form method="POST" class="mt-4">
                            <input type="hidden" name="grupo" value="redes_sociais">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="facebook_url" class="form-label">URL do Facebook</label>
                                        <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                                               value="<?= getConfig($configuracoes, 'redes_sociais', 'facebook_url', '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="instagram_url" class="form-label">URL do Instagram</label>
                                        <input type="url" class="form-control" id="instagram_url" name="instagram_url" 
                                               value="<?= getConfig($configuracoes, 'redes_sociais', 'instagram_url', '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="twitter_url" class="form-label">URL do Twitter</label>
                                        <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                                               value="<?= getConfig($configuracoes, 'redes_sociais', 'twitter_url', '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="youtube_url" class="form-label">URL do YouTube</label>
                                        <input type="url" class="form-control" id="youtube_url" name="youtube_url" 
                                               value="<?= getConfig($configuracoes, 'redes_sociais', 'youtube_url', '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tiktok_url" class="form-label">URL do TikTok</label>
                                        <input type="url" class="form-control" id="tiktok_url" name="tiktok_url" 
                                               value="<?= getConfig($configuracoes, 'redes_sociais', 'tiktok_url', '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="telegram_url" class="form-label">URL do Telegram</label>
                                        <input type="url" class="form-control" id="telegram_url" name="telegram_url" 
                                               value="<?= getConfig($configuracoes, 'redes_sociais', 'telegram_url', '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Redes Sociais
                            </button>
                        </form>
                    </div>
                    
                    <!-- Aba Integração -->
                    <div class="tab-pane fade" id="integracao" role="tabpanel">
                        <form method="POST" class="mt-4">
                            <input type="hidden" name="grupo" value="integracao">
                            
                            <div class="mb-3">
                                <label for="head_code" class="form-label">Código para o Head</label>
                                <textarea class="form-control" id="head_code" name="head_code" rows="6" placeholder="Cole aqui códigos que devem aparecer no <head> da página"><?= getConfig($configuracoes, 'integracao', 'head_code', '') ?></textarea>
                                <div class="form-text">Ex: Meta tags, scripts de analytics, etc.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="body_code" class="form-label">Código para o Body</label>
                                <textarea class="form-control" id="body_code" name="body_code" rows="6" placeholder="Cole aqui códigos que devem aparecer no final do <body>"><?= getConfig($configuracoes, 'integracao', 'body_code', '') ?></textarea>
                                <div class="form-text">Ex: Chat widgets, pixels de rastreamento, etc.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="adsense_code" class="form-label">Código do Google AdSense</label>
                                <textarea class="form-control" id="adsense_code" name="adsense_code" rows="4" placeholder="Cole aqui o código do Google AdSense"><?= getConfig($configuracoes, 'integracao', 'adsense_code', '') ?></textarea>
                            </div>
                            
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Códigos de Integração
                            </button>
                        </form>
                    </div>
                    
                    <!-- Aba Páginas -->
                    <div class="tab-pane fade" id="paginas" role="tabpanel">
                        <form method="POST" class="mt-4">
                            <input type="hidden" name="grupo" value="paginas">
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="newsletter_active" name="newsletter_active" value="1" 
                                       <?= getConfig($configuracoes, 'paginas', 'newsletter_active', '1') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="newsletter_active">
                                    Newsletter Ativa
                                </label>
                            </div>
                            
                            <div class="mb-3">
                                <label for="newsletter_title" class="form-label">Título da Newsletter</label>
                                <input type="text" class="form-control" id="newsletter_title" name="newsletter_title" 
                                       value="<?= getConfig($configuracoes, 'paginas', 'newsletter_title', 'Inscreva-se na Newsletter') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="newsletter_description" class="form-label">Descrição da Newsletter</label>
                                <textarea class="form-control" id="newsletter_description" name="newsletter_description" rows="3"><?= getConfig($configuracoes, 'paginas', 'newsletter_description', 'Receba as melhores piadas e memes diretamente no seu email!') ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="about_page_title" class="form-label">Título da Página Sobre</label>
                                <input type="text" class="form-control" id="about_page_title" name="about_page_title" 
                                       value="<?= getConfig($configuracoes, 'paginas', 'about_page_title', 'Sobre Nós') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="contact_page_title" class="form-label">Título da Página Contato</label>
                                <input type="text" class="form-control" id="contact_page_title" name="contact_page_title" 
                                       value="<?= getConfig($configuracoes, 'paginas', 'contact_page_title', 'Entre em Contato') ?>">
                            </div>
                            
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Configurações de Páginas
                            </button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 