// Sistema de Anúncios Nativos
// Brasil Hilário

// Função para registrar cliques nos anúncios
function registrarCliqueAnuncio(anuncioId, tipoClique) {
    // Obter o ID do post atual (se disponível)
    const postId = document.querySelector('meta[name="post-id"]')?.content || 0;
    
    // Dados para enviar
    const dados = {
        anuncio_id: anuncioId,
        tipo_clique: tipoClique,
        post_id: postId
    };
    
    // Enviar requisição assíncrona
    fetch('/api/registrar-clique-anuncio.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(dados)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Clique registrado:', data);
        } else {
            console.error('Erro ao registrar clique:', data.error);
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
    });
}

// Função para inserir anúncios na sidebar
function inserirAnunciosSidebar(anuncios, postsContainer) {
    if (!anuncios || anuncios.length === 0) return;
    
    const posts = Array.from(postsContainer.children);
    const anunciosHTML = anuncios.map(anuncio => anuncio.html).join('');
    
    // Se há apenas 1 anúncio, inserir após o primeiro post
    if (anuncios.length === 1) {
        if (posts.length > 0) {
            const anuncioDiv = document.createElement('div');
            anuncioDiv.innerHTML = anunciosHTML;
            postsContainer.insertBefore(anuncioDiv.firstElementChild, posts[0].nextSibling);
        }
    } else {
        // Intercalar anúncios com posts
        let anuncioIndex = 0;
        posts.forEach((post, index) => {
            if (anuncioIndex < anuncios.length && index > 0 && index % 2 === 0) {
                const anuncioDiv = document.createElement('div');
                anuncioDiv.innerHTML = anuncios[anuncioIndex].html;
                postsContainer.insertBefore(anuncioDiv.firstElementChild, post);
                anuncioIndex++;
            }
        });
        
        // Adicionar anúncios restantes no final
        for (let i = anuncioIndex; i < anuncios.length; i++) {
            const anuncioDiv = document.createElement('div');
            anuncioDiv.innerHTML = anuncios[i].html;
            postsContainer.appendChild(anuncioDiv.firstElementChild);
        }
    }
}

// Função para inserir anúncios no conteúdo principal
function inserirAnunciosConteudo(anuncios, container) {
    if (!anuncios || anuncios.length === 0) return;
    
    // Criar grid de anúncios
    const anunciosGrid = document.createElement('div');
    anunciosGrid.className = 'anuncios-grid mb-4';
    anunciosGrid.style.cssText = `
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin: 2rem 0;
    `;
    
    anuncios.forEach(anuncio => {
        const anuncioDiv = document.createElement('div');
        anuncioDiv.innerHTML = anuncio.html;
        anunciosGrid.appendChild(anuncioDiv.firstElementChild);
    });
    
    // Inserir após o primeiro post ou no início
    const primeiroPost = container.querySelector('.post-card');
    if (primeiroPost) {
        container.insertBefore(anunciosGrid, primeiroPost.nextSibling);
    } else {
        container.appendChild(anunciosGrid);
    }
}

// Função para carregar anúncios via AJAX
function carregarAnuncios(localizacao, container, callback) {
    const postId = document.querySelector('meta[name="post-id"]')?.content || 0;
    
    fetch(`/api/get-anuncios.php?localizacao=${localizacao}&post_id=${postId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.anuncios) {
                callback(data.anuncios, container);
            }
        })
        .catch(error => {
            console.error('Erro ao carregar anúncios:', error);
        });
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Carregar anúncios da sidebar
    const sidebarContainer = document.querySelector('.sidebar .list-unstyled');
    if (sidebarContainer) {
        carregarAnuncios('sidebar', sidebarContainer, inserirAnunciosSidebar);
    }
    
    // Carregar anúncios do conteúdo principal
    const conteudoContainer = document.querySelector('.post-grid');
    if (conteudoContainer) {
        carregarAnuncios('conteudo', conteudoContainer, inserirAnunciosConteudo);
    }
});

// Estilos CSS para anúncios
const anunciosCSS = `
<style>
.anuncio-card {
    position: relative;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    margin-bottom: 1rem;
}

.anuncio-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.anuncio-patrocinado {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(0,0,0,0.7);
    color: #fff;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 500;
    z-index: 2;
}

.anuncio-imagem {
    width: 100%;
    height: 150px;
    object-fit: cover;
    display: block;
}

.anuncio-conteudo {
    padding: 1rem;
}

.anuncio-titulo {
    color: #333;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    line-height: 1.3;
    display: block;
    margin-bottom: 0.5rem;
}

.anuncio-titulo:hover {
    color: #007bff;
    text-decoration: underline;
}

.anuncio-cta {
    display: inline-block;
    background: #007bff;
    color: #fff;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
    transition: background-color 0.2s ease;
}

.anuncio-cta:hover {
    background: #0056b3;
    color: #fff;
    text-decoration: none;
}

/* Responsividade */
@media (max-width: 768px) {
    .anuncio-imagem {
        height: 120px;
    }
    
    .anuncio-conteudo {
        padding: 0.75rem;
    }
    
    .anuncio-titulo {
        font-size: 0.85rem;
    }
}
</style>
`;

// Inserir CSS no head
document.head.insertAdjacentHTML('beforeend', anunciosCSS); 