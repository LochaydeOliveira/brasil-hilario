// Sistema de Anúncios Nativos - ULTRA-SIMPLES
// Brasil Hilário

// Função para garantir que a sidebar permaneça visível
function garantirSidebarVisivel() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarCol = document.querySelector('.col-lg-4');
    const postContent = document.querySelector('.post-content');
    
    // Verificar se estamos em uma página de post
    const isPostPage = postContent !== null;
    
    if (sidebar) {
        // Usar requestAnimationFrame para melhor performance
        requestAnimationFrame(() => {
            sidebar.style.display = 'block';
            sidebar.style.visibility = 'visible';
            sidebar.style.opacity = '1';
            sidebar.style.position = 'relative';
            sidebar.style.zIndex = isPostPage ? '10' : '1';
            
            // Garantir altura mínima em páginas de post
            if (isPostPage) {
                sidebar.style.minHeight = '200px';
            }
        });
    }
    
    if (sidebarCol) {
        requestAnimationFrame(() => {
            sidebarCol.style.display = 'block';
            sidebarCol.style.visibility = 'visible';
            sidebarCol.style.flex = '0 0 33.333333%';
            sidebarCol.style.maxWidth = '33.333333%';
        });
    }
    
    // Proteção específica para anúncios do Google
    const adsbygoogleElements = document.querySelectorAll('ins.adsbygoogle');
    adsbygoogleElements.forEach(ad => {
        const nextSibling = ad.nextElementSibling;
        if (nextSibling && nextSibling.classList.contains('col-lg-4')) {
            const sidebarInNext = nextSibling.querySelector('.sidebar');
            if (sidebarInNext) {
                sidebarInNext.style.display = 'block';
                sidebarInNext.style.visibility = 'visible';
                sidebarInNext.style.opacity = '1';
                sidebarInNext.style.zIndex = '10';
            }
        }
    });
}

// Função para verificar se há conflitos com anúncios do Google
function verificarConflitosAdSense() {
    const adsbygoogleElements = document.querySelectorAll('ins.adsbygoogle');
    const sidebar = document.querySelector('.sidebar');
    
    if (adsbygoogleElements.length > 0 && sidebar) {
        console.log('🚀 Anúncios do Google detectados, protegendo sidebar...');
        
        // Verificar se algum anúncio está interferindo com a sidebar
        adsbygoogleElements.forEach((ad, index) => {
            const adRect = ad.getBoundingClientRect();
            const sidebarRect = sidebar.getBoundingClientRect();
            
            // Se o anúncio está sobrepondo a sidebar
            if (adRect.right > sidebarRect.left && adRect.left < sidebarRect.right) {
                console.log(`⚠️ Anúncio ${index + 1} pode estar interferindo com a sidebar`);
                garantirSidebarVisivel();
            }
        });
    }
}

// Executar quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Sistema de anúncios carregado');
    
    // Verificação inicial
    garantirSidebarVisivel();
    
    // Verificar se estamos em uma página de post
    const isPostPage = document.querySelector('.post-content') !== null;
    if (isPostPage) {
        console.log('📄 Página de post detectada, aplicando proteções específicas');
    }
    
    // Verificar periodicamente com intervalo maior para melhor performance
    setInterval(garantirSidebarVisivel, 3000);
    
    // Verificar conflitos com AdSense
    setInterval(verificarConflitosAdSense, 5000);
    
    // Verificar quando anúncios do Google carregam
    if (window.adsbygoogle) {
        window.adsbygoogle.push(function() {
            // Usar setTimeout com delay maior para evitar conflitos
            setTimeout(() => {
                garantirSidebarVisivel();
                verificarConflitosAdSense();
            }, 1500);
        });
    }
    
    // Observer para detectar mudanças no DOM (anúncios sendo inseridos)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && node.classList && node.classList.contains('adsbygoogle')) {
                        console.log('🔍 Novo anúncio do Google detectado');
                        setTimeout(garantirSidebarVisivel, 1000);
                    }
                });
            }
        });
    });
    
    // Observar mudanças no body
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Função para registrar cliques em anúncios
function registrarCliqueAnuncio(anuncioId, tipoClique) {
    fetch('/api/registrar-clique-anuncio.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            anuncio_id: anuncioId,
            tipo_clique: tipoClique
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Clique registrado:', data);
    })
    .catch(error => {
        console.error('Erro ao registrar clique:', error);
    });
}

// Função para scroll do carrossel
function scrollCarrossel(grupoId, direcao) {
    const carrossel = document.querySelector(`[data-grupo-id="${grupoId}"] .anuncios-carrossel`);
    if (carrossel) {
        const scrollAmount = direcao === 'left' ? -300 : 300;
        carrossel.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
    }
} 