/**
 * Script de Proteção da Sidebar contra conflitos do AdSense
 * Brasil Hilário - 2025
 */

(function() {
    'use strict';
    
    // Configurações
    const SIDEBAR_SELECTOR = '.sidebar';
    const CHECK_INTERVAL = 3000; // 3 segundos
    const MAX_RETRIES = 10;
    
    let retryCount = 0;
    let sidebarElement = null;
    let lastOverlapCount = 0;
    let isPostPage = false;
    
    // Detectar se é página de post
    function detectPostPage() {
        // Verificar se estamos em uma página de post
        const url = window.location.pathname;
        const isPost = url.includes('/post/') || document.querySelector('.post-content');
        const hasMultipleAds = document.querySelectorAll('.adsbygoogle').length > 1;
        
        isPostPage = isPost || hasMultipleAds;
        
        if (isPostPage) {
            console.log('Sidebar Protection: Página de post detectada - Proteção intensificada');
        }
        
        return isPostPage;
    }
    
    // Função para verificar se a sidebar está visível
    function isSidebarVisible() {
        if (!sidebarElement) return false;
        
        const rect = sidebarElement.getBoundingClientRect();
        const style = window.getComputedStyle(sidebarElement);
        
        return (
            rect.width > 0 &&
            rect.height > 0 &&
            style.display !== 'none' &&
            style.visibility !== 'hidden' &&
            style.opacity !== '0'
        );
    }
    
    // Função para corrigir problemas da sidebar
    function fixSidebarIssues() {
        if (!sidebarElement) return;
        
        const style = window.getComputedStyle(sidebarElement);
        
        // Verificar se há problemas de visibilidade
        if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') {
            console.log('Sidebar: Problema de visibilidade detectado, corrigindo...');
            
            // Forçar visibilidade
            sidebarElement.style.display = 'block';
            sidebarElement.style.visibility = 'visible';
            sidebarElement.style.opacity = '1';
            sidebarElement.style.zIndex = '100';
            
            // Garantir que elementos filhos também estejam visíveis
            const children = sidebarElement.querySelectorAll('*');
            children.forEach(child => {
                child.style.position = 'relative';
                child.style.zIndex = 'inherit';
            });
        }
        
        // Verificar se há problemas de posicionamento
        const rect = sidebarElement.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) {
            console.log('Sidebar: Problema de dimensões detectado, corrigindo...');
            
            // Forçar dimensões mínimas
            sidebarElement.style.minWidth = '300px';
            sidebarElement.style.minHeight = '200px';
        }
    }
    
    // Função para corrigir sobreposições específicas
    function fixOverlapIssues() {
        if (!sidebarElement) return;
        
        // Garantir que a sidebar tenha prioridade
        sidebarElement.style.position = 'relative';
        sidebarElement.style.zIndex = '100';
        
        // Forçar que elementos dentro da sidebar tenham z-index adequado
        const sidebarChildren = sidebarElement.querySelectorAll('*');
        sidebarChildren.forEach(child => {
            const childStyle = window.getComputedStyle(child);
            if (childStyle.position === 'static') {
                child.style.position = 'relative';
            }
            child.style.zIndex = '101';
        });
        
        // Garantir que cards na sidebar tenham z-index alto
        const cards = sidebarElement.querySelectorAll('.card');
        cards.forEach(card => {
            card.style.position = 'relative';
            card.style.zIndex = '102';
        });
        
        // Proteção específica para páginas de post
        if (isPostPage) {
            // Forçar z-index ainda mais alto em páginas de post
            sidebarElement.style.zIndex = '200';
            sidebarChildren.forEach(child => {
                child.style.zIndex = '201';
            });
            cards.forEach(card => {
                card.style.zIndex = '202';
            });
            
            console.log('Sidebar: Proteção intensificada aplicada (página de post)');
        } else {
            console.log('Sidebar: Proteção de sobreposição aplicada');
        }
    }
    
    // Função para monitorar mudanças no DOM
    function setupDOMObserver() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                // Verificar se mudanças afetaram a sidebar
                if (mutation.type === 'childList' || mutation.type === 'attributes') {
                    const target = mutation.target;
                    if (target === sidebarElement || sidebarElement.contains(target)) {
                        setTimeout(() => {
                            fixSidebarIssues();
                            fixOverlapIssues();
                        }, 100);
                    }
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['style', 'class']
        });
    }
    
    // Função para monitorar anúncios AdSense
    function monitorAdSense() {
        const ads = document.querySelectorAll('.adsbygoogle');
        let overlapCount = 0;
        
        ads.forEach(ad => {
            // Verificar se o anúncio está interferindo com a sidebar
            const adRect = ad.getBoundingClientRect();
            const sidebarRect = sidebarElement ? sidebarElement.getBoundingClientRect() : null;
            
            if (sidebarRect && adRect) {
                // Verificar sobreposição
                const overlap = !(adRect.right < sidebarRect.left || 
                                adRect.left > sidebarRect.right || 
                                adRect.bottom < sidebarRect.top || 
                                adRect.top > sidebarRect.bottom);
                
                if (overlap) {
                    overlapCount++;
                    
                    // Verificar se o anúncio tem z-index muito alto
                    const adStyle = window.getComputedStyle(ad);
                    const adZIndex = parseInt(adStyle.zIndex) || 0;
                    
                    if (adZIndex > 50) {
                        console.log('Sidebar: Anúncio com z-index alto detectado, ajustando...');
                        ad.style.zIndex = '1';
                        ad.style.position = 'relative';
                    }
                }
            }
        });
        
        // Aplicar correções se houver sobreposições
        if (overlapCount > 0 && overlapCount !== lastOverlapCount) {
            console.log(`Sidebar: ${overlapCount} anúncio(s) sobrepondo a sidebar, aplicando proteção...`);
            fixOverlapIssues();
            lastOverlapCount = overlapCount;
        } else if (overlapCount === 0 && lastOverlapCount > 0) {
            console.log('Sidebar: Sobreposições resolvidas');
            lastOverlapCount = 0;
        }
        
        // Proteção adicional para páginas de post com muitos anúncios
        if (isPostPage && ads.length > 2) {
            console.log(`Sidebar: Página de post com ${ads.length} anúncios detectados - Proteção intensificada`);
            fixOverlapIssues();
        }
    }
    
    // Função principal de monitoramento
    function monitorSidebar() {
        if (!sidebarElement) {
            sidebarElement = document.querySelector(SIDEBAR_SELECTOR);
            if (!sidebarElement && retryCount < MAX_RETRIES) {
                retryCount++;
                setTimeout(monitorSidebar, 1000);
                return;
            }
        }
        
        if (!sidebarElement) {
            console.log('Sidebar: Elemento não encontrado após', MAX_RETRIES, 'tentativas');
            return;
        }
        
        // Detectar tipo de página
        detectPostPage();
        
        // Verificar visibilidade
        if (!isSidebarVisible()) {
            console.log('Sidebar: Problema de visibilidade detectado');
            fixSidebarIssues();
        }
        
        // Monitorar anúncios
        monitorAdSense();
        
        // Aplicar proteção preventiva
        fixOverlapIssues();
    }
    
    // Função de inicialização
    function init() {
        console.log('Sidebar Protection: Inicializando...');
        
        // Aguardar o DOM estar pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(init, 1000);
            });
            return;
        }
        
        // Iniciar monitoramento
        monitorSidebar();
        
        // Configurar observador de DOM
        setupDOMObserver();
        
        // Monitoramento contínuo
        setInterval(monitorSidebar, CHECK_INTERVAL);
        
        console.log('Sidebar Protection: Ativo');
    }
    
    // Inicializar quando o script for carregado
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        setTimeout(init, 1000);
    }
    
    // Expor funções para debug
    window.sidebarProtection = {
        checkVisibility: isSidebarVisible,
        fixIssues: fixSidebarIssues,
        fixOverlap: fixOverlapIssues,
        monitor: monitorSidebar,
        isPostPage: () => isPostPage
    };
    
})(); 