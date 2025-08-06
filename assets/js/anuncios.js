// Sistema de Anúncios Nativos - ULTRA-SIMPLES
// Brasil Hilário

// Função para garantir que a sidebar permaneça visível
function garantirSidebarVisivel() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarCol = document.querySelector('.col-lg-4');
    
    if (sidebar) {
        // Usar requestAnimationFrame para melhor performance
        requestAnimationFrame(() => {
            sidebar.style.display = 'block';
            sidebar.style.visibility = 'visible';
            sidebar.style.opacity = '1';
            sidebar.style.position = 'relative';
            sidebar.style.zIndex = '1';
        });
    }
    
    if (sidebarCol) {
        requestAnimationFrame(() => {
            sidebarCol.style.display = 'block';
            sidebarCol.style.visibility = 'visible';
        });
    }
}

// Executar quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Sistema de anúncios carregado');
    
    // Verificação inicial
    garantirSidebarVisivel();
    
    // Verificar periodicamente com intervalo maior para melhor performance
    setInterval(garantirSidebarVisivel, 3000);
    
    // Verificar quando anúncios do Google carregam
    if (window.adsbygoogle) {
        window.adsbygoogle.push(function() {
            // Usar setTimeout com delay maior para evitar conflitos
            setTimeout(garantirSidebarVisivel, 1500);
        });
    }
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