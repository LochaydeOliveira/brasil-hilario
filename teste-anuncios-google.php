<?php
require_once 'config/config.php';
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Anúncios Google - Brasil Hilário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="assets/js/anuncios.js" defer></script>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?php echo ADSENSE_CLIENT_ID; ?>" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container mt-4">
        <h1>Teste dos Anúncios do Google</h1>
        <p>Esta página testa se os anúncios do Google estão funcionando corretamente.</p>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5>Conteúdo Principal</h5>
                        <p>Este é o conteúdo principal da página.</p>
                        
                        <!-- Anúncio do Google 1 -->
                        <div class="my-4">
                            <h6>Anúncio 1:</h6>
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
                        
                        <p>Conteúdo entre anúncios.</p>
                        
                        <!-- Anúncio do Google 2 -->
                        <div class="my-4">
                            <h6>Anúncio 2:</h6>
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
                        
                        <p>Mais conteúdo após os anúncios.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="sidebar">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="mb-0">Sidebar</h3>
                        </div>
                        <div class="card-body">
                            <p>Esta é a sidebar.</p>
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
        // Log para verificar se os anúncios estão carregando
        console.log('Página carregada');
        
        // Verificar se o script do Google AdSense carregou
        if (window.adsbygoogle) {
            console.log('Google AdSense carregado');
        } else {
            console.log('Google AdSense não carregou');
        }
        
        // Verificar elementos de anúncio
        document.addEventListener('DOMContentLoaded', function() {
            const anuncios = document.querySelectorAll('ins.adsbygoogle');
            console.log('Anúncios encontrados:', anuncios.length);
            
            anuncios.forEach((anuncio, index) => {
                console.log(`Anúncio ${index + 1}:`, anuncio);
            });
        });
    </script>
</body>
</html> 