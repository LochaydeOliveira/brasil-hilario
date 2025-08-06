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
    <title>Teste: Anúncios AdSense Visíveis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-container {
            background: #f8f9fa;
            border: 2px solid #28a745;
            padding: 20px;
            margin: 20px 0;
        }
        
        .sidebar-test {
            background: #e3f2fd;
            border: 2px solid #2196f3;
            padding: 15px;
            position: relative;
            z-index: 200;
        }
        
        .adsense-test {
            background: #fff3e0;
            border: 2px dashed #ff9800;
            padding: 15px;
            margin: 10px 0;
            position: relative;
            z-index: 1;
        }
        
        .status-indicator {
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
        }
        
        .status-ok {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .adsense-counter {
            background: #fff;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h1>Teste: Anúncios AdSense Visíveis</h1>
    
    <div class="test-container">
        <h3>Status dos Anúncios</h3>
        <div id="adsense-count" class="status-indicator status-ok">
            📊 Contando anúncios...
        </div>
        
        <div id="adsense-visible" class="status-indicator status-ok">
            👁️ Verificando visibilidade...
        </div>
        
        <div id="sidebar-visible" class="status-indicator status-ok">
            📋 Verificando sidebar...
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="adsense-test">
                <h5>Anúncio AdSense 1 (Z-Index: 1)</h5>
                <p>Este anúncio deve estar visível mas com z-index baixo</p>
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
            </div>
            
            <div class="adsense-test">
                <h5>Anúncio AdSense 2 (Z-Index: 1)</h5>
                <p>Este anúncio também deve estar visível</p>
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
            
            <div class="adsense-test">
                <h5>Anúncio AdSense 3 (Z-Index: 1)</h5>
                <p>Este anúncio também deve estar visível</p>
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
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="sidebar-test">
                <h4>Sidebar (Z-Index: 200)</h4>
                <p>Esta sidebar deve estar sempre visível</p>
                
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
                        <h5 class="mb-0">AdSense na Sidebar (Z-Index: 203)</h5>
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
    
    <div class="test-container">
        <h3>Informações Detalhadas</h3>
        <div id="detailed-info" class="adsense-counter">
            Carregando informações detalhadas...
        </div>
        
        <div class="mt-3">
            <button class="btn btn-primary" onclick="checkAdSense()">
                🔍 Verificar Anúncios
            </button>
            <button class="btn btn-success" onclick="checkZIndex()">
                📊 Ver Z-Index
            </button>
            <button class="btn btn-info" onclick="checkVisibility()">
                👁️ Verificar Visibilidade
            </button>
        </div>
    </div>
</div>

<script>
// Funções de teste
function checkAdSense() {
    const ads = document.querySelectorAll('.adsbygoogle');
    const visibleAds = Array.from(ads).filter(ad => {
        const rect = ad.getBoundingClientRect();
        const style = window.getComputedStyle(ad);
        return rect.width > 0 && rect.height > 0 && 
               style.display !== 'none' && 
               style.visibility !== 'hidden' && 
               style.opacity !== '0';
    });
    
    // Atualizar status
    updateStatus('adsense-count', ads.length > 0, `${ads.length} anúncios encontrados`, 'Nenhum anúncio encontrado');
    updateStatus('adsense-visible', visibleAds.length > 0, `${visibleAds.length} anúncios visíveis`, 'Anúncios não visíveis');
    
    console.log('Anúncios AdSense:', {
        total: ads.length,
        visible: visibleAds.length,
        ads: Array.from(ads).map(ad => ({
            visible: visibleAds.includes(ad),
            zIndex: window.getComputedStyle(ad).zIndex,
            display: window.getComputedStyle(ad).display
        }))
    });
}

function checkZIndex() {
    const elements = document.querySelectorAll('.adsbygoogle, .sidebar-test, .card');
    let info = 'Z-Index dos Elementos:\n\n';
    
    elements.forEach((el, index) => {
        const style = window.getComputedStyle(el);
        const zIndex = style.zIndex;
        const position = style.position;
        const isAd = el.classList.contains('adsbygoogle');
        
        info += `${index + 1}. ${isAd ? 'Anúncio' : el.className || el.tagName}:\n`;
        info += `   Z-Index: ${zIndex}\n`;
        info += `   Position: ${position}\n`;
        info += `   Tipo: ${isAd ? 'AdSense' : 'Sidebar'}\n\n`;
    });
    
    document.getElementById('detailed-info').innerHTML = info.replace(/\n/g, '<br>');
}

function checkVisibility() {
    const sidebar = document.querySelector('.sidebar-test');
    const ads = document.querySelectorAll('.adsbygoogle');
    
    const sidebarVisible = sidebar && sidebar.offsetWidth > 0 && sidebar.offsetHeight > 0;
    const adsVisible = Array.from(ads).every(ad => {
        const rect = ad.getBoundingClientRect();
        return rect.width > 0 && rect.height > 0;
    });
    
    let info = 'Status de Visibilidade:\n\n';
    info += `Sidebar visível: ${sidebarVisible ? '✅ Sim' : '❌ Não'}\n`;
    info += `Anúncios visíveis: ${adsVisible ? '✅ Sim' : '❌ Não'}\n`;
    info += `Total de anúncios: ${ads.length}\n`;
    info += `Sidebar z-index: ${sidebar ? window.getComputedStyle(sidebar).zIndex : 'N/A'}\n`;
    
    // Verificar se algum anúncio tem z-index maior que a sidebar
    const sidebarZIndex = sidebar ? parseInt(window.getComputedStyle(sidebar).zIndex) : 0;
    const highZIndexAds = Array.from(ads).filter(ad => {
        const adZIndex = parseInt(window.getComputedStyle(ad).zIndex) || 0;
        return adZIndex > sidebarZIndex;
    });
    
    info += `Anúncios com z-index alto: ${highZIndexAds.length}\n`;
    
    document.getElementById('detailed-info').innerHTML = info.replace(/\n/g, '<br>');
}

function updateStatus(elementId, condition, successText, errorText) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = condition ? `✅ ${successText}` : `❌ ${errorText}`;
        element.className = `status-indicator ${condition ? 'status-ok' : 'status-error'}`;
    }
}

// Executar testes automaticamente
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(checkAdSense, 2000);
    setInterval(checkAdSense, 5000);
});

console.log('Teste de Visibilidade dos Anúncios carregado');
</script>

</body>
</html>

<?php ob_end_flush(); ?> 