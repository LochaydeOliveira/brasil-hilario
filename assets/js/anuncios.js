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
    if (!anuncios || anuncios.length === 0) {
        console.log('Nenhum anúncio para sidebar');
        return;
    }
    
    console.log('Inserindo anúncios na sidebar:', anuncios.length);
    
    // Encontrar todos os posts na sidebar
    const posts = postsContainer.querySelectorAll('li');
    
    anuncios.forEach((anuncio, index) => {
        const anuncioElement = document.createElement('li');
        anuncioElement.className = 'list-group-item';
        anuncioElement.innerHTML = anuncio.html;
        
        // Inserir anúncio a cada 3 posts
        const insertPosition = (index + 1) * 3;
        if (insertPosition < posts.length) {
            postsContainer.insertBefore(anuncioElement, posts[insertPosition]);
        } else {
            postsContainer.appendChild(anuncioElement);
        }
    });
}

// Função para inserir anúncios no conteúdo principal
function inserirAnunciosConteudo(anuncios, container) {
    if (!anuncios || anuncios.length === 0) {
        console.log('Nenhum anúncio para conteúdo');
        return;
    }
    
    console.log('Inserindo anúncios no conteúdo:', anuncios.length);
    
    // Encontrar todos os posts no container
    const posts = container.querySelectorAll('.blog-post');
    
    if (posts.length === 0) {
        console.log('Nenhum post encontrado no container');
        return;
    }
    
    anuncios.forEach((anuncio, index) => {
        // Criar elemento de anúncio
        const anuncioElement = document.createElement('article');
        anuncioElement.className = 'blog-post mb-4 anuncio-sponsorizado';
        anuncioElement.innerHTML = anuncio.html;
        
        // Inserir anúncio a cada 2 posts
        const insertPosition = (index + 1) * 2;
        if (insertPosition < posts.length) {
            container.insertBefore(anuncioElement, posts[insertPosition]);
        } else {
            // Se não há posts suficientes, inserir no final
            container.appendChild(anuncioElement);
        }
    });
}

// Função para carregar anúncios via AJAX
function carregarAnuncios(localizacao, container, callback) {
    const postId = document.querySelector('meta[name="post-id"]')?.content || 0;
    
    // Usar URL relativa correta
    const apiUrl = `/api/get-anuncios.php?localizacao=${localizacao}&post_id=${postId}`;
    
    console.log('=== DEBUG ANÚNCIOS ===');
    console.log('Carregando anúncios para:', localizacao);
    console.log('URL:', apiUrl);
    console.log('Container:', container);
    console.log('Post ID:', postId);
    
    fetch(apiUrl)
        .then(response => {
            console.log('Status da resposta:', response.status);
            console.log('Headers da resposta:', response.headers);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text(); // Primeiro pegar como texto para debug
        })
        .then(text => {
            console.log('Resposta da API (texto):', text);
            console.log('Tamanho da resposta:', text.length);
            try {
                const data = JSON.parse(text);
                console.log('Dados parseados:', data);
                if (data.success && data.anuncios) {
                    console.log('Anúncios encontrados:', data.anuncios.length);
                    callback(data.anuncios, container);
                } else {
                    console.log('Nenhum anúncio encontrado para:', localizacao);
                    console.log('Resposta da API:', data);
                }
            } catch (e) {
                console.error('Erro ao fazer parse do JSON:', e);
                console.log('Texto recebido:', text);
                throw e;
            }
        })
        .catch(error => {
            console.error('Erro ao carregar anúncios:', error);
            console.log('URL tentada:', apiUrl);
        });
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado, iniciando carregamento de anúncios...');
    
    // Carregar anúncios da sidebar - procurar em todas as seções
    const sidebarSections = document.querySelectorAll('.sidebar .card-body .list-unstyled');
    if (sidebarSections.length > 0) {
        console.log('Seções da sidebar encontradas:', sidebarSections.length);
        // Usar a primeira seção (Mais Recentes) para inserir anúncios
        carregarAnuncios('sidebar', sidebarSections[0], inserirAnunciosSidebar);
    } else {
        console.log('Seções da sidebar NÃO encontradas');
    }
    
    // Carregar anúncios do conteúdo principal - procurar pela div que contém os posts
    const conteudoContainer = document.querySelector('.col-lg-8');
    if (conteudoContainer) {
        console.log('Container do conteúdo encontrado');
        carregarAnuncios('conteudo', conteudoContainer, inserirAnunciosConteudo);
    } else {
        console.log('Container do conteúdo NÃO encontrado');
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
</style>
`;

// Inserir CSS no head
document.head.insertAdjacentHTML('beforeend', anunciosCSS); 