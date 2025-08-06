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
            console.log('Sidebar: Problema detectado, corrigindo...');
            
            // Forçar visibilidade
            sidebarElement.style.display = 'block';
            sidebarElement.style.visibility = 'visible';
            sidebarElement.style.opacity = '1';
            sidebarElement.style.zIndex = '10';
            
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
    
    // Função para monitorar mudanças no DOM
    function setupDOMObserver() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                // Verificar se mudanças afetaram a sidebar
                if (mutation.type === 'childList' || mutation.type === 'attributes') {
                    const target = mutation.target;
                    if (target === sidebarElement || sidebarElement.contains(target)) {
                        setTimeout(fixSidebarIssues, 100);
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
                    console.log('Sidebar: Anúncio detectado sobrepondo a sidebar');
                    fixSidebarIssues();
                }
            }
        });
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
        
        // Verificar visibilidade
        if (!isSidebarVisible()) {
            console.log('Sidebar: Problema de visibilidade detectado');
            fixSidebarIssues();
        }
        
        // Monitorar anúncios
        monitorAdSense();
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
        monitor: monitorSidebar
    };
    
})(); 