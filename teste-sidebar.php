<?php
require_once 'config/config.php';
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Sidebar - Brasil Hilário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="assets/js/anuncios.js" defer></script>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?php echo ADSENSE_CLIENT_ID; ?>" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container mt-4">
        <h1>Teste da Sidebar</h1>
        <p>Esta página testa se a sidebar permanece visível quando os anúncios do Google aparecem.</p>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5>Conteúdo Principal</h5>
                        <p>Este é o conteúdo principal da página. A sidebar deve permanecer visível mesmo quando os anúncios do Google carregam.</p>
                        
                        <!-- Anúncio do Google para teste -->
                        <div class="my-4">
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
                        
                        <p>Após o anúncio acima, a sidebar deve continuar visível.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="sidebar">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="mb-0">Sidebar Teste</h3>
                        </div>
                        <div class="card-body">
                            <p>Esta é a sidebar. Ela deve permanecer visível mesmo quando os anúncios do Google aparecem.</p>
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
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 