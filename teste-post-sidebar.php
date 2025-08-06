<?php
require_once 'config/config.php';
require_once 'includes/db.php';

$page_title = "Teste Post Sidebar - Brasil Hilário";
$page_description = "Teste para verificar se a sidebar permanece visível em páginas de post";
$page_keywords = "teste, post, sidebar, adsense";
$page_url = BLOG_URL . '/teste-post-sidebar.php';
$page_image = BLOG_URL . '/assets/img/logo-brasil-hilario-para-og.png';
$page_og_type = 'article';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta name="keywords" content="<?php echo $page_keywords; ?>">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <script src="assets/js/anuncios.js" defer></script>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?php echo ADSENSE_CLIENT_ID; ?>" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <!-- Simular estrutura de uma página de post -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo BLOG_URL; ?>">Início</a></li>
                        <li class="breadcrumb-item"><a href="#">Categoria</a></li>
                        <li class="breadcrumb-item active">Título do Post</li>
                    </ol>
                </nav>

                <h1 class="mt-4 mb-3 title-posts">Título do Post de Teste</h1>

                <div class="post-meta mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="meta-info">
                            <span class="me-3"><i class="far fa-calendar-alt"></i> 01/01/2024</span>
                            <span class="me-3"><i class="far fa-folder"></i> <a href="#">Categoria</a></span>
                            <span><i class="far fa-eye"></i> 1,000 visualizações</span>
                        </div>
                    </div>
                </div>

                <div class="post-content">
                    <p>Este é um post de teste para verificar se a sidebar permanece visível quando os anúncios do Google aparecem.</p>
                    
                    <h2>Primeira Seção</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    
                    <h2>Segunda Seção</h2>
                    <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                    
                    <h2>Terceira Seção</h2>
                    <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                </div>

                <hr>

                <!-- Anúncio do Google no conteúdo -->
                <div class="my-4">
                    <h6>Anúncio no Conteúdo:</h6>
                    <ins class="adsbygoogle"
                        style="display:block"
                        data-ad-format="autorelaxed"
                        data-ad-client="<?php echo ADSENSE_CLIENT_ID; ?>"
                        data-ad-slot="2883155880">
                    </ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>

                <p>Conteúdo após o anúncio.</p>
            </div>
            
            <div class="col-lg-4">
                <div class="sidebar">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="mb-0">Sidebar do Post</h3>
                        </div>
                        <div class="card-body">
                            <p>Esta é a sidebar da página de post. Ela deve permanecer visível mesmo quando os anúncios do Google aparecem.</p>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <a href="#" class="text-decoration-none">Item 1 da Sidebar</a>
                                </li>
                                <li class="mb-3">
                                    <a href="#" class="text-decoration-none">Item 2 da Sidebar</a>
                                </li>
                                <li class="mb-3">
                                    <a href="#" class="text-decoration-none">Item 3 da Sidebar</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Anúncio do Google na sidebar -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6>Anúncio na Sidebar:</h6>
                            <ins class="adsbygoogle"
                                style="display:block; text-align:center;"
                                data-ad-layout="in-article"
                                data-ad-format="fluid"
                                data-ad-client="<?php echo ADSENSE_CLIENT_ID; ?>"
                                data-ad-slot="4177902168">
                            </ins>
                            <script>
                                (adsbygoogle = window.adsbygoogle || []).push({});
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Anúncio do Google no final -->
        <div class="my-4">
            <h6>Anúncio Final:</h6>
            <ins class="adsbygoogle"
                style="display:block"
                data-ad-client="<?php echo ADSENSE_CLIENT_ID; ?>"
                data-ad-slot="6450653464"
                data-ad-format="auto"
                data-full-width-responsive="true">
            </ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Verificar se estamos em uma página de post
        const isPostPage = document.querySelector('.post-content') !== null;
        console.log('Página de post:', isPostPage ? '✅ Sim' : '❌ Não');
        
        // Verificar se estamos em Quirks Mode
        if (document.compatMode === 'BackCompat') {
            console.log('⚠️ Página em Quirks Mode');
        } else {
            console.log('✅ Página em Standards Mode');
        }
        
        // Verificar DOCTYPE
        console.log('DOCTYPE:', document.doctype);
        
        // Verificar se o Google AdSense carregou
        if (window.adsbygoogle) {
            console.log('✅ Google AdSense carregado');
        } else {
            console.log('❌ Google AdSense não carregou');
        }
        
        // Verificar elementos de anúncio
        document.addEventListener('DOMContentLoaded', function() {
            const anuncios = document.querySelectorAll('ins.adsbygoogle');
            console.log('Anúncios encontrados:', anuncios.length);
            
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                console.log('✅ Sidebar encontrada');
                console.log('Sidebar display:', getComputedStyle(sidebar).display);
                console.log('Sidebar visibility:', getComputedStyle(sidebar).visibility);
                console.log('Sidebar opacity:', getComputedStyle(sidebar).opacity);
            } else {
                console.log('❌ Sidebar não encontrada');
            }
        });
    </script>
</body>
</html> 