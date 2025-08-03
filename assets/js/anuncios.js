// Sistema de Anúncios Nativos - Versão SIMPLES
// Brasil Hilário

// Função para registrar clique em anúncio
function registrarCliqueAnuncio(anuncioId, tipoClique = 'imagem') {
    const postId = document.querySelector('meta[name="post-id"]')?.content || 0;
    
    // Temporariamente usar debug para identificar o problema
    fetch('/debug-clique.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            anuncio_id: anuncioId,
            post_id: postId,
            tipo_clique: tipoClique
        })
    })
    .then(response => response.text())
    .then(data => {
        console.log('🔍 Debug response:', data);
    })
    .catch(error => {
        console.error('❌ Erro na requisição:', error);
    });
}

// Função para scroll do carrossel
function scrollCarrossel(grupoId, direction) {
    const carrossel = document.querySelector(`[data-grupo-id="${grupoId}"] .anuncios-carrossel`);
    if (!carrossel) return;
    
    const scrollAmount = 300;
    if (direction === 'left') {
        carrossel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        carrossel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Sistema de anúncios nativos carregado');
}); 