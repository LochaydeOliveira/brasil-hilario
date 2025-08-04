// Sistema de Anúncios Nativos - ULTRA-SIMPLES
// Brasil Hilário

// Função para registrar clique em anúncio
function registrarCliqueAnuncio(anuncioId, tipoClique = 'imagem') {
    // Obter post-id da meta tag
    const postIdMeta = document.querySelector('meta[name="post-id"]');
    const postId = postIdMeta ? parseInt(postIdMeta.content) : 0;
    
    // Dados básicos
    const dados = {
        anuncio_id: parseInt(anuncioId),
        post_id: postId,
        tipo_clique: tipoClique
    };
    
    // Fazer requisição simples
    fetch('/api/registrar-clique.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(dados)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Clique registrado');
        } else {
            console.log('❌ Erro:', data.error);
        }
    })
    .catch(error => {
        console.log('❌ Erro na requisição');
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

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Sistema de anúncios carregado');
}); 