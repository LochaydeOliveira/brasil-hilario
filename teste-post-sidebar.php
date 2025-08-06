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
    <title>Teste Sidebar em Página de Post</title>
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
        
        .post-content {
            background: #fff;
            padding: 20px;
            border: 1px solid #dee2e6;
            margin: 20px 0;
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
        
        .status-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .z-index-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            margin: 10px 0;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h1>Teste de Proteção da Sidebar em Página de Post</h1>
    
    <div class="test-container">
        <h3>Status da Proteção</h3>
        <div id="protection-status" class="status-indicator status-ok">
            ✅ Proteção Ativa
        </div>
        
        <div id="post-page-status" class="status-indicator status-ok">
            ✅ Página de Post Detectada
        </div>
        
        <div id="sidebar-status" class="status-indicator status-ok">
            ✅ Sidebar Visível
        </div>
        
        <div id="z-index-status" class="status-indicator status-ok">
            ✅ Z-Index Correto
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="post-content">
                <h2>Simulação de Conteúdo de Post</h2>
                <p>Esta é uma simulação de uma página de post com múltiplos anúncios AdSense.</p>
                
                <div class="adsense-test">
                    <h5>Anúncio AdSense 1 (Dentro do Conteúdo)</h5>
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
                
                <p>Mais conteúdo do post aqui...</p>
                
                <div class="adsense-test">
                    <h5>Anúncio AdSense 2 (Grupo de Anúncios)</h5>
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
                
                <p>Continuação do conteúdo...</p>
            </div>
            
            <div class="adsense-test">
                <h5>Anúncio AdSense 3 (Final da Página)</h5>
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
                <h4>Sidebar Protegida (Z-Index: 200)</h4>
                <p>Esta é a sidebar que deve permanecer visível mesmo com múltiplos anúncios.</p>
                
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
        <h3>Informações de Debug</h3>
        <div id="debug-info" class="z-index-info">
            Carregando informações...
        </div>
        
        <div class="mt-3">
            <button class="btn btn-primary" onclick="runTests()">
                🔄 Executar Testes
            </button>
            <button class="btn btn-success" onclick="fixIssues()">
                🛠️ Corrigir Problemas
            </button>
            <button class="btn btn-info" onclick="showZIndexInfo()">
                📊 Ver Z-Index
            </button>
            <button class="btn btn-warning" onclick="checkPostPage()">
                🔍 Verificar Página de Post
            </button>
        </div>
    </div>
</div>

<script>
// Funções de teste
function runTests() {
    console.log('Executando testes de proteção para página de post...');
    
    // Teste 1: Verificar se a sidebar está visível
    const sidebar = document.querySelector('.sidebar-test');
    const sidebarVisible = sidebar && sidebar.offsetWidth > 0 && sidebar.offsetHeight > 0;
    
    // Teste 2: Verificar z-index da sidebar
    const sidebarZIndex = sidebar ? parseInt(window.getComputedStyle(sidebar).zIndex) : 0;
    const zIndexCorrect = sidebarZIndex >= 200; // Z-index mais alto para páginas de post
    
    // Teste 3: Verificar se a proteção está ativa
    const protectionActive = typeof window.sidebarProtection !== 'undefined';
    
    // Teste 4: Verificar se é detectada como página de post
    const isPostPage = window.sidebarProtection ? window.sidebarProtection.isPostPage() : false;
    
    // Atualizar status
    updateStatus('protection-status', protectionActive, 'Proteção Ativa', 'Proteção Inativa');
    updateStatus('post-page-status', isPostPage, 'Página de Post Detectada', 'Página Normal');
    updateStatus('sidebar-status', sidebarVisible, 'Sidebar Visível', 'Sidebar Invisível');
    updateStatus('z-index-status', zIndexCorrect, 'Z-Index Correto (≥200)', 'Z-Index Incorreto');
    
    // Log dos resultados
    console.log('Resultados dos testes:', {
        protectionActive,
        isPostPage,
        sidebarVisible,
        zIndexCorrect,
        sidebarZIndex
    });
}

function fixIssues() {
    console.log('Aplicando correções...');
    
    if (window.sidebarProtection) {
        window.sidebarProtection.fixIssues();
        window.sidebarProtection.fixOverlap();
        console.log('Correções aplicadas');
    } else {
        console.log('Proteção não encontrada');
    }
}

function showZIndexInfo() {
    const elements = document.querySelectorAll('.sidebar-test, .adsense-test, .card, .card-body, .post-content');
    let info = 'Informações de Z-Index:\n\n';
    
    elements.forEach((el, index) => {
        const style = window.getComputedStyle(el);
        const zIndex = style.zIndex;
        const position = style.position;
        const display = style.display;
        
        info += `${index + 1}. ${el.className || el.tagName}:\n`;
        info += `   Z-Index: ${zIndex}\n`;
        info += `   Position: ${position}\n`;
        info += `   Display: ${display}\n\n`;
    });
    
    document.getElementById('debug-info').innerHTML = info.replace(/\n/g, '<br>');
}

function checkPostPage() {
    const url = window.location.pathname;
    const hasPostContent = document.querySelector('.post-content');
    const adCount = document.querySelectorAll('.adsbygoogle').length;
    
    let info = 'Detecção de Página de Post:\n\n';
    info += `URL: ${url}\n`;
    info += `Contém /post/: ${url.includes('/post/')}\n`;
    info += `Tem .post-content: ${!!hasPostContent}\n`;
    info += `Número de anúncios: ${adCount}\n`;
    info += `É página de post: ${url.includes('/post/') || !!hasPostContent || adCount > 1}\n`;
    
    if (window.sidebarProtection) {
        info += `Detectado pelo script: ${window.sidebarProtection.isPostPage()}\n`;
    }
    
    document.getElementById('debug-info').innerHTML = info.replace(/\n/g, '<br>');
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
    setTimeout(runTests, 2000);
    setInterval(runTests, 5000);
});

console.log('Teste de Proteção da Sidebar em Página de Post carregado');
</script>

</body>
</html>

<?php ob_end_flush(); ?> 