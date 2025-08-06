<?php
ob_start();
session_start();
require_once 'config/config.php';
require_once 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Sidebar + AdSense</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        
        .sidebar-debug {
            background: #e3f2fd;
            border: 2px solid #2196f3;
            padding: 10px;
            margin: 10px 0;
        }
        
        .adsense-debug {
            background: #fff3e0;
            border: 2px solid #ff9800;
            padding: 10px;
            margin: 10px 0;
        }
        
        .sidebar {
            background: #fff;
            padding: 0 2rem;
            border-left: 1px solid #dee2e6;
            position: relative;
            z-index: 1;
        }
        
        .adsbygoogle {
            border: 2px dashed #ff9800;
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff3e0;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h1>Debug: Sidebar + AdSense</h1>
    
    <div class="debug-info">
        <h4>Informações do Debug</h4>
        <p><strong>Data/Hora:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
        <p><strong>User Agent:</strong> <?php echo $_SERVER['HTTP_USER_AGENT'] ?? 'N/A'; ?></p>
        <p><strong>URL:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'N/A'; ?></p>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="adsense-debug">
                <h4>Área de Conteúdo Principal</h4>
                <p>Esta é a área onde o conteúdo principal seria exibido.</p>
                
                <h5>Teste AdSense 1 (In-Article)</h5>
                <ins class="adsbygoogle"
                     style="display:block; text-align:center;"
                     data-ad-layout="in-article"
                     data-ad-format="fluid"
                     data-ad-client="ca-pub-8313157699231074"
                     data-ad-slot="7748469758">
                </ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
                
                <h5>Teste AdSense 2 (Auto)</h5>
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-8313157699231074"
                     data-ad-slot="6450653464"
                     data-ad-format="auto"
                     data-full-width-responsive="true">
                </ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="sidebar-debug">
                <h4>Sidebar (Debug)</h4>
                <p>Esta é a sidebar que deveria permanecer visível.</p>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Mais Recentes</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <a href="#" class="text-decoration-none">
                                    Teste de Post 1
                                </a>
                                <small class="text-muted d-block">
                                    01/01/2025
                                </small>
                            </li>
                            <li class="mb-3">
                                <a href="#" class="text-decoration-none">
                                    Teste de Post 2
                                </a>
                                <small class="text-muted d-block">
                                    02/01/2025
                                </small>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">AdSense na Sidebar</h5>
                    </div>
                    <div class="card-body">
                        <ins class="adsbygoogle"
                             style="display:block; text-align:center;"
                             data-ad-layout="in-article"
                             data-ad-format="fluid"
                             data-ad-client="ca-pub-8313157699231074"
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
    
    <div class="debug-info mt-4">
        <h4>JavaScript de Monitoramento</h4>
        <div id="sidebar-status">Verificando status da sidebar...</div>
        <div id="adsense-status">Verificando status dos anúncios...</div>
    </div>
</div>

<script>
// Monitoramento da sidebar
function checkSidebar() {
    const sidebar = document.querySelector('.sidebar-debug');
    if (sidebar) {
        const rect = sidebar.getBoundingClientRect();
        const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
        
        document.getElementById('sidebar-status').innerHTML = `
            <strong>Sidebar:</strong> ${isVisible ? '✅ Visível' : '❌ Não visível'}<br>
            <strong>Posição:</strong> Top: ${rect.top.toFixed(2)}, Bottom: ${rect.bottom.toFixed(2)}<br>
            <strong>Dimensões:</strong> ${rect.width.toFixed(2)} x ${rect.height.toFixed(2)}
        `;
    }
}

// Monitoramento dos anúncios AdSense
function checkAdSense() {
    const ads = document.querySelectorAll('.adsbygoogle');
    let status = '';
    
    ads.forEach((ad, index) => {
        const rect = ad.getBoundingClientRect();
        const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
        status += `<strong>Ad ${index + 1}:</strong> ${isVisible ? '✅ Visível' : '❌ Não visível'}<br>`;
    });
    
    document.getElementById('adsense-status').innerHTML = status;
}

// Verificar a cada 2 segundos
setInterval(() => {
    checkSidebar();
    checkAdSense();
}, 2000);

// Verificar imediatamente
checkSidebar();
checkAdSense();

// Monitorar mudanças no DOM
const observer = new MutationObserver(() => {
    checkSidebar();
    checkAdSense();
});

observer.observe(document.body, {
    childList: true,
    subtree: true
});

console.log('Debug Sidebar + AdSense iniciado');
</script>

</body>
</html>

<?php ob_end_flush(); ?> 