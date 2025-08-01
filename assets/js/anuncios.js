// Sistema de Anúncios Nativos
// Brasil Hilário

// Função para registrar clique em anúncio
function registrarCliqueAnuncio(anuncioId, tipoClique) {
    const postId = document.querySelector('meta[name="post-id"]')?.content || 0;
    
    fetch('/api/registrar-clique-anuncio.php', {
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Clique registrado com sucesso');
        }
    })
    .catch(error => {
        console.error('Erro ao registrar clique:', error);
    });
}

// Função para inserir anúncios na sidebar
function inserirAnunciosSidebar(anuncios, postsContainer) {
    if (!anuncios || anuncios.length === 0) return;
    
    anuncios.forEach((anuncio, index) => {
        const anuncioElement = document.createElement('li');
        anuncioElement.className = 'mb-3 anuncio-item';
        anuncioElement.innerHTML = `
            <div style="border: 2px solid #ff6b6b; padding: 10px; margin: 10px 0; background: #fff3cd;">
                <div style="background: #ff6b6b; color: white; padding: 2px 8px; font-size: 12px; display: inline-block; margin-bottom: 5px;">PATROCINADO</div>
                <h4 style="margin: 0 0 5px 0; font-size: 14px;">${anuncio.titulo}</h4>
                <a href="${anuncio.link_compra}" target="_blank" style="color: #007bff; text-decoration: none;">Ver mais</a>
            </div>
        `;
        
        // Inserir no final da lista
        postsContainer.appendChild(anuncioElement);
    });
}

// Função para inserir anúncios no conteúdo principal
function inserirAnunciosConteudo(anuncios, container) {
    if (!anuncios || anuncios.length === 0) return;
    
    anuncios.forEach((anuncio, index) => {
        const anuncioElement = document.createElement('article');
        anuncioElement.className = 'blog-post mb-4 anuncio-sponsorizado';
        anuncioElement.innerHTML = `
            <div style="border: 2px solid #ff6b6b; padding: 15px; margin: 15px 0; background: #fff3cd;">
                <div style="background: #ff6b6b; color: white; padding: 2px 8px; font-size: 12px; display: inline-block; margin-bottom: 10px;">PATROCINADO</div>
                <h2 style="margin: 0 0 10px 0; font-size: 18px;">${anuncio.titulo}</h2>
                <a href="${anuncio.link_compra}" target="_blank" style="color: #007bff; text-decoration: none;">Ver mais</a>
            </div>
        `;
        
        // Inserir no início do container
        container.insertBefore(anuncioElement, container.firstChild);
    });
}

// Função para carregar anúncios via AJAX
function carregarAnuncios(localizacao, container, callback) {
    const postId = document.querySelector('meta[name="post-id"]')?.content || 0;
    const apiUrl = `/get-anuncios.php?localizacao=${localizacao}&post_id=${postId}`;
    
    console.log('Testando API:', apiUrl);
    
    fetch(apiUrl)
        .then(response => {
            console.log('Status da resposta:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Resposta da API:', data);
            if (data.success && data.anuncios) {
                callback(data.anuncios, container);
            } else {
                console.log('API funcionando, mas sem anúncios:', data);
            }
        })
        .catch(error => {
            console.error('Erro ao carregar anúncios:', error);
        });
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Carregar anúncios da sidebar
    carregarAnunciosSidebar();
    
    // Carregar anúncios do conteúdo principal
    carregarAnunciosConteudo();
});

// Função para carregar anúncios da sidebar
function carregarAnunciosSidebar() {
    const sidebarSections = document.querySelectorAll('.sidebar .card-body .list-unstyled');
    if (sidebarSections.length > 0) {
        carregarAnuncios('sidebar', sidebarSections[0], inserirAnunciosSidebar);
    }
}

// Função para carregar anúncios do conteúdo principal
function carregarAnunciosConteudo() {
    const conteudoContainer = document.querySelector('.col-lg-8');
    if (conteudoContainer) {
        carregarAnuncios('conteudo', conteudoContainer, inserirAnunciosConteudo);
    }
}

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
</style>
`;

// Inserir CSS no head
document.head.insertAdjacentHTML('beforeend', anunciosCSS); 