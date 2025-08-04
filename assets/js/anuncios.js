// Sistema de Anúncios Nativos - Versão SIMPLES e ROBUSTA
// Brasil Hilário

// Função para registrar clique em anúncio
function registrarCliqueAnuncio(anuncioId, tipoClique = 'imagem') {
    // Obter post-id da meta tag ou usar 0
    const postIdMeta = document.querySelector('meta[name="post-id"]');
    const postId = postIdMeta ? postIdMeta.content : 0;
    
    // Dados para enviar
    const dados = {
        anuncio_id: parseInt(anuncioId),
        post_id: parseInt(postId),
        tipo_clique: tipoClique
    };
    
    // Fazer requisição
    fetch('/api/registrar-clique.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(dados)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log('✅ Clique registrado:', data.message);
        } else {
            console.error('❌ Erro:', data.error);
        }
    })
    .catch(error => {
        console.error('❌ Erro na requisição:', error.message);
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
    
    // Verificar se há meta tag post-id
    const postIdMeta = document.querySelector('meta[name="post-id"]');
    if (postIdMeta) {
        console.log('✅ Meta tag post-id encontrada:', postIdMeta.content);
    } else {
        console.log('⚠️ Meta tag post-id não encontrada - usando 0');
    }
    
    // Verificar se há anúncios na página
    const anuncios = document.querySelectorAll('[onclick*="registrarCliqueAnuncio"]');
    console.log('🔍 Anúncios encontrados na página:', anuncios.length);
}); 